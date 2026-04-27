<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $fillable = [
        'reservation_id', 'type', 'admin_id', 'general_notes', 'pdf_message',
        'client_signature_path', 'admin_signature_path', 'signed_at',
    ];

    protected $casts = ['signed_at' => 'datetime'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
    public function items() { return $this->hasMany(InspectionItem::class); }
    public function photos() { return $this->hasMany(InspectionPhoto::class); }

    public function isDeparture(): bool { return $this->type === 'departure'; }
    public function isReturn(): bool { return $this->type === 'return'; }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'departure' ? 'État des lieux de départ' : 'État des lieux de retour';
    }

    public function getItemsWithDifferencesAttribute()
    {
        if (!$this->isReturn()) return collect();
        $departure = $this->reservation->departureInspection;
        if (!$departure) return collect();

        return $this->items->filter(function ($item) use ($departure) {
            $depItem = $departure->items->where('checklist_item_id', $item->checklist_item_id)->first();
            return $depItem && $depItem->condition !== $item->condition;
        });
    }
}
