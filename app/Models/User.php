<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'eselon',
        'role', // operator, checker, spv1, spv2
        'unit_kerja',
        'kode_cabang',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function paymentDocuments(): HasMany
    {
        return $this->hasMany(PaymentDocument::class, 'created_by');
    }

    public function isOperator(): bool
    {
        return in_array($this->role, ['operator', 'operator_kp_cabang']);
    }

    public function isChecker(): bool
    {
        return $this->role === 'checker';
    }

    public function isSpv1(): bool
    {
        return $this->role === 'spv1' || $this->role === 'supervisor_eksternal';
    }

    public function isSpv2(): bool
    {
        return in_array($this->role, ['spv2', 'supervisor_psv', 'supervisor_txb']);
    }
}
