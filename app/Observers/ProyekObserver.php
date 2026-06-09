<?php

namespace App\Observers;

use App\Events\ProyekDibatalkan;
use App\Models\Proyek;

class ProyekObserver
{
    public function updated(Proyek $proyek): void
    {
        if ($proyek->wasChanged('dana_terkumpul')) {
            $proyek->checkAndActivateInstalasi();
        }

        if (
            $proyek->wasChanged('estimasi_selesai')
            || ($proyek->wasChanged('status') && $proyek->status === 'aktif_funding')
        ) {
            $proyek->checkAndExtendIfExpired();
        }

        if ($proyek->wasChanged('status') && $proyek->status === 'refund') {
            event(new ProyekDibatalkan($proyek));
        }
    }
}
