<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_donatur';
    public $incrementing = true;
    protected $keyType = 'int';
    public const UPDATED_AT = null;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_telepon',
        'role',
        'status',
        'last_login',
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

    // ─── Saldo (PBI-31) ──────────────────────────────────────────────

    public function saldoDonatur(): HasOne
    {
        return $this->hasOne(SaldoDonatur::class, 'id_donatur', 'id_donatur');
    }

    public function mutasiSaldo(): HasMany
    {
        return $this->hasMany(MutasiSaldo::class, 'id_donatur', 'id_donatur');
    }

    public function getSaldoAttribute(): float
    {
        return (float) ($this->saldoDonatur?->saldo ?? 0);
    }

    public function tambahSaldo(float $nominal, ?string $keterangan = null): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($nominal, $keterangan) {
            $saldoRecord = SaldoDonatur::firstOrCreate(
                ['id_donatur' => $this->id_donatur],
                ['saldo' => 0]
            );
            $saldoRecord->increment('saldo', $nominal);

            MutasiSaldo::create([
                'id_donatur' => $this->id_donatur,
                'nominal' => $nominal,
                'tipe' => 'masuk',
                'keterangan' => $keterangan,
            ]);
        });
    }

    public function kurangiSaldo(float $nominal, ?string $keterangan = null): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($nominal, $keterangan) {
            $saldoRecord = SaldoDonatur::firstOrCreate(
                ['id_donatur' => $this->id_donatur],
                ['saldo' => 0]
            );
            $saldoRecord->decrement('saldo', $nominal);

            MutasiSaldo::create([
                'id_donatur' => $this->id_donatur,
                'nominal' => $nominal,
                'tipe' => 'keluar',
                'keterangan' => $keterangan,
            ]);
        });
    }
}