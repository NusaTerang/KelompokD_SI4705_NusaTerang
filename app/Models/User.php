<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_donatur';
    public const UPDATED_AT = null;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_telepon',
        'role',
        'penyedia_id',
        'saldo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function donasi(): HasMany
    {
        return $this->hasMany(Donasi::class, 'id_donatur', 'id_donatur');
    }

    public function saldoMutasi(): HasMany
    {
        return $this->hasMany(SaldoMutasi::class, 'id_donatur', 'id_donatur');
    }

    public function getNameAttribute(): ?string
    {
        return $this->nama;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'saldo' => 'float',
        ];
    }

    public function penyedia()
    {
        return $this->belongsTo(PenyediaEnergi::class, 'penyedia_id');
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a penyedia.
     */
    public function isPenyedia(): bool
    {
        return $this->hasRole('penyedia');
    }

    /**
     * Check if the user is a donatur.
     */
    public function isDonatur(): bool
    {
        return $this->hasRole('donatur');
    }
}