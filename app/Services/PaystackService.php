<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class PaystackService
{
    private ?string $secret;

    private string $baseUrl;

    public function __construct(?string $secret = null, ?string $baseUrl = null)
    {
        $this->secret = $secret ?? config('paystack.secret');
        $this->baseUrl = rtrim($baseUrl ?? config('paystack.base_url', 'https://api.paystack.co'), '/');
    }

    public function enabled(): bool
    {
        return ! empty($this->secret);
    }

    /**
     * Initialise a transaction and return Paystack's response payload.
     *
     * @return array<string, mixed>
     */
    public function initialize(Order $order, string $email, string $reference): array
    {
        return Http::withToken($this->secret)
            ->acceptJson()
            ->post($this->baseUrl.'/transaction/initialize', [
                'email' => $email,
                'amount' => $this->toSubunit((float) $order->total),
                'currency' => $order->currency ?? 'NGN',
                'reference' => $reference,
                'callback_url' => config('paystack.callback_url'),
                'metadata' => [
                    'order_id' => $order->getKey(),
                    'order_number' => $order->order_number,
                ],
            ])->json() ?? [];
    }

    /**
     * Verify a transaction by reference and return Paystack's response payload.
     *
     * @return array<string, mixed>
     */
    public function verify(string $reference): array
    {
        return Http::withToken($this->secret)
            ->acceptJson()
            ->get($this->baseUrl.'/transaction/verify/'.urlencode($reference))
            ->json() ?? [];
    }

    /**
     * Validate a webhook payload signature (HMAC SHA-512 of the raw body).
     */
    public function verifySignature(string $body, ?string $signature): bool
    {
        if (empty($signature) || empty($this->secret)) {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $body, $this->secret), $signature);
    }

    /**
     * Convert a naira amount to Paystack's lowest currency unit (kobo).
     */
    public function toSubunit(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
