<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Proyek;
use App\Models\User;
use App\Observers\ProyekObserver;
use App\Observers\UserObserver;

use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(\App\Models\Proyek::class, \App\Policies\ProyekPolicy::class);

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\DonationSucceeded::class,
            \App\Listeners\UpdateProjectFunding::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\PaymentConfirmed::class,
            \App\Listeners\UpdateProjectFunding::class
        );

        Proyek::observe(ProyekObserver::class);
        User::observe(UserObserver::class);
    }
}
