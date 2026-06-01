Story Sebagai Donatur dan Admin, saya ingin memantau status terkini proyek beserta riwayat update progres instalasi agar saya dapat melihat perkembangan nyata dari kontribusi yang sudah diberikan.

Precondition

Halaman dapat diakses tanpa login (publik)

Proyek sudah masuk fase eksekusi (PBI-20 selesai)

Vendor sudah pernah submit minimal 1 update progres (PBI-21 selesai)

Alur / Skenario

Happy path — akses halaman monitoring:

Pengguna membuka halaman /projects/{id}/monitoring

Sistem menampilkan:

Status terkini proyek (badge: Menunggu Dana / Eksekusi / Selesai)

Persentase progres terkini (progress bar)

Info desa & vendor

Timeline riwayat update progres (urut terbaru di atas)

Setiap item timeline menampilkan:

Tanggal update

Persentase saat itu

Deskripsi aktivitas

Foto lapangan (jika ada)

Jika proyek sudah selesai, section laporan akhir tampil di bagian bawah

Alternatif — belum ada update progres:

Timeline kosong dengan pesan "Vendor belum mengunggah update progres"

Alternatif — proyek tidak ditemukan:

Sistem menampilkan halaman 404

Mockup → Referensi: 4.5.5 Halaman Monitoring Proyek

Test Case

ID

Skenario

Input

Expected Output

Type

TC-01

Halaman monitoring dapat diakses tanpa login

Akses /projects/{id}/monitoring tanpa auth

Halaman tampil 200 OK

Feature Test

TC-02

Status proyek tampil sesuai kondisi

Proyek status eksekusi

Badge "Sedang Berjalan" tampil

Feature Test

TC-03

Status proyek selesai tampil benar

Proyek status selesai

Badge "Selesai" tampil, section laporan akhir muncul

Feature Test

TC-04

Riwayat update tampil kronologis

3 update tersimpan

Urut terbaru di atas

Feature Test

TC-05

Belum ada update progres

Proyek baru masuk eksekusi

Pesan "Vendor belum mengunggah update progres"

Feature Test

TC-06

Progress bar sesuai persentase terkini

Update terakhir 75%

Progress bar 75%

Feature Test

TC-07

Foto lapangan tampil di timeline

Update punya 3 foto

Ketiga foto tampil

Feature Test

TC-08

Proyek tidak ditemukan

Akses /projects/9999/monitoring

404 Not Found

Feature Test

berikut referensi codingan bagian progress nya = 

<html class="light" lang="id"><head></head><body class="bg-surface font-body text-on-surface antialiased overflow-x-hidden flex flex-col min-h-screen">```html


<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600&amp;family=Poppins:wght@600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-error": "#ffffff",
                        "error": "#ba1a1a",
                        "on-tertiary-fixed-variant": "#005228",
                        "inverse-on-surface": "#eef1f0",
                        "on-surface": "#181c1c",
                        "outline-variant": "#cfc6ac",
                        "background": "#f7faf9",
                        "surface-variant": "#e0e3e2",
                        "tertiary-fixed-dim": "#61de8a",
                        "secondary-fixed-dim": "#a0c9ff",
                        "surface-container-high": "#e6e9e8",
                        "surface-container-lowest": "#ffffff",
                        "surface-dim": "#d7dbda",
                        "secondary-container": "#92c1fe",
                        "on-secondary-fixed-variant": "#07497d",
                        "on-primary-fixed-variant": "#544600",
                        "inverse-primary": "#e8c404",
                        "error-container": "#ffdad6",
                        "on-primary-fixed": "#221b00",
                        "primary-fixed": "#ffe16a",
                        "inverse-surface": "#2d3131",
                        "tertiary": "#006d37",
                        "primary": "#6f5d00",
                        "on-secondary-container": "#144f84",
                        "tertiary-container": "#72ef99",
                        "on-primary-container": "#6d5b00",
                        "on-tertiary": "#ffffff",
                        "on-background": "#181c1c",
                        "surface": "#f7faf9",
                        "on-secondary-fixed": "#001c37",
                        "surface-bright": "#f7faf9",
                        "on-primary": "#ffffff",
                        "primary-container": "#f9d423",
                        "on-surface-variant": "#4c4733",
                        "surface-tint": "#6f5d00",
                        "secondary": "#0F4C81",
                        "on-tertiary-fixed": "#00210c",
                        "on-secondary": "#ffffff",
                        "primary-fixed-dim": "#e8c404",
                        "on-tertiary-container": "#006b36",
                        "surface-container-low": "#f1f4f3",
                        "surface-container-highest": "#e0e3e2",
                        "outline": "#7e7760",
                        "on-error-container": "#93000a",
                        "tertiary-fixed": "#7efba4",
                        "secondary-fixed": "#d2e4ff",
                        "surface-container": "#ebeeed"
                    },
                    fontFamily: {
                        "headline": ["Poppins", "sans-serif"],
                        "body": ["Inter", "sans-serif"],
                        "label": ["Inter", "sans-serif"],
                        "plus-jakarta": ["Plus Jakarta Sans", "sans-serif"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1rem", "3xl": "16px", "full": "9999px" },
                },
            },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        body {
            width: 100%;
            min-height: 100vh;
            margin: 0;
            position: relative;
            background-color: #f7faf9;
        }
        .timeline-line::before {
            content: '';
            position: absolute;
            left: 114px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e0e3e2;
            z-index: 0;
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
    </style>
<!-- TopNavBar (Public layout from SCREEN_4) -->
<nav class="fixed top-0 w-full flex justify-between items-center px-8 h-20 bg-white/80 backdrop-blur-md z-50 shadow-sm">
<div class="text-2xl font-bold text-slate-900 flex items-center gap-2 font-plus-jakarta">
<span class="text-[#F9D423] material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">wb_sunny</span>
            NusaTerang
        </div>
<div class="hidden md:flex items-center gap-8 font-plus-jakarta font-semibold text-sm">
<a class="text-slate-600 hover:text-[#F9D423] transition-colors" href="#">Beranda</a>
<a class="text-[#F9D423] font-bold border-b-2 border-[#F9D423] pb-1" href="#">Proyek</a>
<a class="text-slate-600 hover:text-[#F9D423] transition-colors" href="#">Penyedia Energi</a>
<a class="text-slate-600 hover:text-[#F9D423] transition-colors" href="#">Tentang</a>
</div>
<button class="bg-primary-container text-on-primary-container px-6 py-2.5 rounded-lg font-semibold hover:opacity-90 transition-all scale-95 active:scale-90 font-plus-jakarta">
            Mulai Donasi
        </button>
</nav>
<!-- Main Content (Full Width) -->
<main class="pt-28 px-10 pb-20 max-w-[1280px] mx-auto flex-grow">
<!-- Breadcrumb & Title -->
<div class="mb-10">
<nav class="flex items-center gap-2 text-[10px] font-bold text-on-surface-variant/60 uppercase tracking-widest mb-3">
<span>Dashboard</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span>Proyek Energi</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-secondary">PLTS Komunal Mandiri Tahap I</span>
</nav>
<h2 class="text-4xl font-headline font-bold text-on-surface tracking-tight">Monitoring Proyek: <br/>PLTS Komunal Mandiri Tahap I</h2>
</div>
<div class="flex gap-8">
<!-- Left Column: 65% -->
<div class="w-[65%] space-y-10">
<!-- Project Header Card -->
<div class="bg-white rounded-3xl p-8 shadow-sm border border-surface-container-high/60">
<div class="flex items-center gap-8">
<div class="w-56 h-40 rounded-2xl overflow-hidden flex-shrink-0">
<img alt="PLTS" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC5JdDqlbt8JPbkrJjHkAaTJwph_PdSUHTWp5rPM6RhhhoNx_rpX4vGrdyV8cU6VD8hbp01PFnGxcYpRFkglDugcIqsjPIBudv3HXbgqril4lebtu7nyii-t7VE7-sGG1Es7dO4Hgw1AZarqLTwd91dvfv-EjZKKmmT3Hjp2nAH5NIS_YN798Z93VBXKPR-7X78pvWh2vby9VgVi17uJkPHGSHYpntzD8SlZL8pReQas9WJLF_Gor2VT1s2WG6jDQMH28lVKHQJpUKr"/>
</div>
<div class="flex-grow">
<h3 class="text-2xl font-headline font-bold text-[#0F4C81] mb-2 leading-tight">Pembangkit Listrik Tenaga Surya</h3>
<div class="flex items-center gap-2 text-on-surface-variant text-sm">
<span class="material-symbols-outlined text-lg text-secondary">location_on</span>
<span class="font-semibold">Desa Sukamaju, Jawa Barat</span>
</div>
</div>
<div class="relative w-28 h-28 flex-shrink-0">
<svg class="w-full h-full progress-ring">
<circle class="progress-ring__circle" cx="56" cy="56" fill="transparent" r="48" stroke="#f1f4f3" stroke-width="8"></circle>
<circle class="progress-ring__circle" cx="56" cy="56" fill="transparent" r="48" stroke="#F9D423" stroke-dasharray="301.59" stroke-dashoffset="90.47" stroke-linecap="round" stroke-width="8"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="text-2xl font-extrabold font-plus-jakarta text-[#0F4C81]">70%</span>
<span class="text-[9px] font-extrabold uppercase tracking-widest text-on-surface-variant/60">Done</span>
</div>
</div>
</div>
</div>
<!-- Timeline Section -->
<div class="relative timeline-line">
<h4 class="text-xl font-headline font-bold text-on-surface mb-10 flex items-center gap-3">
<span class="w-1.5 h-7 bg-tertiary rounded-full"></span>
                        Timeline &amp; Progress Updates
                    </h4>
<div class="space-y-12 relative z-10">
<!-- Item 1 (Done) -->
<div class="flex items-start gap-8">
<div class="w-20 text-right pt-3 flex-shrink-0">
<p class="text-sm font-extrabold text-on-surface-variant">25 Okt</p>
<p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest">2023</p>
</div>
<div class="mt-3.5">
<div class="w-4 h-4 rounded-full bg-tertiary flex items-center justify-center ring-4 ring-tertiary/10">
<span class="material-symbols-outlined text-white text-[10px] font-bold">check</span>
</div>
</div>
<div class="flex-grow bg-surface-container-low/40 p-6 rounded-3xl border border-transparent">
<div class="flex justify-between items-center mb-4">
<span class="bg-tertiary-container text-on-tertiary-container text-[9px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">100% Selesai</span>
<span class="material-symbols-outlined text-tertiary text-xl">check_circle</span>
</div>
<h5 class="font-headline font-bold text-[#0F4C81] mb-2">Pondasi &amp; Rangka Penyangga</h5>
<p class="text-sm text-on-surface-variant leading-relaxed mb-4">Pemasangan seluruh struktur penyangga panel telah selesai dilakukan dan diuji beban sesuai spesifikasi teknis.</p>
<img alt="Construction" class="w-32 h-20 rounded-xl object-cover shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC-yBT0x5oV8O44KIDNwCP0RVHSTB7Q-1tOPZcYaWFFEOy7VmgP20Dl1RpyIUdOk-UjfmlrQTSZ6kr9qdHK3Q6vsParPhdR7Drzh4IAHTxk193ghi1Sck7SbRPUMx9dXk0VXxtwBenwN8jHPlKRZZDxlIWrMQokLf60z0PPCvTKCXk6Pc6Bj8XGDq1eZFV8NGOl6RVFwLEljY9zaOp6dC8GCBAv3lTrGfHmeyHUXsNslEzFm91YqxmTEQ7OuyczCbS9i3yM1e2FNszV"/>
</div>
</div>
<!-- Item 2 (Active) -->
<div class="flex items-start gap-8">
<div class="w-20 text-right pt-3 flex-shrink-0">
<p class="text-sm font-extrabold text-[#0F4C81]">05 Nov</p>
<p class="text-[10px] font-bold text-on-surface-variant/40 uppercase tracking-widest">2023</p>
</div>
<div class="mt-3.5">
<div class="w-4 h-4 rounded-full bg-[#F9D423] ring-4 ring-[#F9D423]/30 animate-pulse"></div>
</div>
<div class="flex-grow bg-white p-6 rounded-3xl shadow-lg shadow-[#0F4C81]/5 border border-[#F9D423]/30">
<div class="flex justify-between items-center mb-4">
<span class="bg-[#F9D423]/20 text-[#0F4C81] text-[9px] font-extrabold px-3 py-1 rounded-full uppercase tracking-widest">Sedang Berjalan</span>
<span class="material-symbols-outlined text-[#F9D423] text-xl animate-spin-slow">sync</span>
</div>
<h5 class="font-headline font-bold text-[#0F4C81] mb-2">Instalasi Modul Fotovoltaik</h5>
<p class="text-sm text-on-surface-variant leading-relaxed mb-4">Tim teknis sedang melakukan pemasangan 40 unit panel solar. Progres mencapai 45% dari total instalasi modul.</p>
<img alt="Installation" class="w-32 h-20 rounded-xl object-cover shadow-sm" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCFC1GICK51OefXEpiwuA826c6j1NIxELOuMUhWpmA1YDIjFMBepEYoX1p7l_rAGejtI1bOt4DmSfcFAj77KEnEJUAkm0aCi9Oo8_8GqLSzGlYBezjyUAWRwq-CF1bIC6FKMXzZWWYGDahrQjD2bN2ErlsNrHggQ1vlPgtex4DCDDq-WMGGXMW9VJ4_3Cans9e3dnCh9eZaZpnIo3sfsppgrIELQrdwuIWDNM6mAI5XDp5DPdNEcAT7eJXi5UIlfeoyoErBDg3Ydrop"/>
</div>
</div>
<!-- Item 3 (Upcoming) -->
<div class="flex items-start gap-8">
<div class="w-20 text-right pt-3 flex-shrink-0">
<p class="text-sm font-extrabold text-on-surface-variant/40">12 Nov</p>
<p class="text-[10px] font-bold text-on-surface-variant/30 uppercase tracking-widest">2023</p>
</div>
<div class="mt-3.5">
<div class="w-4 h-4 rounded-full bg-surface-container-highest ring-4 ring-white"></div>
</div>
<div class="flex-grow bg-surface-container-low/30 p-6 rounded-3xl opacity-60">
<div class="flex justify-between items-center mb-4">
<span class="bg-surface-container-highest text-on-surface-variant text-[9px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">Mendatang</span>
</div>
<h5 class="font-headline font-bold text-[#0F4C81] mb-2">Wiring &amp; Commissioning</h5>
<p class="text-sm text-on-surface-variant leading-relaxed">Penyambungan kabel sistem ke inverter pusat dan uji coba fungsional jaringan mikro-grid desa.</p>
</div>
</div>
</div>
</div>
</div>
<!-- Right Column: 35% -->
<div class="w-[35%] space-y-6">
<div class="sticky top-24 space-y-6">
<!-- Project Status Card -->
<div class="bg-[#0F4C81] rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
<div class="absolute -right-8 -top-8 w-24 h-24 bg-[#F9D423] opacity-10 rounded-full blur-2xl"></div>
<div class="flex justify-between items-center mb-6">
<span class="text-[9px] font-bold uppercase tracking-widest text-white/50">Project Status</span>
<span class="bg-[#F9D423] text-[#0F4C81] text-[9px] font-extrabold px-3 py-1 rounded-full">Instalasi Berjalan</span>
</div>
<div class="space-y-6">
<div>
<p class="text-[9px] uppercase tracking-widest text-white/40 mb-3 font-bold">Technical Partner</p>
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/10">
<span class="material-symbols-outlined text-[#F9D423] text-xl">foundation</span>
</div>
<p class="font-headline font-bold text-base">PT. Surya Nusantara</p>
</div>
</div>
<div class="grid grid-cols-2 gap-4 pt-6 border-t border-white/10">
<div>
<p class="text-[9px] uppercase tracking-widest text-white/40 mb-1 font-bold">Assignment</p>
<p class="text-sm font-bold">10 Okt 2023</p>
</div>
<div>
<p class="text-[9px] uppercase tracking-widest text-white/40 mb-1 font-bold">Completion</p>
<p class="text-sm font-bold text-[#F9D423]">15 Nov 2023</p>
</div>
</div>
</div>
</div>
<!-- Donor Card -->
<div class="bg-white rounded-3xl p-6 shadow-sm border border-surface-container-high/60">
<h4 class="font-headline font-bold text-lg text-on-surface mb-6">Dukung Proyek Ini</h4>
<div class="flex -space-x-3 mb-6">
<img alt="Donor" class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBZzpL8VL9-2etQAgtHeJNgVhOjDG7Ng2qYpXGKw4tgSOA9cpV3SyVZBAeKztlABMoPvDXp0Zy70ZDAqcFmhZKtMDz47C26SFEEjlEKmRMxMJgrBrHvYEzbz3YuoyQIc-b76TU1sM_ssG47DvBwNcsnnvHovmF9LXTp8Y9fCPnL9oan8L8DYkRzp5J4eZ2vw9NHrZbhSbKocwqlsD-g63LB0DKBARHFaEMuWZRRQL0KC_UlbOGBYVRG3uZhG65M_MUoITaDwxFX0_zG"/>
<img alt="Donor" class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlnMP18IE_OFjHyhs9m1JR9q02PGSGSLBseafrSE7UV2Ls7tAtSoQCvoD0d1K8MnnD96dMtvYHGqE4x_40i-mee3MufBYw9pYY7UpRmNd0hZAeCakd3kHy4_NuN0TCX_SmvBNButVpgJjOl0P4INps6mKg9Kh3DAb-GrfZ1mrpEVj2Pkz4Ny6FjjymCS7MneMfxw9rOMHBS6KMbN_KWXBjm1QDCqT5yw0hlb4iVIAA0fdMeDVgiYmdQdB-vumYDn7E9SsFoPOIxRrN"/>
<img alt="Donor" class="w-10 h-10 rounded-full border-2 border-white object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCh7HxG2aIYG2sPkaHt8b3AsxLfgm6RDSyYDJaM7VvXTNbfqOkZNnSUpztFhzk-ZSyTBBQ3ySJXEnz2ghA-C-RV_DecdB82OaTtQNjxH8IgSK6uUtLfILp5gQyzYtdjHlG4XZzYb877nj0YK-ro5Q7MFwJp5Jg-HwenWwrvbrZYnjecZFOA1xlM1fN71j600H3eKBA8mAmqdmtVgaVrLq9lwlUcvTjt4qE03JjIRI-HjFkQYwfKZWcET5KEJu9FuJ0e16YYBYzGuVKh"/>
<div class="w-10 h-10 rounded-full border-2 border-white bg-primary-fixed flex items-center justify-center text-[#0F4C81] text-[8px] font-black">
                                +1.2k
                            </div>
</div>
<p class="text-xs text-on-surface-variant font-medium leading-relaxed mb-8">
<span class="text-[#0F4C81] font-bold">1,248 donatur</span> telah berkontribusi untuk menerangi desa ini.
                        </p>
<button class="w-full bg-[#F9D423] text-[#0F4C81] font-bold py-4 rounded-xl shadow-lg shadow-[#F9D423]/10 hover:shadow-xl transition-all flex items-center justify-center gap-2 text-sm">
<span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">volunteer_activism</span>
                            Donasi Sekarang
                        </button>
</div>
<!-- Environmental Impact Card -->
<div class="bg-tertiary-container/30 border border-tertiary/10 p-6 rounded-3xl flex items-center gap-4">
<div class="w-12 h-12 bg-tertiary rounded-full flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-white text-2xl">eco</span>
</div>
<div>
<p class="text-[8px] font-extrabold text-on-tertiary-container uppercase tracking-widest mb-0.5">Impact</p>
<p class="text-sm font-bold text-tertiary leading-tight">Kurangi 12.5 Ton CO2/Tahun</p>
</div>
</div>
</div>
</div>
</div>
</main>
<!-- Footer from SCREEN_8 -->
<footer class="bg-secondary text-white py-16 px-8">
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
<div class="col-span-1 md:col-span-2">
<div class="text-2xl font-bold text-white flex items-center gap-2 mb-6">
<span class="material-symbols-outlined text-[#F9D423]" style="font-variation-settings: 'FILL' 1;">wb_sunny</span>
<span class="font-headline font-extrabold tracking-tight">NusaTerang</span>
</div>
<p class="text-white/70 max-w-sm mb-8 font-body">Platform crowdfunding energi terbarukan pertama di Indonesia yang fokus pada pemberdayaan desa tertinggal, terdepan, dan terluar.</p>
<div class="flex gap-4">
<a class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary-container hover:text-secondary transition-all" href="#">
<span class="material-symbols-outlined text-sm">social_leaderboard</span>
</a>
<a class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary-container hover:text-secondary transition-all" href="#">
<span class="material-symbols-outlined text-sm">camera</span>
</a>
<a class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary-container hover:text-secondary transition-all" href="#">
<span class="material-symbols-outlined text-sm">alternate_email</span>
</a>
</div>
</div>
<div>
<h4 class="font-headline font-bold text-lg mb-6">Tautan Cepat</h4>
<ul class="flex flex-col gap-4 text-white/70 font-body text-sm">
<li><a class="hover:text-primary-container transition-colors" href="#">Tentang Kami</a></li>
<li><a class="hover:text-primary-container transition-colors" href="#">Cara Kerja</a></li>
<li><a class="hover:text-primary-container transition-colors" href="#">Daftar Proyek</a></li>
<li><a class="hover:text-primary-container transition-colors" href="#">Penyedia Energi</a></li>
</ul>
</div>
<div>
<h4 class="font-headline font-bold text-lg mb-6">Bantuan</h4>
<ul class="flex flex-col gap-4 text-white/70 font-body text-sm">
<li><a class="hover:text-primary-container transition-colors" href="#">Pusat Bantuan</a></li>
<li><a class="hover:text-primary-container transition-colors" href="#">Kontak Kami</a></li>
<li><a class="hover:text-primary-container transition-colors" href="#">Kebijakan Privasi</a></li>
<li><a class="hover:text-primary-container transition-colors" href="#">Syarat &amp; Ketentuan</a></li>
</ul>
</div>
</div>
<div class="max-w-7xl mx-auto mt-16 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-white/40">
<p>© 2024 NusaTerang. Seluruh hak cipta dilindungi.</p>
<p>Terdaftar dan diawasi oleh Otoritas Jasa Keuangan.</p>
</div>
</footer>
```</body></html>


, tetapi fokus aja pada bagian Timeline & Progress Update dimana menggunakan line progress vertical kebawah gitu dan ada gambarnya , sesuai dengan update progress yang dilakukan oleh penyedia energi yang dapat attach gambar. Untuk bagian utama tetap gunakan dari halaman proyek detail aja. fokus ambil referensi untuk bagian timeline & update progressnya aja.