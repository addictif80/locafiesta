<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageScaleItem extends Model
{
    protected $fillable = ['equipment_id', 'label', 'description', 'amount'];
    protected $casts = ['amount' => 'decimal:2'];

    public function equipment() { return $this->belongsTo(Equipment::class); }
}
