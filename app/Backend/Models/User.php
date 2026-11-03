<?php

namespace App\Backend\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Model berada di namespace App\Backend\Models, sehingga resolusi
     * factory otomatis perlu diarahkan ke Database\Factories\UserFactory.
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'eselon',
        'role', // operator, checker, spv1, spv2
        'unit_kerja',
        'kode_cabang',
        'delegate_to_user_id',
        'delegate_start',
        'delegate_end',
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
            'delegate_start' => 'datetime',
            'delegate_end' => 'datetime',
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

    public function delegateTo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'delegate_to_user_id');
    }

    public function isDelegateActive(): bool
    {
        return $this->delegate_to_user_id !== null
            && $this->delegate_start !== null
            && $this->delegate_end !== null
            && now()->between($this->delegate_start, $this->delegate_end);
    }
}
