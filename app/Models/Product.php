<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'price_on_request',
        'stock',
        'image',
        'badge',
        'is_active',
        'featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_on_request' => 'boolean',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function hasVariants(): bool
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->isNotEmpty();
        }

        return $this->variants()->exists();
    }

    /**
     * The lowest effective price across the product (used for "from ₦X" labels).
     */
    public function fromPrice(): ?float
    {
        $prices = $this->activeVariants()->pluck('price')->filter(fn ($p) => $p !== null)->map(fn ($p) => (float) $p);

        if ($prices->isNotEmpty()) {
            return (float) $prices->min();
        }

        return $this->price !== null ? (float) $this->price : null;
    }

    /**
     * Whether the product can be purchased online right now.
     */
    public function isPurchasable(): bool
    {
        if ($this->hasVariants()) {
            return $this->activeVariants()->where('stock', '>', 0)->exists();
        }

        return $this->is_active
            && ! $this->price_on_request
            && $this->price !== null
            && $this->stock > 0;
    }
}
