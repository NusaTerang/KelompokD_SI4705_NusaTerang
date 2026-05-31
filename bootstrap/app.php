<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
        $middleware->redirectGuestsTo(fn () => null);
        $middleware->alias([
            'penyedia' => \App\Http\Middleware\EnsurePenyedia::class,
=======
        // Midtrans webhook tidak bisa kirim CSRF token, jadi harus diexclude
        $middleware->validateCsrfTokens(except: [
            'payments/midtrans-notification',
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
