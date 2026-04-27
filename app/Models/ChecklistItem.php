<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    protected $fillable = ['equipment_id', 'label', 'description', 'order'];

    public function equipment() { return $this->belongsTo(Equipment::class); }
    public function inspectionItems() { return $this->hasMany(InspectionItem::class); }
}
