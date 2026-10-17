<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'product_type',
        'target_user_id',
        'target_zone_id',
        'target_nickname',
        'subtotal',
        'voucher_code',
        'discount_amount',
        'admin_fee',
        'total_amount',
        'status',
        'notes',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'product_type' => ProductType::class,
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'admin_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * User who placed the order (null for guests).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order items list.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Payment record.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Generate unique order number: INV-YYYYMMDD-XXXXX
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'INV-'.date('Ymd');
        $random = strtoupper(Str::random(5));

        while (self::where('order_number', "{$prefix}-{$random}")->exists()) {
            $random = strtoupper(Str::random(5));
        }

        return "{$prefix}-{$random}";
    }

    /**
     * Get payment method code from associated payment record.
     */
    public function getPaymentMethodAttribute(): ?string
    {
        return $this->payment?->payment_method;
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->status, [OrderStatus::Processing, OrderStatus::Completed], true);
    }

    /**
     * Check if game order.
     */
    public function isGame(): bool
    {
        return $this->product_type === ProductType::Game;
    }

    /**
     * Check if account order.
     */
    public function isAccount(): bool
    {
        return $this->product_type === ProductType::Account;
    }

    /**
     * Check if game account order.
     */
    public function isGameAccount(): bool
    {
        return $this->product_type === ProductType::GameAccount;
    }

    /**
     * Check if joki rank order.
     */
    public function isJoki(): bool
    {
        return $this->product_type === ProductType::Joki;
    }

    /**
     * Scope orders by status.
     */
    public function scopeStatus(Builder $query, OrderStatus|string $status): Builder
    {
        $value = $status instanceof OrderStatus ? $status->value : $status;

        return $query->where('status', $value);
    }
}
