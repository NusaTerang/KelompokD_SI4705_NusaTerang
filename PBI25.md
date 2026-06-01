Story Sebagai Penyedia Energi, saya ingin mengisi dan mengirimkan laporan akhir proyek setelah instalasi selesai 100% agar status proyek berubah menjadi selesai dan semua pihak mendapat konfirmasi bahwa proyek telah berhasil dilaksanakan.

Precondition

Vendor sudah login dengan role Penyedia Energi

Proyek sudah berstatus eksekusi (PBI-20 selesai)

Vendor sudah submit update progres dengan persentase 100% dan status "Selesai" (PBI-21 selesai)

RBAC aktif (PBI-06 selesai)

Alur / Skenario

Happy path — submit laporan akhir:

Vendor memilih status "Selesai" di form update progres (PBI-21)

Sistem otomatis menampilkan form laporan akhir

Vendor mengisi form laporan akhir:

Deskripsi hasil pekerjaan keseluruhan

Kapasitas daya yang berhasil terpasang (kWh/kWp)

Upload foto dokumentasi akhir (min 1 foto)

Catatan tambahan (opsional)

Vendor klik "Submit Laporan Akhir"

Sistem menyimpan laporan akhir

Status proyek otomatis berubah dari eksekusi → selesai

Notifikasi terkirim ke Admin & semua Donatur proyek

Halaman monitoring menampilkan section laporan akhir

Alternatif — simpan draft laporan:

Vendor klik "Simpan Draft"

Laporan tersimpan, status proyek belum berubah

Vendor dapat melanjutkan kapan saja

Alternatif — vendor coba submit laporan dua kali:

Sistem menampilkan error "Laporan akhir sudah pernah disubmit"

Alternatif — foto tidak diunggah:

Sistem menampilkan error "Minimal 1 foto dokumentasi akhir wajib diunggah"

Alternatif — non-vendor coba akses:

Sistem menampilkan 403 Forbidden

Test Case

ID

Skenario

Input

Expected Output

Type

TC-01

Vendor berhasil submit laporan akhir

Semua field valid, min 1 foto

Laporan tersimpan, status proyek → selesai

Feature Test

TC-02

Status proyek otomatis berubah ke selesai

Submit laporan akhir

projects.status = selesai di DB

Feature Test

TC-03

Admin & donatur dapat notifikasi proyek selesai

Submit laporan akhir

Notifikasi ProyekSelesai terkirim ke Admin & donatur

Feature Test

TC-04

Simpan draft laporan

Klik "Simpan Draft"

Tersimpan is_draft = true, status proyek belum berubah

Feature Test

TC-05

Submit tanpa deskripsi

Field deskripsi kosong

Error "Deskripsi laporan wajib diisi"

Unit Test

TC-06

Submit tanpa foto

Field foto kosong

Error "Minimal 1 foto dokumentasi wajib diunggah"

Unit Test

TC-07

Submit laporan dua kali

Submit laporan di proyek yang sudah ada laporannya

Error "Laporan akhir sudah pernah disubmit"

Feature Test

TC-08

Non-vendor coba submit laporan

Login sebagai Admin

403 Forbidden

Feature Test

TC-09

Vendor submit laporan proyek vendor lain

Akses project_id bukan miliknya

403 Forbidden

Feature Test

TC-10

Section laporan akhir tampil di monitoring

Laporan berhasil disubmit

Halaman monitoring tampilkan section laporan akhir

Feature Test

berikut referensi designnya = 

<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Submit Laporan Akhir - NusaTerang Admin</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "on-error-container": "#93000a",
                      "surface-variant": "#e0e3e2",
                      "tertiary": "#006d37",
                      "surface-container-high": "#e6e9e8",
                      "on-secondary-fixed-variant": "#07497d",
                      "tertiary-fixed": "#7efba4",
                      "on-error": "#ffffff",
                      "surface-container": "#ebeeed",
                      "on-background": "#181c1c",
                      "surface-tint": "#6f5d00",
                      "surface": "#f7faf9",
                      "on-tertiary-fixed": "#00210c",
                      "secondary-fixed-dim": "#a0c9ff",
                      "inverse-on-surface": "#eef1f0",
                      "tertiary-container": "#72ef99",
                      "on-secondary": "#ffffff",
                      "primary-container": "#f9d423",
                      "surface-container-lowest": "#ffffff",
                      "primary": "#6f5d00",
                      "outline-variant": "#cfc6ac",
                      "on-surface-variant": "#4c4733",
                      "inverse-surface": "#2d3131",
                      "surface-dim": "#d7dbda",
                      "surface-container-highest": "#e0e3e2",
                      "surface-bright": "#f7faf9",
                      "on-primary-fixed-variant": "#544600",
                      "primary-fixed-dim": "#e8c404",
                      "error": "#ba1a1a",
                      "on-primary-fixed": "#221b00",
                      "background": "#f7faf9",
                      "on-tertiary": "#ffffff",
                      "tertiary-fixed-dim": "#61de8a",
                      "secondary-container": "#92c1fe",
                      "secondary": "#2d6197",
                      "surface-container-low": "#f1f4f3",
                      "primary-fixed": "#ffe16a",
                      "on-tertiary-container": "#006b36",
                      "on-tertiary-fixed-variant": "#005228",
                      "error-container": "#ffdad6",
                      "secondary-fixed": "#d2e4ff",
                      "outline": "#7e7760",
                      "on-secondary-fixed": "#001c37",
                      "inverse-primary": "#e8c404",
                      "on-surface": "#181c1c",
                      "on-secondary-container": "#144f84",
                      "on-primary-container": "#6d5b00",
                      "on-primary": "#ffffff"
              },
              "borderRadius": {
                      "DEFAULT": "0.25rem",
                      "lg": "0.5rem",
                      "xl": "0.75rem",
                      "full": "9999px"
              },
              "fontFamily": {
                      "headline": ["Plus Jakarta Sans", "sans-serif"],
                      "display": ["Plus Jakarta Sans", "sans-serif"],
                      "body": ["Inter", "sans-serif"],
                      "label": ["Inter", "sans-serif"]
              }
            }
          }
        }
    </script>
<style>
        .material-symbols-outlined {
          font-variation-settings:
          'FILL' 0,
          'wght' 400,
          'GRAD' 0,
          'opsz' 24
        }
        
        .material-symbols-outlined.filled {
          font-variation-settings:
          'FILL' 1,
          'wght' 400,
          'GRAD' 0,
          'opsz' 24
        }
        
        /* Ambient shadow for cards */
        .ambient-shadow {
            box-shadow: 0 16px 32px -4px rgba(24, 28, 28, 0.06);
        }
        
        /* Ghost border */
        .ghost-border {
            border: 1px solid rgba(207, 198, 172, 0.15);
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased flex h-screen overflow-hidden">
<!-- SideNavBar -->
<aside class="hidden md:flex flex-col h-full py-8 bg-[#0F4C81] dark:bg-slate-950 h-screen w-64 fixed left-0 top-0 overflow-y-auto no-border tonal-transition-via-bg-shifts shadow-2xl dark:shadow-none z-50">
<!-- Brand Logo -->
<div class="px-8 mb-10 text-2xl font-bold text-[#F9D423] font-['Plus_Jakarta_Sans'] font-medium tracking-tight">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined filled text-3xl">solar_power</span>
<div>
<h1 class="text-xl">NusaTerang</h1>
<p class="text-[10px] font-normal text-white/70 uppercase tracking-wider mt-0.5">Administrative Portal</p>
</div>
</div>
</div>
<!-- Navigation Links -->
<nav class="flex-1 flex flex-col gap-1 w-full font-['Plus_Jakarta_Sans'] font-medium tracking-tight">
<!-- Dashboard -->
<a class="flex items-center gap-4 text-white/70 hover:text-white pl-5 py-3 transition-colors hover:bg-white/10 transition-all duration-200 scale-95 active:opacity-80 transition-transform" href="#">
<span class="material-symbols-outlined">dashboard</span>
<span>Dashboard</span>
</a>
<!-- Proyek Energi (Active) -->
<a class="flex items-center gap-4 text-[#F9D423] font-bold border-l-4 border-[#F9D423] pl-4 py-3 bg-white/5 hover:bg-white/10 transition-all duration-200 scale-95 active:opacity-80 transition-transform" href="#">
<span class="material-symbols-outlined filled">bolt</span>
<span>Proyek Energi</span>
</a>
<!-- Data Desa -->
<a class="flex items-center gap-4 text-white/70 hover:text-white pl-5 py-3 transition-colors hover:bg-white/10 transition-all duration-200 scale-95 active:opacity-80 transition-transform" href="#">
<span class="material-symbols-outlined">location_city</span>
<span>Data Desa</span>
</a>
<!-- Penyedia Energi -->
<a class="flex items-center gap-4 text-white/70 hover:text-white pl-5 py-3 transition-colors hover:bg-white/10 transition-all duration-200 scale-95 active:opacity-80 transition-transform" href="#">
<span class="material-symbols-outlined">factory</span>
<span>Penyedia Energi</span>
</a>
</nav>
<!-- CTA & Footer Nav -->
<div class="px-5 mt-auto flex flex-col gap-4 w-full">
<button class="w-full bg-[#F9D423] text-[#0F4C81] font-bold py-3 rounded-lg hover:bg-white transition-colors duration-200 shadow-md">
                New Project
            </button>
<div class="flex flex-col gap-1 border-t border-white/10 pt-4 font-['Plus_Jakarta_Sans'] font-medium tracking-tight">
<a class="flex items-center gap-4 text-white/70 hover:text-white px-3 py-2 transition-colors hover:bg-white/10 rounded-lg" href="#">
<span class="material-symbols-outlined">settings</span>
<span class="text-sm">Settings</span>
</a>
<a class="flex items-center gap-4 text-white/70 hover:text-white px-3 py-2 transition-colors hover:bg-white/10 rounded-lg" href="#">
<span class="material-symbols-outlined">logout</span>
<span class="text-sm">Logout</span>
</a>
</div>
</div>
</aside>
<!-- Main Content Area -->
<div class="flex-1 flex flex-col ml-0 md:ml-64 w-full h-screen overflow-hidden bg-surface relative">
<!-- TopNavBar -->
<header class="flex justify-between items-center px-8 w-full bg-white/80 dark:bg-slate-900/80 backdrop-blur-md fixed top-0 right-0 md:left-64 h-16 z-40 bg-surface-container-low against surface flat-no-shadows">
<!-- Mobile Menu Toggle & Brand (Hidden on md) -->
<div class="flex items-center gap-4 md:hidden text-[#0F4C81] dark:text-[#F9D423] font-['Plus_Jakarta_Sans'] text-sm">
<button class="text-on-surface hover:text-[#F9D423] transition-colors focus-within:ring-2 focus-within:ring-[#0F4C81]">
<span class="material-symbols-outlined">menu</span>
</button>
<span class="font-bold text-lg hidden">NusaTerang Admin</span>
</div>
<!-- Breadcrumbs -->
<div class="hidden md:flex items-center gap-2 text-sm font-['Plus_Jakarta_Sans'] text-slate-500">
<a class="hover:text-[#0F4C81] transition-colors" href="#">Dashboard</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<a class="hover:text-[#0F4C81] transition-colors" href="#">Proyek Energi</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span class="text-[#0F4C81] font-semibold">Laporan Akhir</span>
</div>
<!-- Trailing Actions -->
<div class="flex items-center gap-4 text-[#0F4C81] dark:text-[#F9D423] font-['Plus_Jakarta_Sans'] text-sm">
<button class="text-slate-500 hover:text-[#F9D423] transition-colors focus-within:ring-2 focus-within:ring-[#0F4C81] p-2 rounded-full hover:bg-surface-container-low">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="text-slate-500 hover:text-[#F9D423] transition-colors focus-within:ring-2 focus-within:ring-[#0F4C81] p-2 rounded-full hover:bg-surface-container-low">
<span class="material-symbols-outlined">help</span>
</button>
<div class="w-8 h-8 rounded-full bg-surface-container-high overflow-hidden ml-2 border border-surface-variant cursor-pointer focus-within:ring-2 focus-within:ring-[#0F4C81]">
<!-- Using a robust prompt for the avatar just in case -->
<img alt="Administrator Profile" class="w-full h-full object-cover" data-alt="A professional headshot of a corporate administrator, looking directly at the camera with a subtle smile. High key lighting, modern bright background, clean corporate aesthetic, matching a premium sustainable tech platform UI." src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2gAkB0FlSL_MYzEChNv63cRIUuuiOpRlLToeIau6p6o7bm8_PYvKOxXC2p4hnFxPEP52b0t17l690YKZTB7jaCZFOuJEbSHJuVdmvK-ds67wPh7eOtg8E7QpMvtyfyM8pQVgKsWTd6YQx_EHhgodnjRGYM_wb3FsAmQqWFic_0xsO6YQpD5afUHztyyd13Yhi17vnukTz-APjF-O9a9dvsULmIYCkZ-ctLea8vdkJzIegEGlz_hEJnQiFdkTqM6hXm2PGcGMHcuKV"/>
</div>
</div>
</header>
<!-- Scrollable Canvas -->
<main class="flex-1 overflow-y-auto pt-24 pb-12 px-6 md:px-12 w-full max-w-5xl mx-auto">
<!-- Page Header -->
<div class="mb-10 max-w-3xl">
<h1 class="font-headline font-bold text-3xl md:text-4xl text-on-surface mb-3 tracking-tight">Submit Laporan Akhir</h1>
<p class="font-body text-on-surface-variant text-lg">Selesaikan pengerjaan proyek dan kirimkan laporan hasil akhir instalasi.</p>
</div>
<!-- Content Area - Nested Hierarchy -->
<div class="flex flex-col gap-8">
<!-- Card 1: Project Information Summary -->
<div class="bg-surface-container-lowest rounded-xl p-8 ambient-shadow relative overflow-hidden ghost-border">
<!-- Subtle decorative background accent -->
<div class="absolute -top-10 -right-10 w-40 h-40 bg-primary-container opacity-10 rounded-full blur-3xl pointer-events-none"></div>
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
<div class="flex items-start gap-4">
<div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-primary shrink-0">
<span class="material-symbols-outlined filled">solar_power</span>
</div>
<div>
<h2 class="font-headline font-bold text-xl text-on-surface mb-1">PLTS Desa Sukamaju</h2>
<div class="flex items-center gap-2 text-on-surface-variant font-body text-sm">
<span class="material-symbols-outlined text-[16px]">location_on</span>
<span>Kabupaten Sumba Timur, NTT</span>
</div>
</div>
</div>
<div class="flex items-center gap-3 bg-surface-container-low px-4 py-2 rounded-lg ghost-border">
<div class="w-2 h-2 rounded-full bg-tertiary"></div>
<span class="font-body font-medium text-on-surface text-sm">Instalasi 100% Selesai</span>
</div>
</div>
</div>
<!-- Card 2: Final Report Form -->
<div class="bg-surface-container-lowest rounded-xl ambient-shadow ghost-border overflow-hidden">
<div class="border-b border-surface-variant/50 px-8 py-6 bg-surface-container-low/30">
<h3 class="font-headline font-bold text-lg text-on-surface">Form Laporan Akhir</h3>
</div>
<form class="p-8 flex flex-col gap-8">
<!-- Input Group: Ringkasan -->
<div class="flex flex-col gap-2">
<label class="font-headline font-semibold text-on-surface text-sm" for="ringkasan">Ringkasan Hasil Pekerjaan</label>
<textarea class="w-full bg-surface-container-highest rounded-lg border-0 ghost-border px-4 py-3 font-body text-on-surface placeholder:text-on-surface-variant/50 focus:ring-2 focus:ring-secondary focus:bg-surface transition-all resize-y" id="ringkasan" placeholder="Jelaskan hasil pengerjaan proyek secara keseluruhan..." rows="4"></textarea>
</div>
<!-- Input Group: Kapasitas -->
<div class="flex flex-col gap-2">
<label class="font-headline font-semibold text-on-surface text-sm" for="kapasitas">Kapasitas Terpasang Akhir (kWp)</label>
<div class="relative">
<input class="w-full md:w-1/2 bg-surface-container-highest rounded-lg border-0 ghost-border px-4 py-3 font-body text-on-surface focus:ring-2 focus:ring-secondary focus:bg-surface transition-all" id="kapasitas" placeholder="0.00" type="number"/>
<div class="absolute inset-y-0 right-0 md:right-1/2 flex items-center pr-4 pointer-events-none text-on-surface-variant font-medium">
                                    kWp
                                </div>
</div>
</div>
<!-- Input Group: Upload -->
<div class="flex flex-col gap-2">
<label class="font-headline font-semibold text-on-surface text-sm">Dokumentasi Foto Akhir</label>
<div class="w-full border-2 border-dashed border-outline-variant/50 rounded-xl p-10 flex flex-col items-center justify-center text-center hover:bg-surface-container-low/50 transition-colors cursor-pointer group">
<div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant mb-4 group-hover:text-primary transition-colors">
<span class="material-symbols-outlined text-3xl">cloud_upload</span>
</div>
<p class="font-body font-medium text-on-surface mb-1">Tarik dan lepas foto dokumentasi di sini</p>
<p class="font-body text-sm text-on-surface-variant">(Min. 1 foto, Max. 5)</p>
</div>
</div>
<!-- Input Group: Catatan -->
<div class="flex flex-col gap-2">
<label class="font-headline font-semibold text-on-surface text-sm" for="catatan">Catatan Tambahan (Opsional)</label>
<textarea class="w-full bg-surface-container-highest rounded-lg border-0 ghost-border px-4 py-3 font-body text-on-surface placeholder:text-on-surface-variant/50 focus:ring-2 focus:ring-secondary focus:bg-surface transition-all resize-y" id="catatan" placeholder="Tambahkan catatan khusus jika ada..." rows="3"></textarea>
</div>
</form>
</div>
<!-- Action Footer -->
<div class="flex flex-col sm:flex-row-reverse items-center justify-start gap-4 mt-4 pt-6 border-t border-surface-variant/30">
<button class="w-full sm:w-auto px-8 py-3.5 bg-primary-container text-primary-fixed-variant font-headline font-bold rounded-xl hover:bg-primary-fixed transition-colors shadow-sm relative overflow-hidden group" type="button">
<div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        Submit Laporan Akhir
                    </button>
<button class="w-full sm:w-auto px-8 py-3.5 bg-surface-container-high text-on-surface font-headline font-medium rounded-xl hover:bg-surface-container-highest transition-colors ghost-border" type="button">
                        Batal
                    </button>
</div>
</div>
</main>
</div>
<!-- Success Toast Notification (Hidden by default, shown for UI demonstration) -->
<div class="fixed top-24 right-8 z-50 transform transition-all duration-300 translate-y-0 opacity-100 flex items-center gap-3 bg-surface-container-lowest px-4 py-3 rounded-xl ambient-shadow ghost-border border-l-4 border-l-tertiary" id="success-toast">
<div class="w-6 h-6 rounded-full bg-tertiary/20 flex items-center justify-center text-tertiary shrink-0">
<span class="material-symbols-outlined text-[16px] filled">check_circle</span>
</div>
<p class="font-body font-medium text-sm text-on-surface">Laporan Akhir Berhasil Dikirim</p>
<button class="ml-4 text-on-surface-variant hover:text-on-surface transition-colors" onclick="document.getElementById('success-toast').style.opacity='0'; setTimeout(()=&gt;document.getElementById('success-toast').style.display='none', 300)">
<span class="material-symbols-outlined text-[18px]">close</span>
</button>
</div>
<script>
        // Simple JS to hide the toast after 5 seconds for demonstration
        setTimeout(() => {
            const toast = document.getElementById('success-toast');
            if(toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.style.display = 'none', 300);
            }
        }, 5000);
    </script>
</body></html>