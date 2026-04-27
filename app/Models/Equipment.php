<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'reference', 'description', 'daily_rate', 'deposit_amount', 'is_active',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function photos() { return $this->hasMany(EquipmentPhoto::class); }
    public function primaryPhoto() { return $this->hasOne(EquipmentPhoto::class)->where('is_primary', true); }
    public function checklistItems() { return $this->hasMany(ChecklistItem::class)->orderBy('order'); }
    public function damageScaleItems() { return $this->hasMany(DamageScaleItem::class); }
    public function reservationItems() { return $this->hasMany(ReservationItem::class); }

    public function isAvailableBetween(\Carbon\Carbon $start, \Carbon\Carbon $end, ?int $excludeReservationId = null): bool
    {
        $query = $this->reservationItems()
            ->whereHas('reservation', function ($q) use ($start, $end) {
                $q->whereNotIn('status', ['cancelled', 'cancelled_no_refund'])
                  ->where(function ($q2) use ($start, $end) {
                      $q2->whereBetween('start_date', [$start, $end])
                         ->orWhereBetween('end_date', [$start, $end])
                         ->orWhere(function ($q3) use ($start, $end) {
                             $q3->where('start_date', '<=', $start)->where('end_date', '>=', $end);
                         });
                  });
            });

        if ($excludeReservationId) {
            $query->where('reservation_id', '!=', $excludeReservationId);
        }

        return $query->count() === 0;
    }
}
