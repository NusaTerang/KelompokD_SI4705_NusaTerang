# Setup dan Menjalankan Automated Testing untuk Donasi (DonasiTest)

## 📋 Daftar Test Case

File `tests/Browser/DonasiTest.php` berisi 4 test case untuk fitur donasi:

| TC ID | Test Case | Expected Result |
|-------|-----------|-----------------|
| TC-17-01 | User membuka halaman detail proyek crowdfunding | System menampilkan progress pendanaan, total dana, target dana, dan status project |
| TC-17-02 | Terdapat transaksi donasi baru dengan status success | Data pendanaan dan activity feed ter-update otomatis tanpa reload |
| TC-17-03 | Transaksi donasi berstatus failed/pending | Progress pendanaan tidak berubah |
| TC-17-04 | Total dana mencapai target pendanaan | Progress menjadi 100% dan status project berubah sesuai kondisi |

## 🔧 Prasyarat

Pastikan project sudah memiliki:
- PHP 8.3+
- Laravel 13.0+
- SQLite atau database lainnya sudah dikonfigurasi

## 📦 Instalasi Laravel Dusk

1. Install Laravel Dusk via Composer:
```bash
composer require --dev laravel/dusk
```

2. Publikasikan Dusk assets:
```bash
php artisan dusk:install
```

3. Jika menggunakan Windows, pastikan ChromeDriver sudah ter-setup. Dusk akan otomatis mendownload di `database/dusk-drivers/`

## 🛠️ Konfigurasi

### 1. Database Testing
Pastikan `.env` sudah dikonfigurasi atau buat `.env.testing`:
```
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### 2. Aplikasi URL
Di `phpunit.dusk.xml`, pastikan `APP_URL` sudah benar:
```xml
<env name="APP_URL" value="http://localhost:8000"/>
```

### 3. Database Migration
Pastikan migrations sudah berjalan:
```bash
php artisan migrate --env=testing
```

## ▶️ Menjalankan Tests

### Jalankan semua test Dusk:
```bash
php artisan dusk
```

### Jalankan hanya DonasiTest:
```bash
php artisan dusk tests/Browser/DonasiTest.php
```

### Jalankan test case spesifik:
```bash
php artisan dusk tests/Browser/DonasiTest.php --filter test_tc_17_01_user_views_crowdfunding_project_detail
```

### Mode verbose (menampilkan detail lebih lengkap):
```bash
php artisan dusk --verbose
```

## 📊 Output yang Diharapkan

Setelah test selesai, Anda akan melihat:
```
PASS  Tests\Browser\DonasiTest
  ✓ test_tc_17_01_user_views_crowdfunding_project_detail
  ✓ test_tc_17_02_donation_success_auto_updates_funding
  ✓ test_tc_17_03_failed_donation_does_not_update_funding
  ✓ test_tc_17_04_funding_reaches_target_updates_status

Tests:  4 passed
Time:   XX.XXs
```

## 🔍 Detail Setiap Test Case

### TC-17-01: User membuka halaman detail proyek crowdfunding
- **Setup**: Create proyek dengan target 500jt, dana terkumpul 150jt
- **Action**: Buka halaman `/proyek/{id}`
- **Assertion**: 
  - Progress menampilkan 30%
  - Total dana menampilkan Rp 150 (juta)
  - Target dana menampilkan Rp 500 (juta)
  - Status project menampilkan 'aktif_funding'

### TC-17-02: Terdapat transaksi donasi baru dengan status success
- **Setup**: Create proyek dengan data awal, buat satu donasi
- **Action**: 
  - Buka halaman project
  - Create donasi baru dengan status 'success' (100jt)
  - Refresh halaman
- **Assertion**:
  - Progress ter-update menjadi 60% (300jt/500jt)
  - Total dana ter-update menjadi Rp 300

### TC-17-03: Transaksi donasi berstatus failed/pending
- **Setup**: Create proyek dengan dana 150jt
- **Action**:
  - Buka halaman project
  - Create donasi dengan status 'failed' (100jt)
  - Create donasi dengan status 'pending' (50jt)
  - Refresh halaman
- **Assertion**:
  - Progress tetap 30% (tidak berubah)
  - Total dana tetap Rp 150

### TC-17-04: Total dana mencapai target pendanaan
- **Setup**: Create proyek dengan dana 400jt (80% dari target 500jt)
- **Action**:
  - Buka halaman project
  - Create donasi success sebesar 100jt (total jadi 500jt)
  - Update status project ke 'eksekusi'
  - Refresh halaman
- **Assertion**:
  - Progress menampilkan 100%
  - Total dana menampilkan Rp 500
  - Status project menampilkan 'eksekusi'

## 📸 Mengambil Screenshot Saat Test Gagal

Dusk otomatis mengambil screenshot jika test gagal. File screenshot disimpan di:
```
tests/Browser/screenshots/
```

Untuk debugging lebih detail, lihat juga console logs di:
```
tests/Browser/console/
```

## 🐛 Troubleshooting

### ChromeDriver tidak ditemukan
```bash
php artisan dusk:chrome-driver
```

### Port 8000 sudah digunakan
Jalankan server di port berbeda:
```bash
php artisan serve --port=8001
```

Kemudian update `APP_URL` di `.env.testing`:
```
APP_URL=http://localhost:8001
```

### Test timeout
Tingkatkan timeout di test method:
```php
$this->browse(function ($browser) {
    $browser->driver->manage()->timeouts()->implicitlyWait(10);
});
```

### Database tidak ter-reset antar test
Pastikan trait `RefreshDatabase` sudah digunakan di test class (sudah ada di DonasiTest.php)

## ✅ Verifikasi Test Independensi

Setiap test case di DonasiTest sudah dirancang untuk:
- ✅ Independen (tidak bergantung pada test lain)
- ✅ Menggunakan `RefreshDatabase` (database di-reset setiap test)
- ✅ Membuat data sendiri via Factory dan Model::create()
- ✅ Dapat dijalankan secara individual
- ✅ Dapat dijalankan dalam urutan apapun

## 📝 Tips Best Practice

1. **Jalankan dalam urutan random** untuk memastikan independensi:
```bash
php artisan dusk --shuffle
```

2. **Gunakan `--parallel`** untuk mempercepat test (memerlukan multiple browsers):
```bash
php artisan dusk --parallel --processes=4
```

3. **Monitor test execution** dengan `--verbose`:
```bash
php artisan dusk --verbose
```

4. **Integrasi dengan CI/CD** - Tambahkan ke `composer.json` scripts:
```json
"test-dusk": [
    "@php artisan config:clear",
    "@php artisan dusk"
]
```

Kemudian jalankan: `composer test-dusk`

---

**Last Updated**: 2025-06-08
**Test Framework**: Laravel Dusk
**PHP Version**: 8.3+
**Laravel Version**: 13.0+
