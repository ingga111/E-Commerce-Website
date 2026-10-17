<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'order_item_id',
        'credentials',
        'notes',
        'is_used',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'assigned_at' => 'datetime',
        ];
    }

    /**
     * Associated product variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Associated order item when sold.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    /**
     * Scope for unused / available stock.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_used', false);
    }
}
