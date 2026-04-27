<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionItem extends Model
{
    protected $fillable = ['inspection_id', 'checklist_item_id', 'condition', 'notes'];

    public function inspection() { return $this->belongsTo(Inspection::class); }
    public function checklistItem() { return $this->belongsTo(ChecklistItem::class); }
    public function photos() { return $this->hasMany(InspectionPhoto::class); }
    public function damageCharges() { return $this->hasMany(DamageCharge::class); }

    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'good' => 'Bon état',
            'worn' => 'Usure normale',
            'damaged' => 'Dégradé',
            'missing' => 'Manquant',
            default => $this->condition,
        };
    }

    public function getConditionColorAttribute(): string
    {
        return match($this->condition) {
            'good' => 'green',
            'worn' => 'yellow',
            'damaged' => 'red',
            'missing' => 'gray',
            default => 'gray',
        };
    }
}
