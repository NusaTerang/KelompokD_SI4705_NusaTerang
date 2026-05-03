<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\PenyediaController;

Route::get('/', function () {
    $projects = \App\Models\Proyek::with(['desa', 'fotos'])->paginate(6);
    return view('welcome', compact('projects'));
});

Route::middleware('guest')->group(function () {
    // Register
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

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

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });

    Route::prefix('penyedia')->group(function () {
        Route::get('/dashboard', function () {
            return view('penyedia.dashboard');
        })->name('penyedia.dashboard');
    });

    Route::prefix('donatur')->group(function () {
        Route::get('/dashboard', function () {
            return view('donatur.dashboard');
        })->name('donatur.dashboard');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
});

Route::prefix('desa')->name('desa.')->group(function () {
    Route::get('input', [DesaController::class, 'create'])->name('input');
    Route::post('/', [DesaController::class, 'store'])->name('store');
    Route::get('kelola', [DesaController::class, 'kelola'])->name('kelola');
    Route::get('daftar', [DesaController::class, 'index'])->name('daftar');
});

Route::get('/penyedia/daftar', [PenyediaController::class, 'index'])->name('penyedia.daftar');

Route::get('/profil-preview', [ProfileController::class, 'edit']);

Route::post('/assign', [PenugasanController::class, 'assign']);
Route::post('/respon/{id}', [PenugasanController::class, 'respon']);
Route::post('/detail', [PenugasanController::class, 'isiDetail']);

Route::prefix('proyek')->name('proyek.')->group(function () {
    Route::get('/buat', [ProyekController::class, 'create'])->name('create');
    Route::post('/buat', [ProyekController::class, 'saveStep1'])->name('save.step1');
    
    Route::get('/{id}/pilih-penyedia', [ProyekController::class, 'step2'])->name('step2');
    Route::post('/{id}/pilih-penyedia', [ProyekController::class, 'saveStep2'])->name('save.step2');
    
    Route::get('/{id}/review', [ProyekController::class, 'review'])->name('review');
    Route::post('/{id}/kirim', [ProyekController::class, 'kirimKePenyedia'])->name('kirim');
    
    Route::get('/{id}', [ProyekController::class, 'show'])->name('show');
});

Route::get('/api/penyedia/rekomendasi', [PenyediaController::class, 'getRekomendasi'])->name('api.penyedia.rekomendasi');
