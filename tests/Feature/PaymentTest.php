<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private string $secret = 'sk_test_emerald';

    private function fakeInitialize(): void
    {
        Http::fake([
            '*transaction/initialize*' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/abc123',
                    'reference' => 'REF',
                ],
            ]),
        ]);
    }

    public function test_guests_cannot_start_payment(): void
    {
        $order = Order::factory()->create();

        $this->get(route('payments.pay', $order))->assertRedirect(route('login'));
    }

    public function test_customers_cannot_pay_another_customers_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('payments.pay', $order))
            ->assertForbidden();
    }

    public function test_online_payment_is_disabled_without_a_secret_key(): void
    {
        config(['paystack.secret' => null]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['total' => 100]);

        $this->actingAs($user)
            ->get(route('payments.pay', $order))
            ->assertRedirect(route('checkout.success', $order))
            ->assertSessionHas('error');
    }

    public function test_paying_redirects_to_paystack_and_records_a_payment(): void
    {
        config(['paystack.secret' => $this->secret]);
        $this->fakeInitialize();

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['total' => 150, 'currency' => 'NGN']);

        $this->actingAs($user)
            ->get(route('payments.pay', $order))
            ->assertRedirect('https://checkout.paystack.com/abc123');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'pending',
            'amount' => 150,
        ]);

        Http::assertSent(fn ($request) => $request['amount'] === 15000 && $request['currency'] === 'NGN');
    }

    public function test_callback_verification_marks_the_order_paid(): void
    {
        config(['paystack.secret' => $this->secret]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['total' => 150, 'currency' => 'NGN']);
        $payment = Payment::factory()->for($order)->create([
            'reference' => 'REF-PAID',
            'amount' => 150,
            'currency' => 'NGN',
            'status' => 'pending',
        ]);

        Http::fake([
            '*transaction/verify*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success', 'amount' => 15000, 'currency' => 'NGN', 'reference' => 'REF-PAID'],
            ]),
        ]);

        $this->actingAs($user)
            ->get(route('payments.callback', ['reference' => 'REF-PAID']))
            ->assertRedirect(route('checkout.success', $order));

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('success', $payment->fresh()->status);
    }

    public function test_callback_does_not_mark_paid_when_amount_differs(): void
    {
        config(['paystack.secret' => $this->secret]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['total' => 150, 'currency' => 'NGN']);
        $payment = Payment::factory()->for($order)->create([
            'reference' => 'REF-WRONG',
            'amount' => 150,
            'currency' => 'NGN',
        ]);

        Http::fake([
            '*transaction/verify*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success', 'amount' => 1, 'currency' => 'NGN'],
            ]),
        ]);

        $this->actingAs($user)->get(route('payments.callback', ['reference' => 'REF-WRONG']));

        $this->assertSame('unpaid', $order->fresh()->payment_status);
        $this->assertNotSame('success', $payment->fresh()->status);
    }

    public function test_webhook_with_valid_signature_marks_paid_and_is_idempotent(): void
    {
        config(['paystack.secret' => $this->secret]);

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create(['total' => 150, 'currency' => 'NGN']);
        $payment = Payment::factory()->for($order)->create([
            'reference' => 'REF-WEBHOOK',
            'amount' => 150,
            'currency' => 'NGN',
        ]);

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => 'REF-WEBHOOK', 'status' => 'success', 'amount' => 15000, 'currency' => 'NGN'],
        ]);

        $signature = hash_hmac('sha512', $payload, $this->secret);

        $send = fn () => $this->call('POST', '/payments/paystack/webhook', [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], $payload);

        $send()->assertOk();
        $send()->assertOk();

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('success', $payment->fresh()->status);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_webhook_rejects_an_invalid_signature(): void
    {
        config(['paystack.secret' => $this->secret]);

        $order = Order::factory()->create(['total' => 150, 'currency' => 'NGN']);
        Payment::factory()->for($order)->create(['reference' => 'REF-BAD', 'amount' => 150, 'currency' => 'NGN']);

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => 'REF-BAD', 'status' => 'success', 'amount' => 15000, 'currency' => 'NGN'],
        ]);

        $this->call('POST', '/payments/paystack/webhook', [], [], [], [
            'HTTP_X_PAYSTACK_SIGNATURE' => 'not-a-valid-signature',
            'CONTENT_TYPE' => 'application/json',
        ], $payload)->assertStatus(401);

        $this->assertSame('unpaid', $order->fresh()->payment_status);
    }
}
