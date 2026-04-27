<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedDate extends Model
{
    protected $fillable = ['start_date', 'end_date', 'reason', 'type', 'created_by'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'holiday' => 'Congés',
            'maintenance' => 'Maintenance',
            'closure' => 'Fermeture',
            default => 'Autre',
        };
    }
}
