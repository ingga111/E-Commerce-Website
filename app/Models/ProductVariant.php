<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'cost_price',
        'price',
        'original_price',
        'stock_type',
        'stock',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'stock' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Parent product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Account credentials in stock (for account products).
     */
    public function accountStocks(): HasMany
    {
        return $this->hasMany(AccountStock::class);
    }

    /**
     * Available (unused) account credentials.
     */
    public function availableStocks(): HasMany
    {
        return $this->accountStocks()->where('is_used', false);
    }

    /**
     * Check current stock availability.
     */
    public function getStockCountAttribute(): int|string
    {
        if ($this->stock_type === 'unlimited') {
            return 'Tersedia';
        }

        return max(0, (int) $this->stock);
    }

    /**
     * Check if variant has available stock.
     */
    public function hasStock(int $quantity = 1): bool
    {
        if ($this->stock_type === 'unlimited') {
            return true;
        }

        return (int) $this->stock >= $quantity;
    }

    /**
     * Scope for active variants.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
