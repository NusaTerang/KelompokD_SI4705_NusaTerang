<?php

namespace App\Policies;

use App\Models\Proyek;
use App\Models\User;

class ProyekPolicy
{
    /**
     * Determine whether the user can view the project.
     *
     * Halaman detail proyek dapat diakses user login.
     */
    public function view(User $user, Proyek $proyek): bool
    {
        return true; // Any authenticated user can view the project detail page
    }

    /**
     * Determine whether the user can monitor the project funding data.
     *
     * Data monitoring admin hanya role admin.
     */
    public function monitor(User $user, Proyek $proyek): bool
    {
        return $user->isAdmin();
    }
}
