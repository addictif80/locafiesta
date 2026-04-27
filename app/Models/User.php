<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'first_name', 'last_name', 'birth_date', 'email', 'password',
        'phone', 'address', 'postal_code', 'city', 'role',
        'is_blacklisted', 'blacklist_reason',
        'rgpd_consent', 'rgpd_consent_at', 'deletion_requested_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'rgpd_consent_at' => 'datetime',
            'deletion_requested_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_blacklisted' => 'boolean',
            'rgpd_consent' => 'boolean',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isAgent(): bool { return in_array($this->role, ['admin', 'agent']); }
    public function isClient(): bool { return $this->role === 'client'; }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function reservations() { return $this->hasMany(Reservation::class, 'client_id'); }
    public function invoices() { return $this->hasMany(Invoice::class, 'client_id'); }
}
