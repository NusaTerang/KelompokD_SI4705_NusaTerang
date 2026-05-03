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

    protected $table = 'donatur';
    protected $primaryKey = 'id_donatur';
    public const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'penyedia_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function donasi(): HasMany
    {
        return $this->hasMany(Donasi::class, 'id_donatur', 'id_donatur');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function penyedia()
    {
        return $this->belongsTo(PenyediaEnergi::class, 'penyedia_id');
    }
}