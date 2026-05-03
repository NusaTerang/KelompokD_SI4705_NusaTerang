# Product Requirements Document (PRD)
## NusaTerang — Sprint 1
**Versi:** 1.0  
**Tanggal:** April 2026  
**Kelompok:** D — PPL Telkom University  
**PIC Sprint 1:** Riftita (PM), Firdaus, Jasmine, Naila, Nasywan  

---

## 1. Ringkasan Produk

**NusaTerang** adalah platform web berbasis crowdfunding yang menghubungkan data desa tanpa akses energi (dari pemerintah) dengan penyedia energi terbarukan dan donatur. Platform ini memungkinkan Admin mengelola proyek energi dari perencanaan hingga eksekusi dan monitoring secara transparan.

**Tech Stack:**
- Backend: PHP (Laravel)
- Frontend: Blade + Tailwind CSS + JavaScript
- Database: MySQL
- Web Server: Apache (XAMPP / lokal)

---

## 2. Scope Sprint 1

Sprint 1 berjalan **6 April – 2 Mei 2026** dengan total **84 Story Points, 15 PBI**.

Dokumen ini mencakup dua PBI utama yang dikembangkan secara penuh:

| PBI | Nama Fitur | FR | UC | PIC | SP |
|-----|-----------|----|----|-----|----|
| PBI-10 | Rekomendasi Penyedia Energi | FR-09 | UC-10 | Riftita | 8 |
| PBI-12 | Buat Proyek Energi | FR-11 | UC-12 | Riftita | 8 |

PBI lain dalam Sprint 1 menggunakan **dummy data** sebagai dependensi (detail di Seksi 3).

---

## 3. Dependensi Sprint 1

Fitur PBI-10 dan PBI-12 membutuhkan data dari PBI lain yang belum dikembangkan penuh. Berikut pemetaan dummy data yang digunakan:

| Dependensi | PBI Asal | Status | Dummy Data |
|-----------|---------|--------|-----------|
| Data Desa Terverifikasi | PBI-07 (Kelola Data Desa) | Dummy | 3–5 desa seeded di database dengan status `terverifikasi` |
| Data Penyedia Energi | PBI-11 (Kelola Data Penyedia) | Dummy | 5–8 penyedia seeded dengan spesialisasi & wilayah bervariasi |
| Auth & Session Admin | PBI-01 s/d PBI-06 (Auth) | Dikerjakan Firdaus | Menggunakan login Admin yang dikerjakan paralel |
| RBAC Admin | PBI-06 | Dikerjakan Firdaus | Guard `admin` harus sudah tersedia |

### Seed Data: Desa (Dummy)

```php
// database/seeders/DesaDummySeeder.php
[
  ['nama' => 'Desa Cikaret', 'provinsi' => 'Jawa Barat', 'kabupaten' => 'Bogor', 
   'lat' => -6.5971, 'lng' => 106.8160, 'jenis_energi' => 'solar', 
   'estimasi_biaya' => 150000000, 'status' => 'terverifikasi'],

  ['nama' => 'Desa Sumber Makmur', 'provinsi' => 'Jawa Tengah', 'kabupaten' => 'Banjarnegara',
   'lat' => -7.3606, 'lng' => 109.6946, 'jenis_energi' => 'mikro_hidro', 
   'estimasi_biaya' => 200000000, 'status' => 'terverifikasi'],

  ['nama' => 'Desa Lembah Hijau', 'provinsi' => 'Sumatera Barat', 'kabupaten' => 'Solok',
   'lat' => -0.7893, 'lng' => 100.6500, 'jenis_energi' => 'solar', 
   'estimasi_biaya' => 120000000, 'status' => 'terverifikasi'],
]
```

### Seed Data: Penyedia Energi (Dummy)

```php
// database/seeders/PenyediaDummySeeder.php
[
  ['nama' => 'SolarNusa Indonesia', 'spesialisasi' => 'solar', 
   'provinsi_operasi' => 'Jawa Barat', 'kisaran_harga_min' => 100000000, 
   'kisaran_harga_max' => 200000000, 'rating' => 4.8, 'status' => 'aktif'],

  ['nama' => 'HidroTech Nusantara', 'spesialisasi' => 'mikro_hidro', 
   'provinsi_operasi' => 'Jawa Tengah', 'kisaran_harga_min' => 150000000, 
   'kisaran_harga_max' => 300000000, 'rating' => 4.5, 'status' => 'aktif'],

  ['nama' => 'EnergiHijau Sumatera', 'spesialisasi' => 'solar', 
   'provinsi_operasi' => 'Sumatera Barat', 'kisaran_harga_min' => 80000000, 
   'kisaran_harga_max' => 180000000, 'rating' => 4.2, 'status' => 'aktif'],

  ['nama' => 'Surya Mandiri Group', 'spesialisasi' => 'solar', 
   'provinsi_operasi' => 'Jawa Barat', 'kisaran_harga_min' => 90000000, 
   'kisaran_harga_max' => 160000000, 'rating' => 4.0, 'status' => 'aktif'],

  ['nama' => 'PowerDesa Teknologi', 'spesialisasi' => 'mikro_hidro', 
   'provinsi_operasi' => 'Jawa Barat', 'kisaran_harga_min' => 120000000, 
   'kisaran_harga_max' => 250000000, 'rating' => 3.9, 'status' => 'aktif'],
]
```

---

## 4. PBI-10 — Rekomendasi Penyedia Energi

### 4.1 Deskripsi

Sistem menampilkan rekomendasi penyedia energi secara otomatis menggunakan **rule-based matching** berdasarkan tiga parameter: spesialisasi teknis, kedekatan geografis, dan kesesuaian anggaran. Fitur ini muncul di dalam wizard pembuatan proyek (Step 2).

**FR:** FR-09  
**UC:** UC-10  
**Aktor:** Sistem (otomatis), Admin (pemilih)

---

### 4.2 User Story

> **Sebagai** Admin,  
> **Saya ingin** mendapatkan rekomendasi penyedia energi yang sesuai secara otomatis,  
> **Agar** saya dapat memilih mitra terbaik untuk proyek tanpa harus membandingkan secara manual.

---

### 4.3 Acceptance Criteria

| No | Kriteria | Kondisi Terpenuhi |
|----|---------|------------------|
| AC-10-01 | Sistem menjalankan rule-based matching saat Admin masuk ke Step 2 wizard proyek | Daftar rekomendasi muncul otomatis tanpa input tambahan |
| AC-10-02 | Rekomendasi diurutkan berdasarkan skor kesesuaian (tertinggi ke terendah) | Penyedia dengan skor tertinggi muncul paling atas |
| AC-10-03 | Penyedia dengan skor tertinggi mendapat badge "Direkomendasikan" | Badge tampil jelas di kartu penyedia teratas |
| AC-10-04 | Kartu penyedia menampilkan: nama, spesialisasi, rating, kisaran harga | Semua atribut tampil dengan benar |
| AC-10-05 | Jika tidak ada penyedia yang cocok, sistem tetap menampilkan semua penyedia aktif | Pesan "Tidak ada rekomendasi otomatis" tampil, daftar manual tetap ada |
| AC-10-06 | Admin dapat memfilter daftar berdasarkan jenis energi (solar / mikro-hidro) | Daftar berubah sesuai filter |
| AC-10-07 | Admin dapat memilih satu penyedia dari daftar | Pilihan terkunci dan profil singkat muncul di bawah |
| AC-10-08 | Hanya penyedia dengan status `aktif` yang ditampilkan | Penyedia nonaktif tidak muncul |

---

### 4.4 Logika Rule-Based Matching

Skor kesesuaian dihitung dari tiga parameter dengan bobot:

| Parameter | Bobot | Kondisi Skor Penuh |
|-----------|-------|-------------------|
| Spesialisasi Teknis | 50% | `penyedia.spesialisasi == proyek.jenis_energi` |
| Kedekatan Geografis | 30% | `penyedia.provinsi_operasi == desa.provinsi` |
| Kesesuaian Anggaran | 20% | `desa.estimasi_biaya` berada dalam rentang `penyedia.kisaran_harga_min` s/d `kisaran_harga_max` |

**Formula Skor:**
```
skor = (spesialisasi_match * 50) + (geo_match * 30) + (budget_match * 20)
```

**Implementasi (PHP/Laravel):**
```php
// app/Services/PenyediaRecommendationService.php

public function getRecommendations(Proyek $proyek): Collection
{
    $desa = $proyek->desa;
    
    return Penyedia::where('status', 'aktif')
        ->get()
        ->map(function ($penyedia) use ($proyek, $desa) {
            $skor = 0;

            // Spesialisasi (bobot 50)
            if ($penyedia->spesialisasi === $proyek->jenis_energi) {
                $skor += 50;
            }

            // Kedekatan geografis (bobot 30)
            if ($penyedia->provinsi_operasi === $desa->provinsi) {
                $skor += 30;
            }

            // Kesesuaian anggaran (bobot 20)
            if ($desa->estimasi_biaya >= $penyedia->kisaran_harga_min 
                && $desa->estimasi_biaya <= $penyedia->kisaran_harga_max) {
                $skor += 20;
            }

            $penyedia->skor_kesesuaian = $skor;
            return $penyedia;
        })
        ->sortByDesc('skor_kesesuaian')
        ->values();
}
```

---

### 4.5 Tampilan UI

**Komponen yang harus ada di Step 2 wizard:**

```
┌─────────────────────────────────────────────────────┐
│  Step 2: Pilih Penyedia Energi                      │
│                                                     │
│  Filter: [Solar ▼] [Mikro-Hidro] [Semua]           │
│                                                     │
│  ┌─────────────────────────────┐                   │
│  │ ⭐ DIREKOMENDASIKAN         │                   │
│  │ SolarNusa Indonesia         │                   │
│  │ Spesialisasi: Solar Panel   │                   │
│  │ Rating: ★★★★★ 4.8         │                   │
│  │ Harga: Rp 100–200 juta     │                   │
│  │ Skor Kesesuaian: 100/100   │                   │
│  │              [Pilih]        │                   │
│  └─────────────────────────────┘                   │
│                                                     │
│  ┌─────────────────────────────┐                   │
│  │ Surya Mandiri Group         │                   │
│  │ ...                         │                   │
│  └─────────────────────────────┘                   │
│                                                     │
│            [◀ Kembali]  [Lanjut ke Review ▶]       │
└─────────────────────────────────────────────────────┘
```

---

### 4.6 Skema Database

Tidak ada tabel baru khusus untuk fitur ini. Menggunakan tabel `penyedia_energis` yang sudah ada:

```sql
CREATE TABLE penyedia_energis (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    no_telepon VARCHAR(20),
    spesialisasi ENUM('solar', 'mikro_hidro', 'lainnya') NOT NULL,
    provinsi_operasi VARCHAR(100),
    kisaran_harga_min BIGINT,
    kisaran_harga_max BIGINT,
    rating DECIMAL(3,1) DEFAULT 0,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

### 4.7 Routes & Controller

```
GET  /proyek/buat/step-2/{proyekId}   → ProyekController@step2
POST /proyek/buat/step-2/{proyekId}   → ProyekController@saveStep2
GET  /api/penyedia/rekomendasi        → PenyediaController@getRekomendasi (AJAX/filter)
```

---

## 5. PBI-12 — Buat Proyek Energi

### 5.1 Deskripsi

Admin membuat proyek energi baru melalui **wizard tiga langkah**: (1) Informasi Dasar, (2) Pilih Penyedia Energi (integrasikan UC-10), (3) Review & Kirim ke Penyedia. Proyek yang dikirim berstatus `menunggu_konfirmasi_penyedia` dan memicu notifikasi ke penyedia terpilih.

**FR:** FR-11  
**UC:** UC-12  
**Aktor:** Admin

---

### 5.2 User Story

> **Sebagai** Admin,  
> **Saya ingin** membuat proyek energi baru melalui form wizard bertahap,  
> **Agar** saya dapat dengan terstruktur menetapkan desa target, penyedia, dan mengirim proyek untuk dikonfirmasi.

---

### 5.3 Acceptance Criteria

| No | Kriteria | Kondisi Terpenuhi |
|----|---------|------------------|
| AC-12-01 | Admin dapat mengakses halaman buat proyek dari menu "Proyek Energi" atau dari halaman Desa Prioritas | Tombol "Buat Proyek" tersedia di dua lokasi |
| AC-12-02 | Step 1 menampilkan form: pilih desa, judul, deskripsi, foto, estimasi tanggal | Semua field tersedia dan tervalidasi |
| AC-12-03 | Jika desa dipilih dari halaman prioritas, field desa terisi otomatis | Field desa pre-filled dan tidak dapat diubah dari Step 1 |
| AC-12-04 | Step 2 menampilkan rekomendasi penyedia sesuai PBI-10 | Rekomendasi muncul otomatis berdasarkan jenis energi desa |
| AC-12-05 | Admin wajib memilih satu penyedia untuk melanjutkan ke Step 3 | Tombol "Lanjut" disabled jika belum ada penyedia dipilih |
| AC-12-06 | Step 3 menampilkan ringkasan: desa, judul, penyedia, estimasi waktu | Semua data dari Step 1 & 2 tampil di halaman review |
| AC-12-07 | Admin dapat menekan "Kembali" antar step tanpa kehilangan data yang sudah diisi | Data dipertahankan di session/database |
| AC-12-08 | Admin dapat menyimpan draf di tahap manapun | Proyek tersimpan dengan status `draft` |
| AC-12-09 | Setelah menekan "Kirim ke Penyedia", proyek tersimpan dengan status `menunggu_konfirmasi_penyedia` | Status tersimpan benar di database |
| AC-12-10 | Sistem menampilkan peringatan jika desa yang dipilih sudah memiliki proyek aktif | Dialog konfirmasi muncul sebelum melanjutkan |
| AC-12-11 | Admin diredirect ke halaman detail proyek setelah berhasil mengirim | URL berubah ke `/proyek/{id}` dengan flash message sukses |

---

### 5.4 Alur Wizard

```
[Masuk ke /proyek/buat]
        │
        ▼
┌──────────────────┐
│ STEP 1           │
│ Informasi Dasar  │
│ - Pilih Desa     │
│ - Judul Proyek   │
│ - Deskripsi      │
│ - Foto (maks 5)  │
│ - Est. Tanggal   │
└────────┬─────────┘
         │ [Lanjut ▶] atau [Simpan Draf]
         ▼
┌──────────────────┐
│ STEP 2           │
│ Pilih Penyedia   │◄── integrasikan UC-10 (Rekomendasi)
│ - Daftar rekom.  │
│ - Filter energi  │
│ - Pilih 1 vendor │
└────────┬─────────┘
         │ [Lanjut ▶] atau [◀ Kembali] atau [Simpan Draf]
         ▼
┌──────────────────┐
│ STEP 3           │
│ Review & Kirim   │
│ - Ringkasan data │
│ - Konfirmasi     │
└────────┬─────────┘
         │ [Kirim ke Penyedia] atau [◀ Kembali] atau [Simpan Draf]
         ▼
   Status: menunggu_konfirmasi_penyedia
   + Notifikasi ke Penyedia
   + Redirect ke /proyek/{id}
```

---

### 5.5 Skema Database

```sql
CREATE TABLE proyeks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    desa_id BIGINT NOT NULL,
    penyedia_id BIGINT,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    jenis_energi ENUM('solar', 'mikro_hidro', 'lainnya'),
    estimasi_mulai DATE,
    estimasi_selesai DATE,
    status ENUM(
        'draft',
        'menunggu_konfirmasi_penyedia',
        'diterima_penyedia',
        'menunggu_review_admin',
        'aktif_funding',
        'eksekusi',
        'selesai',
        'ditolak'
    ) DEFAULT 'draft',
    created_by BIGINT,  -- user_id Admin
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (desa_id) REFERENCES desas(id),
    FOREIGN KEY (penyedia_id) REFERENCES penyedia_energis(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE proyek_fotos (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    proyek_id BIGINT NOT NULL,
    path VARCHAR(500),
    urutan TINYINT DEFAULT 1,
    created_at TIMESTAMP,

    FOREIGN KEY (proyek_id) REFERENCES proyeks(id) ON DELETE CASCADE
);
```

---

### 5.6 Routes & Controller

```
GET  /proyek/buat                     → ProyekController@create       (Step 1 form)
POST /proyek/buat/step-1              → ProyekController@saveStep1
GET  /proyek/buat/{proyekId}/step-2   → ProyekController@step2
POST /proyek/buat/{proyekId}/step-2   → ProyekController@saveStep2
GET  /proyek/buat/{proyekId}/review   → ProyekController@review        (Step 3)
POST /proyek/buat/{proyekId}/kirim    → ProyekController@kirimKePenyedia
POST /proyek/{proyekId}/draf          → ProyekController@simpanDraf
GET  /proyek/{id}                     → ProyekController@show
```

---

### 5.7 Validasi Form (Step 1)

```php
// app/Http/Requests/StoreProyekStep1Request.php

return [
    'desa_id'          => 'required|exists:desas,id',
    'judul'            => 'required|string|min:10|max:255',
    'deskripsi'        => 'required|string|min:50',
    'estimasi_mulai'   => 'required|date|after:today',
    'estimasi_selesai' => 'required|date|after:estimasi_mulai',
    'fotos.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    'fotos'            => 'nullable|array|max:5',
];
```

---

### 5.8 State Management Wizard

Data antar step disimpan menggunakan **Laravel Session** dengan key `proyek_wizard_{proyekId}`. Alternatifnya, setiap step langsung disimpan ke database dengan status `draft` sehingga data tidak hilang jika tab ditutup.

**Rekomendasi:** simpan langsung ke DB per step (lebih aman untuk multi-tab).

---

## 6. Kebutuhan Non-Fungsional Sprint 1

| Aspek | Target |
|-------|--------|
| Waktu respons halaman | ≤ 3 detik |
| Validasi form | Inline, real-time untuk field kritis |
| Akses | Hanya Admin (RBAC guard) |
| Upload foto | Maks 5 foto, maks 2MB/foto, format JPG/PNG/WEBP |
| Konsistensi UI | Tailwind CSS, ikuti komponen yang sudah ada di codebase |
| Error handling | Flash message untuk sukses/gagal, form tidak reset saat error |

---

## 7. Out of Scope Sprint 1

Fitur berikut **tidak** masuk Sprint 1 dan akan dikerjakan di Sprint 2:

- Publikasi proyek ke halaman crowdfunding publik (PBI-15 → Sprint 2)
- Pembayaran donasi dan tracking real-time
- Notifikasi email (bisa ditambahkan jika kapasitas memungkinkan)
- Penerimaan proyek oleh penyedia (PBI-14) — dikerjakan Nasywan Sprint 1 sebagai PBI-13 & 14

---

## 8. Definition of Done (DoD)

Sebuah PBI dinyatakan selesai jika:

- [ ] Semua Acceptance Criteria terpenuhi
- [ ] Kode sudah di-push ke branch feature masing-masing dan di-merge ke `develop`
- [ ] Tidak ada error fatal di environment lokal
- [ ] Dummy data sudah terseed dan fitur berjalan dengan data tersebut
- [ ] UI konsisten dengan desain Tailwind yang disepakati tim
- [ ] Validasi input berjalan sesuai spesifikasi
- [ ] Tidak ada data sensitif yang ter-expose di response

---

## 9. Referensi

- Proposal NusaTerang — Final (Kelompok D, PPL 2026)
- Sprint Backlog: Sprint 1 (6 April – 2 Mei 2026)
- FR-09, FR-11 (Dokumen Kebutuhan Fungsional)
- UC-10, UC-12 (Skenario Use Case)
