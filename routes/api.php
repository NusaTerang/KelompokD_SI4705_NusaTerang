<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PenyediaController;
use App\Http\Controllers\Api\ProyekController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Proyek Funding Routes
    Route::get('/proyek/{id}/funding', [ProyekController::class, 'funding']);
    
    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Penyedia Energi Routes
        Route::get('/penyedia', [PenyediaController::class, 'index']);
        Route::get('/penyedia/{id}', [PenyediaController::class, 'show']);
        Route::post('/admin/penyedia', [PenyediaController::class, 'store']);
        Route::post('/proyek/rekomendasi-penyedia', [PenyediaController::class, 'recommendations']);

        // Proyek Routes
        Route::get('/proyek', [ProyekController::class, 'index']);
        Route::get('/proyek/{id}', [ProyekController::class, 'show']);
        
        // Proyek Wizard Routes
        Route::post('/admin/proyek', [ProyekController::class, 'createStep1']);
        Route::put('/admin/proyek/{id}/penyedia', [ProyekController::class, 'saveStep2']);
        Route::post('/admin/proyek/{id}/submit', [ProyekController::class, 'submit']);

    });
});
