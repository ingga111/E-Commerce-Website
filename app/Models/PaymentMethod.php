<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'group',
        'account_number',
        'account_holder',
        'qr_image',
        'fee_flat',
        'fee_percent',
        'instructions',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fee_flat' => 'decimal:2',
            'fee_percent' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope for active payment methods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Calculate total fee for given amount.
     */
    public function calculateFee(float $amount): float
    {
        $percentFee = ($amount * (float) $this->fee_percent) / 100;

        return (float) $this->fee_flat + $percentFee;
    }
}
