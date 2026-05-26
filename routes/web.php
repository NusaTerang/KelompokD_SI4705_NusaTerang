<?php

use Illuminate\Support\Facades\Route;
<<<<<<< Updated upstream
use App\Http\Controllers\PaymentCallbackController;
=======
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\PenyediaController;
use App\Http\Controllers\Admin\PenyediaController as AdminPenyediaController;
use App\Http\Controllers\Vendor\ProyekController as VendorProyekController;
use App\Http\Controllers\PaymentCallbackController; // Midtrans
>>>>>>> Stashed changes
use App\Http\Controllers\OrderController;

// ─── Public ───────────────────────────────────────────────────────────────────

Route::get('/', function () {
    return view('welcome');
});

// ─── Placeholder routes untuk controller yang belum diimplementasi ─────────────
// (Akan diisi oleh tim setelah controller dibuat)

// Proyek & Penyedia — public
Route::get('/proyek/{id}', 'App\Http\Controllers\ProyekController@show')
    ->name('proyek.show');
Route::get('/penyedia/daftar', 'App\Http\Controllers\PenyediaController@index')
    ->name('penyedia.daftar');
Route::get('/penyedia/{id}', 'App\Http\Controllers\PenyediaController@show')
    ->name('penyedia.show');
Route::get('/api/penyedia/rekomendasi', 'App\Http\Controllers\PenyediaController@getRekomendasi')
    ->name('api.penyedia.rekomendasi');

// ─── Auth (guest only) ─────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('register', 'App\Http\Controllers\Auth\RegisteredUserController@create')
        ->name('register');
    Route::post('register', 'App\Http\Controllers\Auth\RegisteredUserController@store');

    Route::get('login', 'App\Http\Controllers\Auth\AuthenticatedSessionController@create')
        ->name('login');
    Route::post('login', 'App\Http\Controllers\Auth\AuthenticatedSessionController@store');

    Route::get('forgot-password', function () { return "Lupa Password (Belum Diimplementasi)"; })
        ->name('password.request');
});

Route::post('/logout', 'App\Http\Controllers\Auth\AuthenticatedSessionController@destroy')
    ->name('logout')
    ->middleware('auth');

// ─── Donasi (Authenticated — role: user biasa / donatur) ──────────────────────

Route::middleware('auth')->prefix('donasi')->name('donasi.')->group(function () {
    Route::get('/',               [OrderController::class, 'create'])->name('create');
    Route::post('/',              [OrderController::class, 'store'])->name('store');
    Route::get('/{order}',        [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/status', [OrderController::class, 'status'])->name('status');
});

// ─── Authenticated ─────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::post('/donasi/{proyek_id}', [OrderController::class, 'store'])->name('donasi.store');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');

    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Redirect berdasarkan role
        return match (property_exists($user, 'role') ? $user->role : null) {
            'admin'    => redirect()->route('desa.daftar'),
            'penyedia' => redirect()->route('vendor.dashboard'),
            default    => redirect()->route('donasi.create'),
        };
    })->name('dashboard');

    Route::get('/profil', 'App\Http\Controllers\ProfileController@edit')->name('profil.edit');
    Route::put('/profil', 'App\Http\Controllers\ProfileController@update')->name('profil.update');
});

// ─── Vendor (Penyedia Energi) ──────────────────────────────────────────────────

Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', function () {
        return view('penyedia.dashboard');
    })->name('dashboard');

    Route::prefix('proyek')->name('proyek.')->group(function () {
        Route::get('/',            'App\Http\Controllers\Vendor\ProyekController@index')->name('index');
        Route::get('/{id}',        'App\Http\Controllers\Vendor\ProyekController@show')->name('show');
        Route::get('/{id}/expiry-decision', 'App\Http\Controllers\Vendor\ProyekController@expiryDecisionShow')->name('expiry-decision.show');
        Route::put('/{id}/detail', 'App\Http\Controllers\Vendor\ProyekController@saveDetail')->name('detail');
        Route::post('/{id}/expiry-decision', 'App\Http\Controllers\Vendor\ProyekController@expiryDecision')->name('expiry-decision');
        Route::post('/{id}/klarifikasi', 'App\Http\Controllers\Vendor\ProyekController@mintaKlarifikasi')->name('klarifikasi');
        Route::post('/{id}/kendala',     'App\Http\Controllers\Vendor\ProyekController@laporkanKendala')->name('kendala');
    });
});

Route::middleware('auth')->get('/penyedia/dashboard', function () {
    return redirect()->route('vendor.dashboard');
})->name('penyedia.dashboard');

// ─── Admin ─────────────────────────────────────────────────────────────────────

Route::middleware('auth')->prefix('admin')->group(function () {

    // Desa management
    Route::prefix('desa')->name('desa.')->group(function () {
        Route::get('input',  'App\Http\Controllers\DesaController@create')->name('input');
        Route::post('/',     'App\Http\Controllers\DesaController@store')->name('store');
        Route::get('kelola', 'App\Http\Controllers\DesaController@kelola')->name('kelola');
        Route::get('daftar', 'App\Http\Controllers\DesaController@index')->name('daftar');
        Route::get('{id}/edit', 'App\Http\Controllers\DesaController@edit')->name('edit');
        Route::put('{id}',      'App\Http\Controllers\DesaController@update')->name('update');
        Route::delete('{id}',   'App\Http\Controllers\DesaController@destroy')->name('destroy');
    });

    // Proyek management
    Route::prefix('proyek')->name('proyek.')->group(function () {
        Route::get('buat',   'App\Http\Controllers\ProyekController@create')->name('create');
        Route::post('buat',  'App\Http\Controllers\ProyekController@saveStep1')->name('save.step1');
        Route::get('kelola', 'App\Http\Controllers\ProyekController@kelola')->name('kelola');
        Route::get('{id}/detail',    'App\Http\Controllers\ProyekController@adminShow')->name('admin.show');
        Route::patch('{id}/publish', 'App\Http\Controllers\ProyekController@publish')->name('publish');
        Route::delete('{id}',        'App\Http\Controllers\ProyekController@destroy')->name('destroy');
        Route::get('{id}/pilih-penyedia',  'App\Http\Controllers\ProyekController@step2')->name('step2');
        Route::post('{id}/pilih-penyedia', 'App\Http\Controllers\ProyekController@saveStep2')->name('save.step2');
        Route::get('{id}/review',  'App\Http\Controllers\ProyekController@review')->name('review');
        Route::post('{id}/draft',  'App\Http\Controllers\ProyekController@saveDraft')->name('save.draft');
        Route::post('{id}/kirim',  'App\Http\Controllers\ProyekController@kirimKePenyedia')->name('kirim');
    });

    // Vendor CRUD
    Route::prefix('vendors')->name('admin.vendors.')->group(function () {
        Route::get('/',              'App\Http\Controllers\Admin\PenyediaController@index')->name('index');
        Route::get('/create',        'App\Http\Controllers\Admin\PenyediaController@create')->name('create');
        Route::post('/',             'App\Http\Controllers\Admin\PenyediaController@store')->name('store');
        Route::get('/{id}/edit',     'App\Http\Controllers\Admin\PenyediaController@edit')->name('edit');
        Route::put('/{id}',          'App\Http\Controllers\Admin\PenyediaController@update')->name('update');
        Route::patch('/{id}/toggle', 'App\Http\Controllers\Admin\PenyediaController@toggleStatus')->name('toggleStatus');
    });
});

// ─── Legacy / misc ─────────────────────────────────────────────────────────────

Route::post('/assign',      'App\Http\Controllers\PenugasanController@assign');
Route::post('/respon/{id}', 'App\Http\Controllers\PenugasanController@respon');
Route::post('/detail',      'App\Http\Controllers\PenugasanController@isiDetail');

<<<<<<< Updated upstream
// ─── Midtrans Webhook (CSRF exempt — lihat bootstrap/app.php) ─────────────────

Route::post('/payments/midtrans-notification', [PaymentCallbackController::class, 'receive'])
    ->name('midtrans.callback');
=======
Route::post('/assign', [PenugasanController::class, 'assign']);
Route::post('/respon/{id}', [PenugasanController::class, 'respon']);
Route::post('/detail', [PenugasanController::class, 'isiDetail']);

// Midtrans
Route::post('payments/midtrans-notification', [PaymentCallbackController::class, 'receive']);
>>>>>>> Stashed changes
