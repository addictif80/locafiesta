<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageCharge extends Model
{
    protected $fillable = [
        'reservation_id', 'inspection_item_id', 'damage_scale_item_id',
        'label', 'description', 'amount',
    ];

    protected $casts = ['amount' => 'decimal:2'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function inspectionItem() { return $this->belongsTo(InspectionItem::class); }
    public function damageScaleItem() { return $this->belongsTo(DamageScaleItem::class); }
}
