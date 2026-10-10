<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct(private PaystackService $paystack) {}

    /**
     * Start a Paystack transaction for an unpaid order.
     */
    public function pay(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->isPaid()) {
            return redirect()->route('checkout.success', $order)->with('success', 'This order has already been paid.');
        }

        if (! $this->paystack->enabled()) {
            return redirect()->route('checkout.success', $order)
                ->with('error', 'Online payment is not configured yet. Please confirm your order on WhatsApp.');
        }

        $reference = 'EH-'.$order->order_number.'-'.strtoupper(Str::random(4));

        $payment = Payment::create([
            'order_id' => $order->getKey(),
            'provider' => 'paystack',
            'reference' => $reference,
            'amount' => (float) $order->total,
            'currency' => $order->currency ?? 'NGN',
            'status' => 'pending',
        ]);

        $result = $this->paystack->initialize($order, $order->email ?: auth()->user()->email, $reference);
        $url = $result['data']['authorization_url'] ?? null;

        if (! ($result['status'] ?? false) || ! $url) {
            $payment->markFailed($result);

            return redirect()->route('checkout.success', $order)
                ->with('error', 'We could not start the payment. Please try again or order on WhatsApp.');
        }

        $payment->update(['payload' => $result]);

        return redirect()->away($url);
    }

    /**
     * Handle the customer returning from Paystack. The result is verified
     * server-side; a redirect alone never marks an order paid.
     */
    public function callback(Request $request)
    {
        $reference = (string) $request->query('reference', '');

        if ($reference === '') {
            return redirect()->route('account')->with('error', 'Missing payment reference.');
        }

        $payment = Payment::where('reference', $reference)->first();

        if (! $payment) {
            return redirect()->route('account')->with('error', 'Unknown payment reference.');
        }

        $order = $payment->order;
        abort_unless($order && $order->user_id === auth()->id(), 403);

        $this->settle($payment, $this->paystack->verify($reference));

        $payment->refresh();

        return redirect()->route('checkout.success', $order)->with(
            $payment->isSuccessful() ? 'success' : 'error',
            $payment->isSuccessful() ? 'Payment received. Thank you!' : 'Payment was not completed.',
        );
    }

    /**
     * Paystack webhook. Authenticated by signature (not CSRF). Idempotent.
     */
    public function webhook(Request $request)
    {
        $body = $request->getContent();

        if (! $this->paystack->verifySignature($body, $request->header('x-paystack-signature'))) {
            return response()->json(['status' => 'invalid signature'], 401);
        }

        $reference = $request->input('data.reference');

        if ($request->input('event') === 'charge.success' && $reference) {
            $payment = Payment::where('reference', $reference)->first();

            if ($payment) {
                $this->settle($payment, ['status' => true, 'data' => $request->input('data', [])]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Verify a payment result against its order and settle idempotently.
     *
     * @param  array<string, mixed>  $result
     */
    private function settle(Payment $payment, array $result): void
    {
        DB::transaction(function () use ($payment, $result) {
            $locked = Payment::query()->whereKey($payment->getKey())->lockForUpdate()->first();

            if (! $locked || $locked->isSuccessful()) {
                return;
            }

            $data = $result['data'] ?? [];
            $successful = ($result['status'] ?? false)
                && (($data['status'] ?? null) === 'success')
                && isset($data['amount'])
                && (int) $data['amount'] === (int) round((float) $locked->amount * 100)
                && (($data['currency'] ?? $locked->currency) === $locked->currency);

            if ($successful) {
                $locked->markSuccessful($result);
            } else {
                $locked->markFailed($result);
            }
        });
    }
}
