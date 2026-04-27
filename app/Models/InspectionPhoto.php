<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionPhoto extends Model
{
    protected $fillable = ['inspection_id', 'inspection_item_id', 'path', 'caption'];

    public function inspection() { return $this->belongsTo(Inspection::class); }
    public function inspectionItem() { return $this->belongsTo(InspectionItem::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
