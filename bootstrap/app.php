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
        $middleware->redirectGuestsTo(fn () => null);
        $middleware->alias([
            'penyedia' => \App\Http\Middleware\EnsurePenyedia::class,
<<<<<<< HEAD
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
        
        // Midtrans webhook tidak bisa kirim CSRF token, jadi harus diexclude
=======
        ]);
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd
        $middleware->validateCsrfTokens(except: [
            'payments/midtrans-notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
