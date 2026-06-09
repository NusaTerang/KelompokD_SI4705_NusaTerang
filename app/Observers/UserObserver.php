<?php

namespace App\Observers;

use App\Models\User;
use App\Models\SaldoDonatur;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Otomatis membuat entry saldo_donatur dengan saldo awal 0
        SaldoDonatur::firstOrCreate(
            ['id_donatur' => $user->id_donatur],
            ['saldo' => 0.00]
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
