<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'discount_amount',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'discount_amount' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Scope to active vouchers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to available vouchers (active, valid date, quota available).
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    /**
     * Check if voucher is currently valid for use.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if voucher can be applied to a given subtotal.
     */
    public function canApplyTo(float $subtotal): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        if ($subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->canApplyTo($subtotal)) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discount = $subtotal * ((float) $this->discount_amount / 100);
            if ($this->max_discount !== null && $discount > (float) $this->max_discount) {
                $discount = (float) $this->max_discount;
            }

            return min($subtotal, $discount);
        }

        // Fixed nominal discount
        return min($subtotal, (float) $this->discount_amount);
    }

    /**
     * Format discount label for UI.
     */
    public function getFormattedDiscountAttribute(): string
    {
        if ($this->type === 'percentage') {
            return round((float) $this->discount_amount).'%';
        }

        return 'Rp '.number_format((float) $this->discount_amount, 0, ',', '.');
    }
}
