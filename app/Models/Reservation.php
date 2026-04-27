<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference', 'client_id', 'start_date', 'start_time', 'end_date', 'end_time',
        'status', 'subtotal', 'discount_amount', 'promo_code_id', 'total_amount',
        'deposit_percentage', 'deposit_amount', 'balance_amount',
        'stripe_payment_intent_id', 'stripe_charge_id', 'deposit_paid_at',
        'use_different_address', 'use_address', 'use_postal_code', 'use_city',
        'cancelled_at', 'cancellation_reason', 'deposit_refunded', 'deposit_refunded_at',
        'admin_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deposit_paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'deposit_refunded_at' => 'datetime',
        'use_different_address' => 'boolean',
        'deposit_refunded' => 'boolean',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Reservation $reservation) {
            if (empty($reservation->reference)) {
                $reservation->reference = 'RES-' . strtoupper(uniqid());
            }
        });
    }

    public function client() { return $this->belongsTo(User::class, 'client_id'); }
    public function items() { return $this->hasMany(ReservationItem::class); }
    public function promoCode() { return $this->belongsTo(PromoCode::class); }
    public function inspections() { return $this->hasMany(Inspection::class); }
    public function departureInspection() { return $this->hasOne(Inspection::class)->where('type', 'departure'); }
    public function returnInspection() { return $this->hasOne(Inspection::class)->where('type', 'return'); }
    public function securityDeposits() { return $this->hasMany(SecurityDeposit::class); }
    public function departureDeposit() { return $this->hasOne(SecurityDeposit::class)->where('phase', 'departure'); }
    public function returnDeposit() { return $this->hasOne(SecurityDeposit::class)->where('phase', 'return'); }
    public function damageCharges() { return $this->hasMany(DamageCharge::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }

    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function canBeCancelledWithRefund(): bool
    {
        return Carbon::now()->lt($this->start_date->setTimeFromTimeString($this->start_time)->subHours(48));
    }

    public function isPending(): bool { return $this->status === 'pending_payment'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isCancelled(): bool { return in_array($this->status, ['cancelled', 'cancelled_no_refund']); }

    public function getTotalDamageChargesAttribute(): float
    {
        return (float) $this->damageCharges()->sum('amount');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'En attente de paiement',
            'confirmed' => 'Confirmée',
            'in_progress' => 'En cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée (remboursée)',
            'cancelled_no_refund' => 'Annulée (non remboursée)',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending_payment' => 'yellow',
            'confirmed' => 'blue',
            'in_progress' => 'green',
            'completed' => 'gray',
            'cancelled' => 'red',
            'cancelled_no_refund' => 'red',
            default => 'gray',
        };
    }
}
