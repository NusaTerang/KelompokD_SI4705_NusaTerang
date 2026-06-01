<?php

namespace App\Observers;

use App\Models\Proyek;

class ProyekObserver
{
    public function updated(Proyek $proyek): void
    {
        if ($proyek->isDirty('dana_terkumpul')) {
            $proyek->checkAndActivateInstalasi();
        }
    }
}
