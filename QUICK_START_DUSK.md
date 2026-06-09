# Quick Start Guide - Menjalankan DonasiTest

## 🚀 Langkah Cepat

### 1️⃣ Setup Awal (Sekali saja)
```bash
# Install Laravel Dusk
composer require --dev laravel/dusk

# Setup Dusk (pilih 'yes' untuk setup)
php artisan dusk:install

# Pastikan database sudah configured di .env atau .env.testing
```

### 2️⃣ Jalankan Semua Test Donasi
```bash
php artisan dusk tests/Browser/DonasiTest.php
```

### 3️⃣ Expected Output
```
======================================================
 LARAVEL DUSK TEST SUITE
======================================================

PASS  Tests\Browser\DonasiTest
  ✓ test_tc_17_01_user_views_crowdfunding_project_detail
  ✓ test_tc_17_02_donation_success_auto_updates_funding
  ✓ test_tc_17_03_failed_donation_does_not_update_funding
  ✓ test_tc_17_04_funding_reaches_target_updates_status

======================================================
 RESULTS
======================================================

Tests:    4 passed (4 assertions)
Duration: 45.23s

✅ All tests passed!
```

---

## 🎯 Menjalankan Test Individu

### Jalankan TC-17-01 saja:
```bash
php artisan dusk tests/Browser/DonasiTest.php --filter test_tc_17_01_user_views_crowdfunding_project_detail
```

### Jalankan TC-17-02 saja:
```bash
php artisan dusk tests/Browser/DonasiTest.php --filter test_tc_17_02_donation_success_auto_updates_funding
```

### Jalankan TC-17-03 saja:
```bash
php artisan dusk tests/Browser/DonasiTest.php --filter test_tc_17_03_failed_donation_does_not_update_funding
```

### Jalankan TC-17-04 saja:
```bash
php artisan dusk tests/Browser/DonasiTest.php --filter test_tc_17_04_funding_reaches_target_updates_status
```

---

## 📊 Test Execution Flow

```
┌─────────────────────────────────────────────────┐
│   Test Start: DonasiTest                        │
└─────────────────────────────────────────────────┘
                      │
        ┌─────────────┼─────────────┐
        │             │             │
        ▼             ▼             ▼
   Refresh DB   Setup Dusk   Start Server
        │             │             │
        └─────────────┼─────────────┘
                      │
        ┌─────────────▼──────────────┐
        │ TC-17-01: View Project     │
        │ ✓ Create test data         │
        │ ✓ Navigate to page         │
        │ ✓ Assert elements shown    │
        └─────────────┬──────────────┘
                      │
        ┌─────────────▼──────────────┐
        │ TC-17-02: Success Donation │
        │ ✓ Create test data         │
        │ ✓ Navigate to page         │
        │ ✓ Create donation          │
        │ ✓ Refresh & assert update  │
        └─────────────┬──────────────┘
                      │
        ┌─────────────▼──────────────┐
        │ TC-17-03: Failed Donation  │
        │ ✓ Create test data         │
        │ ✓ Navigate to page         │
        │ ✓ Create failed donations  │
        │ ✓ Assert no change         │
        └─────────────┬──────────────┘
                      │
        ┌─────────────▼──────────────┐
        │ TC-17-04: Target Reached   │
        │ ✓ Create test data         │
        │ ✓ Navigate to page         │
        │ ✓ Create donation          │
        │ ✓ Update status            │
        │ ✓ Assert 100% & new status │
        └─────────────┬──────────────┘
                      │
        ┌─────────────▼──────────────┐
        │ All Tests Complete         │
        │ Database Refresh           │
        │ Browser Close              │
        └─────────────┬──────────────┘
                      │
        ┌─────────────▼──────────────┐
        │ 📊 Summary Report          │
        │ Passed: 4/4                │
        │ Failed: 0/4                │
        │ Skipped: 0/4               │
        │ Time: 45.23s               │
        └────────────────────────────┘
```

---

## 📝 Struktur Test Case

Setiap test case mengikuti pola **AAA (Arrange-Act-Assert)**:

```php
/**
 * Test Description
 * Expected: What should happen
 */
public function test_tc_XX_XX_description(): void
{
    // ARRANGE: Setup test data
    $proyek = Proyek::factory()->create([...]);
    $user = User::factory()->create([...]);
    
    // ACT: Perform the action
    $this->browse(function ($browser) use ($proyek) {
        $browser->visit("/proyek/{$proyek->id}")
                ->waitForLocation("/proyek/{$proyek->id}");
        
        // Simulate user actions
    });
    
    // ASSERT: Verify results
    $browser->assertSee('expected text')
            ->assertSee('another element');
}
```

---

## 🔧 Troubleshooting Cepat

| Problem | Solution |
|---------|----------|
| `ChromeDriver error` | Run `php artisan dusk:chrome-driver` |
| `Port 8000 already in use` | Change port: `php artisan serve --port=8001` |
| `Database not refreshing` | Ensure `RefreshDatabase` trait is used |
| `Test timeout` | Increase timeout in browser or wait methods |
| `Element not found` | Add explicit wait: `->waitFor('.selector')` |

---

## ✅ Checklist Sebelum Jalankan Test

- [ ] Laravel Dusk sudah install
- [ ] Dusk sudah di-setup (`php artisan dusk:install`)
- [ ] `.env.testing` sudah configured
- [ ] Database migrations sudah berjalan
- [ ] Server dapat dijalankan di `http://localhost:8000`
- [ ] No applications using port 8000
- [ ] Project dependencies sudah install (`composer install`)

---

## 📚 Referensi Dokumentasi

- [Laravel Dusk Official Docs](https://laravel.com/docs/dusk)
- [Laravel Testing Docs](https://laravel.com/docs/testing)
- [RefreshDatabase Trait](https://laravel.com/docs/database-testing#resetting-the-database-after-each-test)

---

**Created**: 2025-06-08  
**Framework**: Laravel Dusk  
**Test File**: `tests/Browser/DonasiTest.php`
