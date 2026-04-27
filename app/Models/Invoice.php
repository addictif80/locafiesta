<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number', 'reservation_id', 'client_id', 'type',
        'amount', 'status', 'pdf_path', 'paid_at', 'payment_method', 'notes',
    ];

    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $year = now()->format('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $invoice->invoice_number = 'FAC-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function client() { return $this->belongsTo(User::class, 'client_id'); }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'Acompte',
            'balance' => 'Solde',
            'damage' => 'Dégradations',
            'manual' => 'Manuelle',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'paid' => 'Payée',
            'cancelled' => 'Annulée',
            'refunded' => 'Remboursée',
            default => $this->status,
        };
    }
}
