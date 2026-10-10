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

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Whether the product can be purchased online right now.
     */
    public function isPurchasable(): bool
    {
        return $this->is_active
            && ! $this->price_on_request
            && $this->price !== null
            && $this->stock > 0;
    }
}
