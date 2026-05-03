<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes are grouped by middleware:
| - 'guest'  → only accessible when NOT logged in (login, register)
| - 'auth'   → only accessible when logged in (dashboard, logout)
|
*/

// ─── Landing Page ────────────────────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome');
});

// ─── Guest Routes (Login & Register) ────────────────────────────────────────

Route::middleware('guest')->group(function () {

    // Register
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// ─── Authenticated Routes ───────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Generic dashboard fallback
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return match ($user->role) {
            'admin'    => redirect('/admin/dashboard'),
            'penyedia' => redirect('/penyedia/dashboard'),
            'donatur'  => redirect('/donatur/dashboard'),
            default    => redirect('/'),
        };
    })->name('dashboard');

    // ── Admin Routes ─────────────────────────────────────────────────────
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    // ── Penyedia Routes ──────────────────────────────────────────────────
    Route::prefix('penyedia')->group(function () {
        Route::get('/dashboard', function () {
            return view('penyedia.dashboard');
        })->name('penyedia.dashboard');
    });

    // ── Donatur Routes ───────────────────────────────────────────────────
    Route::prefix('donatur')->group(function () {
        Route::get('/dashboard', function () {
            return view('donatur.dashboard');
        })->name('donatur.dashboard');
    });

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
