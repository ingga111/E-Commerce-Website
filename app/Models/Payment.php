<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_gateway',
        'payment_method',
        'payment_name',
        'reference',
        'merchant_ref',
        'pay_code',
        'account_number',
        'account_holder',
        'qr_url',
        'checkout_url',
        'payment_proof',
        'payment_proof_uploaded_at',
        'admin_note',
        'amount',
        'fee',
        'total_amount',
        'status',
        'paid_at',
        'expires_at',
        'raw_response',
        'raw_callback',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'payment_proof_uploaded_at' => 'datetime',
            'raw_response' => 'array',
            'raw_callback' => 'array',
        ];
    }

    /**
     * Parent order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Check if payment is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }
}
