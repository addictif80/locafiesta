<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    protected $fillable = ['reservation_id', 'equipment_id', 'daily_rate', 'days_count', 'subtotal'];
    protected $casts = ['daily_rate' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function equipment() { return $this->belongsTo(Equipment::class); }
}
