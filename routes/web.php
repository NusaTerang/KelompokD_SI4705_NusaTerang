<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\PenyediaController;
use App\Http\Controllers\Admin\PenyediaController as AdminPenyediaController;

// ─── Public ──────────────────────────────────────────────────────────────────

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
    }

    $projects = $query->latest()->paginate(6)->withQueryString();
    return view('welcome', compact('projects'));
});

Route::get('/proyek/{id}', [ProyekController::class, 'show'])->name('proyek.show');
Route::get('/penyedia/daftar', [PenyediaController::class, 'index'])->name('penyedia.daftar');
Route::get('/api/penyedia/rekomendasi', [PenyediaController::class, 'getRekomendasi'])->name('api.penyedia.rekomendasi');

// ─── Auth (guest only) ────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// ─── Authenticated ────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return match ($user->role) {
            'admin'    => redirect()->route('desa.daftar'),
            'penyedia' => redirect()->route('penyedia.dashboard'),
            default    => redirect('/'),
        };
    })->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
});

// ─── Penyedia ─────────────────────────────────────────────────────────────────

Route::middleware('auth')->prefix('penyedia')->group(function () {
    Route::get('/dashboard', function () {
        return view('penyedia.dashboard');
    })->name('penyedia.dashboard');
});

// ─── Admin ────────────────────────────────────────────────────────────────────

Route::middleware('auth')->prefix('admin')->group(function () {

    // Desa management
    Route::prefix('desa')->name('desa.')->group(function () {
        Route::get('input', [DesaController::class, 'create'])->name('input');
        Route::post('/', [DesaController::class, 'store'])->name('store');
        Route::get('kelola', [DesaController::class, 'kelola'])->name('kelola');
        Route::get('daftar', [DesaController::class, 'index'])->name('daftar');
    });

    // Proyek creation wizard
    Route::prefix('proyek')->name('proyek.')->group(function () {
        Route::get('buat', [ProyekController::class, 'create'])->name('create');
        Route::post('buat', [ProyekController::class, 'saveStep1'])->name('save.step1');

        Route::get('{id}/pilih-penyedia', [ProyekController::class, 'step2'])->name('step2');
        Route::post('{id}/pilih-penyedia', [ProyekController::class, 'saveStep2'])->name('save.step2');

        Route::get('{id}/review', [ProyekController::class, 'review'])->name('review');
        Route::post('{id}/kirim', [ProyekController::class, 'kirimKePenyedia'])->name('kirim');
    });

    // Vendor / Penyedia Energi CRUD
    Route::prefix('vendors')->name('admin.vendors.')->group(function () {
        Route::get('/', [AdminPenyediaController::class, 'index'])->name('index');
        Route::get('/create', [AdminPenyediaController::class, 'create'])->name('create');
        Route::post('/', [AdminPenyediaController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminPenyediaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminPenyediaController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle', [AdminPenyediaController::class, 'toggleStatus'])->name('toggleStatus');
    });
});

// ─── Legacy / misc ────────────────────────────────────────────────────────────

Route::get('/profil-preview', [ProfileController::class, 'edit']);

Route::post('/assign', [PenugasanController::class, 'assign']);
Route::post('/respon/{id}', [PenugasanController::class, 'respon']);
Route::post('/detail', [PenugasanController::class, 'isiDetail']);
