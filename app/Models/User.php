<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Autentikasi donatur — tabel {@see $table} mengikuti ERD donatur.
 */
#[Fillable([
    'nama',
    'email',
    'password',
    'no_telepon',
])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'donatur';

    protected $primaryKey = 'id_donatur';

    public const UPDATED_AT = null;

    public function donasi(): HasMany
    {
        return $this->hasMany(Donasi::class, 'id_donatur', 'id_donatur');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
