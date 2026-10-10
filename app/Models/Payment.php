<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'reference',
        'amount',
        'currency',
        'status',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payload' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Mark this payment successful and settle the linked order.
     *
     * Idempotent: repeated calls (e.g. duplicate webhooks) are a no-op.
     */
    public function markSuccessful(?array $payload = null): void
    {
        $this->refresh();

        if ($this->isSuccessful()) {
            return;
        }

        $this->update([
            'status' => 'success',
            'payload' => $payload ?? $this->payload,
        ]);

        $this->order?->markPaid($this->reference);
    }

    public function markFailed(?array $payload = null): void
    {
        if ($this->isSuccessful()) {
            return;
        }

        $this->update([
            'status' => 'failed',
            'payload' => $payload ?? $this->payload,
        ]);
    }
}
