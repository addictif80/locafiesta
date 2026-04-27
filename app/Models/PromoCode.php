<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'max_uses', 'used_count',
        'valid_from', 'valid_until', 'is_active', 'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function reservations() { return $this->hasMany(Reservation::class); }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_until && now()->gt($this->valid_until->endOfDay())) return false;
        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * ($this->value / 100), 2);
        }
        return min((float) $this->value, $amount);
    }
}
