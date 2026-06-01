<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\PaymentCallbackController;
=======
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\PenyediaController;
use App\Http\Controllers\Admin\PenyediaController as AdminPenyediaController;
use App\Http\Controllers\Vendor\ProyekController as VendorProyekController;
use App\Http\Controllers\OrderController;
<<<<<<< HEAD

// ─── Public ───────────────────────────────────────────────────────────────────
=======
use App\Http\Controllers\PaymentCallbackController;

// ─── Public ──────────────────────────────────────────────────────────────────
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd

Route::get('/', function () {
    $query = \App\Models\Proyek::with(['desa', 'fotos']);

    if (request()->filled('search')) {
        $search = request('search');
        $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhereHas('desa', fn ($dq) => $dq->where('nama_desa', 'like', "%{$search}%"));
        });
    }

    $statusMap = [
        'berlangsung' => 'aktif_funding',
        'terpenuhi'   => 'eksekusi',
        'selesai'     => 'selesai',
    ];

    if (request()->filled('status') && isset($statusMap[request('status')])) {
        $query->where('status', $statusMap[request('status')]);
    } else {
        $query->where('status', 'aktif_funding');
    }

    $provinceOptions = (clone $query)
        ->whereHas('desa')
        ->join('desa', 'proyeks.desa_id', '=', 'desa.id_desa')
        ->select('desa.provinsi')
        ->distinct()
        ->orderBy('desa.provinsi')
        ->pluck('desa.provinsi');

    if (request()->filled('wilayah')) {
        $query->whereHas('desa', fn ($dq) => $dq->where('provinsi', request('wilayah')));
    }

    $projects = $query->latest('proyeks.created_at')->paginate(6)->withQueryString();
    return view('welcome', compact('projects', 'provinceOptions'));
});

<<<<<<< HEAD
=======
Route::get('/proyek/{id}', [ProyekController::class, 'show'])->name('proyek.show');
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd
Route::get('/penyedia/daftar', [PenyediaController::class, 'index'])->name('penyedia.daftar');
Route::get('/penyedia/{id}', [PenyediaController::class, 'show'])->name('penyedia.show');
Route::get('/api/penyedia/rekomendasi', [PenyediaController::class, 'getRekomendasi'])->name('api.penyedia.rekomendasi');

// ─── Auth (guest only) ─────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

<<<<<<< HEAD
// ─── Authenticated ─────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    // Project detail is protected (only accessible to logged-in users)
    Route::get('/proyek/{id}', [ProyekController::class, 'show'])->name('proyek.show');

    // Donation routes
    Route::post('/donasi/{proyek_id}', [OrderController::class, 'store'])->name('donasi.store');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');

=======
// ─── Donasi (Authenticated) ───────────────────────────────────────────────────

Route::middleware('auth')->prefix('donasi')->name('donasi.')->group(function () {
    Route::get('/',                      [OrderController::class, 'create'])->name('create');
    Route::post('/{proyek}',             [OrderController::class, 'store'])->name('store');
    Route::get('/{order}',               [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/status',        [OrderController::class, 'status'])->name('status');
});

// ─── Authenticated ────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return match ($user->role) {
            'admin'    => redirect()->route('desa.daftar'),
            'penyedia' => redirect()->route('vendor.dashboard'),
            default    => redirect('/'),
        };
    })->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
});

<<<<<<< HEAD
// ─── Vendor (Penyedia Energi) ──────────────────────────────────────────────────
=======
// ─── Vendor (Penyedia Energi) ─────────────────────────────────────────────────
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd

Route::middleware(['auth', 'penyedia'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/dashboard', function () {
        return view('penyedia.dashboard');
    })->name('dashboard');

    Route::prefix('proyek')->name('proyek.')->controller(VendorProyekController::class)->group(function () {
        Route::get('/',            'index')->name('index');
        Route::get('/{id}',        'show')->name('show');
        Route::get('/{id}/expiry-decision', 'expiryDecisionShow')->name('expiry-decision.show');
        Route::put('/{id}/detail', 'saveDetail')->name('detail');
        Route::post('/{id}/expiry-decision', 'expiryDecision')->name('expiry-decision');
        Route::post('/{id}/klarifikasi', 'mintaKlarifikasi')->name('klarifikasi');
        Route::post('/{id}/kendala',     'laporkanKendala')->name('kendala');
    });
});

Route::middleware('auth')->get('/penyedia/dashboard', function () {
    return redirect()->route('vendor.dashboard');
})->name('penyedia.dashboard');

<<<<<<< HEAD
// ─── Admin ─────────────────────────────────────────────────────────────────────
=======
// ─── Admin ────────────────────────────────────────────────────────────────────
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Desa management
    Route::prefix('desa')->name('desa.')->group(function () {
        Route::get('input', [DesaController::class, 'create'])->name('input');
        Route::post('/', [DesaController::class, 'store'])->name('store');
        Route::get('kelola', [DesaController::class, 'kelola'])->name('kelola');
        Route::get('daftar', [DesaController::class, 'index'])->name('daftar');

        Route::get('{id}/edit', [DesaController::class, 'edit'])->name('edit');
        Route::put('{id}', [DesaController::class, 'update'])->name('update');
        Route::delete('{id}', [DesaController::class, 'destroy'])->name('destroy');
    });

    // Proyek management
    Route::prefix('proyek')->name('proyek.')->group(function () {
        Route::get('buat', [ProyekController::class, 'create'])->name('create');
        Route::post('buat', [ProyekController::class, 'saveStep1'])->name('save.step1');
        Route::get('kelola', [ProyekController::class, 'kelola'])->name('kelola');
        Route::get('{id}/detail', [ProyekController::class, 'adminShow'])->name('admin.show');
        Route::patch('{id}/publish', [ProyekController::class, 'publish'])->name('publish');
        Route::delete('{id}', [ProyekController::class, 'destroy'])->name('destroy');

        Route::get('{id}/pilih-penyedia', [ProyekController::class, 'step2'])->name('step2');
        Route::post('{id}/pilih-penyedia', [ProyekController::class, 'saveStep2'])->name('save.step2');

        Route::get('{id}/review', [ProyekController::class, 'review'])->name('review');
        Route::post('{id}/draft', [ProyekController::class, 'saveDraft'])->name('save.draft');
        Route::post('{id}/kirim', [ProyekController::class, 'kirimKePenyedia'])->name('kirim');
    });

    // Vendor CRUD
    Route::prefix('vendors')->name('admin.vendors.')->group(function () {
        Route::get('/', [AdminPenyediaController::class, 'index'])->name('index');
        Route::get('/create', [AdminPenyediaController::class, 'create'])->name('create');
        Route::post('/', [AdminPenyediaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminPenyediaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminPenyediaController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle', [AdminPenyediaController::class, 'toggleStatus'])->name('toggleStatus');
    });
});

// ─── Legacy / misc ─────────────────────────────────────────────────────────────

Route::get('/profil-preview', [ProfileController::class, 'edit']);

Route::post('/assign', [PenugasanController::class, 'assign']);
Route::post('/respon/{id}', [PenugasanController::class, 'respon']);
Route::post('/detail', [PenugasanController::class, 'isiDetail']);

<<<<<<< HEAD
// ─── Midtrans Webhook (CSRF exempt) ───────────────────────────────────────────
=======
// ─── Midtrans Webhook (CSRF exempt — lihat bootstrap/app.php) ─────────────────
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd

Route::post('/payments/midtrans-notification', [PaymentCallbackController::class, 'receive'])
    ->name('midtrans.callback');
