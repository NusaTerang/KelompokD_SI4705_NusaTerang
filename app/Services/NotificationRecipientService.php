<?php

namespace App\Services;

use App\Models\Proyek;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationRecipientService
{
    public function admins(): Collection
    {
        return User::where('role', 'admin')->get();
    }

    public function vendorForProject(Proyek $proyek): ?User
    {
        if (! $proyek->penyedia_id) {
            return null;
        }

        return User::where('role', 'penyedia')
            ->where('penyedia_id', $proyek->penyedia_id)
            ->first();
    }

    public function donorsForProject(Proyek $proyek): Collection
    {
        if (! Schema::hasTable('donasi')) {
            return collect();
        }

        $query = DB::table('donasi')
            ->where('id_proyek', $proyek->id)
            ->select('id_donatur')
            ->distinct();

        if (Schema::hasColumn('donasi', 'status')) {
            $query->whereIn('status', ['berhasil', 'paid', 'success', 'sukses']);
        }

        $donorIds = $query->pluck('id_donatur');

        if ($donorIds->isEmpty()) {
            return collect();
        }

        return User::whereIn('id_donatur', $donorIds)
            ->where('role', 'donatur')
            ->get();
    }

    public function adminsAndDonorsForProject(Proyek $proyek): Collection
    {
        return $this->admins()
            ->merge($this->donorsForProject($proyek))
            ->unique(fn (User $user) => $user->getKey())
            ->values();
    }
}
