# 📋 Dokumentasi Proyek — NusaTerang

> Platform energi terbarukan untuk menerangi desa-desa di Indonesia.

---

## 🛠️ Tech Stack

| Kategori           | Teknologi                  | Versi / Detail                        |
|--------------------|----------------------------|---------------------------------------|
| **Framework**      | Laravel                    | 11.x                                  |
| **Bahasa**         | PHP                        | 8.2+                                  |
| **Database**       | PostgreSQL                 | 15+ (disarankan)                      |
| **Frontend CSS**   | Tailwind CSS               | CDN (`cdn.tailwindcss.com`)           |
| **Font**           | Google Fonts — Inter       | Weight: 400, 500, 600, 700, 800      |
| **Ikon**           | Lucide Icons               | CDN (`unpkg.com/lucide@latest`)       |
| **Template Engine**| Laravel Blade              | `.blade.php`                          |
| **Autentikasi**    | Custom (tanpa Breeze/Jetstream) | Controller manual                |

---

## 📁 Struktur Proyek (Project Tree)

```
Project PPL/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Auth/
│   │           ├── AuthenticatedSessionController.php   ← Login & Logout logic
│   │           └── RegisteredUserController.php         ← Register logic
│   │
│   └── Models/
│       └── User.php                                     ← User model + role helpers
│
├── database/
│   └── migrations/
│       └── 2024_01_01_000000_create_users_table.php     ← Migration: users, password_resets, sessions
│
├── resources/
│   └── views/
│       └── auth/
│           ├── login.blade.php                          ← Halaman Masuk
│           └── register.blade.php                       ← Halaman Daftar
│
├── routes/
│   └── web.php                                          ← Semua route (guest & auth)
│
├── daftar.html                                          ← Referensi desain UI (Register)
├── masuk.html                                           ← Referensi desain UI (Login)
│
└── fitur.md                                             ← Dokumentasi ini
```

---

## 🗄️ Skema Database

### Tabel `users`

| Kolom               | Tipe      | Keterangan                                         |
|----------------------|-----------|-----------------------------------------------------|
| `id`                 | BIGINT    | Primary Key, auto-increment                         |
| `name`               | VARCHAR   | Nama lengkap pengguna                               |
| `email`              | VARCHAR   | Unik, untuk login                                   |
| `email_verified_at`  | TIMESTAMP | Nullable, verifikasi email                          |
| `password`           | VARCHAR   | Di-hash dengan `Hash::make` / cast `hashed`         |
| `phone`              | VARCHAR   | Nullable, nomor telepon                             |
| `role`               | ENUM      | `'admin'`, `'penyedia'`, `'donatur'` (default: `donatur`) |
| `remember_token`     | VARCHAR   | Untuk fitur "Ingat Saya"                            |
| `created_at`         | TIMESTAMP | Otomatis oleh Laravel                               |
| `updated_at`         | TIMESTAMP | Otomatis oleh Laravel                               |

### Tabel `password_reset_tokens`

| Kolom        | Tipe      | Keterangan          |
|--------------|-----------|----------------------|
| `email`      | VARCHAR   | Primary Key          |
| `token`      | VARCHAR   | Token reset          |
| `created_at` | TIMESTAMP | Waktu dibuat         |

### Tabel `sessions`

| Kolom           | Tipe    | Keterangan               |
|-----------------|---------|---------------------------|
| `id`            | VARCHAR | Primary Key               |
| `user_id`       | BIGINT  | Nullable, FK ke users     |
| `ip_address`    | VARCHAR | IP pengguna               |
| `user_agent`    | TEXT    | Browser / device info     |
| `payload`       | LONGTEXT| Data session              |
| `last_activity` | INT     | Timestamp aktivitas       |

---

## 🔐 Fitur Autentikasi

### 1. Registrasi (`/register`)
- **Controller**: `RegisteredUserController`
- **Validasi**:
  - `name` → wajib, string, maks 255 karakter
  - `email` → wajib, format email, unik
  - `phone` → opsional, maks 20 karakter
  - `password` → wajib, minimal 8 karakter, harus dikonfirmasi
- **Proses**:
  1. Validasi input
  2. Buat user baru dengan role default `donatur`
  3. Password di-hash dengan `Hash::make`
  4. Event `Registered` di-dispatch
  5. User langsung login otomatis
  6. Redirect ke dashboard sesuai role

### 2. Login (`/login`)
- **Controller**: `AuthenticatedSessionController`
- **Validasi**:
  - `email` → wajib, format email
  - `password` → wajib
- **Proses**:
  1. Validasi input
  2. `Auth::attempt` dengan opsi "Remember Me"
  3. Jika gagal → error "Email atau password salah"
  4. Jika berhasil → regenerasi session → redirect sesuai role
- **Fitur Tambahan**:
  - ✅ Checkbox "Ingat Saya" (Remember Me)
  - ✅ Link "Lupa Password?"
  - ✅ Proteksi session fixation (session regeneration)

### 3. Logout (`POST /logout`)
- Invalidasi session
- Regenerasi CSRF token
- Redirect ke halaman utama (`/`)

---

## 🔀 Role-Based Redirect

| Role       | Redirect Setelah Login/Register     | Route Name            |
|------------|--------------------------------------|-----------------------|
| `admin`    | `/admin/dashboard`                   | `admin.dashboard`     |
| `penyedia` | `/penyedia/dashboard`                | `penyedia.dashboard`  |
| `donatur`  | `/donatur/dashboard`                 | `donatur.dashboard`   |

### Helper Methods di Model `User`

```php
$user->hasRole('admin');    // true/false
$user->isAdmin();           // true/false
$user->isPenyedia();        // true/false
$user->isDonatur();         // true/false
```

---

## 🛣️ Daftar Route

### Guest Routes (hanya untuk user yang belum login)

| Method | URI         | Controller                          | Name       |
|--------|-------------|--------------------------------------|------------|
| GET    | `/register` | `RegisteredUserController@create`    | `register` |
| POST   | `/register` | `RegisteredUserController@store`     | —          |
| GET    | `/login`    | `AuthenticatedSessionController@create` | `login` |
| POST   | `/login`    | `AuthenticatedSessionController@store`  | —       |

### Auth Routes (hanya untuk user yang sudah login)

| Method | URI                   | Action / View              | Name                 |
|--------|-----------------------|-----------------------------|----------------------|
| GET    | `/dashboard`          | Auto-redirect by role       | `dashboard`          |
| GET    | `/admin/dashboard`    | `admin.dashboard` view      | `admin.dashboard`    |
| GET    | `/penyedia/dashboard` | `penyedia.dashboard` view   | `penyedia.dashboard` |
| GET    | `/donatur/dashboard`  | `donatur.dashboard` view    | `donatur.dashboard`  |
| POST   | `/logout`             | `AuthenticatedSessionController@destroy` | `logout` |

---

## 🎨 Desain UI

### Konsep Visual
- **Layout**: Split-screen (desktop) — brand panel kiri + form kanan
- **Warna Utama**: Dark blue gradient (`#1a4a6e` → `#0d2f4a`)
- **Aksen**: Kuning (`bg-yellow-400`) untuk tombol utama
- **Background**: Warm gray (`#fafaf7`)
- **Input Style**: `bg-gray-100`, rounded-xl, ikon di kiri (Lucide), focus border `#1a4a6e`
- **Responsif**: Logo mobile terpisah, form full-width di mobile

### Halaman Login (`login.blade.php`)
- Hero image: Mobil listrik (Unsplash)
- Tagline: "Terangi Nusantara Bersama"
- Form: Email + Password + Ingat Saya + Lupa Password
- Social Login: Google + Facebook
- Footer: Link ke halaman Daftar

### Halaman Register (`register.blade.php`)
- Hero image: Panel surya (Unsplash)
- Tagline: "Mulai Perjalanan Keberlanjutan Anda"
- Form: Nama + Email + Telepon + Password + Konfirmasi (grid 2 kolom)
- Social Login: Google + Facebook
- Footer: Link ke halaman Masuk + Syarat & Ketentuan

---

## 🔒 Keamanan

| Fitur                        | Implementasi                                      |
|------------------------------|---------------------------------------------------|
| CSRF Protection              | `@csrf` di setiap form Blade                      |
| Password Hashing             | `Hash::make()` + cast `hashed` di model           |
| Session Fixation Prevention  | `$request->session()->regenerate()` setelah login  |
| Session Invalidation         | `$request->session()->invalidate()` saat logout    |
| CSRF Token Regeneration      | `$request->session()->regenerateToken()` saat logout|
| Input Validation             | Server-side validation di controller               |
| Error Display                | `@error('field')` directive di Blade               |
| Mass Assignment Protection   | `$fillable` di model User                          |

---

## 📌 Catatan Setup

### Prerequisite
- PHP >= 8.2
- Composer
- PostgreSQL >= 15
- Node.js (opsional, jika ingin compile Tailwind lokal)

### Langkah Instalasi
```bash
# 1. Buat proyek Laravel (jika belum)
composer create-project laravel/laravel . --prefer-dist

# 2. Konfigurasi .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nusaterang
DB_USERNAME=postgres
DB_PASSWORD=your_password

# 3. Jalankan migrasi
php artisan migrate

# 4. Jalankan server
php artisan serve
```

### File yang Harus Di-copy (dari dokumentasi ini)
1. `app/Models/User.php`
2. `app/Http/Controllers/Auth/RegisteredUserController.php`
3. `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
4. `database/migrations/2024_01_01_000000_create_users_table.php`
5. `resources/views/auth/login.blade.php`
6. `resources/views/auth/register.blade.php`
7. `routes/web.php`

---

## 🚧 TODO — Belum Dikerjakan

- [ ] Dashboard views (`admin/dashboard.blade.php`, `penyedia/dashboard.blade.php`, `donatur/dashboard.blade.php`)
- [ ] Middleware role-based access (mencegah donatur akses `/admin/dashboard`)
- [ ] Halaman Lupa Password / Reset Password
- [ ] Email verification flow
- [ ] Integrasi OAuth (Google & Facebook login)
- [ ] Profil pengguna (edit nama, email, foto)
- [ ] Seeder untuk user admin default
- [ ] Unit & Feature tests untuk auth flow
