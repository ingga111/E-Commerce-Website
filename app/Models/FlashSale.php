<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'badge_text',
        'discount_percentage',
        'start_time',
        'end_time',
        'target_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'discount_percentage' => 'integer',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    /**
     * Scope to only active flash sales.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to currently running flash sales (active and not expired).
     */
    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_time')
                    ->orWhere('end_time', '>', now());
            });
    }

    /**
     * Check if currently running.
     */
    public function isRunning(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->end_time && $this->end_time->isPast()) {
            return false;
        }

        return true;
    }
}
