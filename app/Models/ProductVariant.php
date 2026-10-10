<?php

namespace App\Models;

use Database\Factories\ProductVariantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'label',
        'options',
        'price',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The effective unit price: the variant price, or the parent product price.
     */
    public function unitPrice(): ?float
    {
        if ($this->price !== null) {
            return (float) $this->price;
        }

        return $this->product?->price !== null ? (float) $this->product->price : null;
    }

    public function isPurchasable(): bool
    {
        return $this->is_active && $this->stock > 0 && $this->unitPrice() !== null;
    }
}
