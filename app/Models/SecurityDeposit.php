<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityDeposit extends Model
{
    protected $fillable = [
        'reservation_id', 'phase', 'holder_name', 'bank_name', 'bank_address',
        'check_number', 'amount', 'status', 'notes', 'action_at', 'recorded_by',
    ];

    protected $casts = ['amount' => 'decimal:2', 'action_at' => 'datetime'];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'held' => 'Conservé',
            'returned' => 'Restitué',
            'cashed' => 'Encaissé',
            default => $this->status,
        };
    }
}
