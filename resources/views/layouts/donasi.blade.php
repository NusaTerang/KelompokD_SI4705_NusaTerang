<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Donasi') — NusaTerang</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            background: #ffffff;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 800;
            font-size: 1.25rem;
            color: #1e293b;
            text-decoration: none;
        }
        .navbar-brand svg { width: 28px; height: 28px; }
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #475569;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .btn-donate-nav {
            background-color: #facc15; /* Yellow */
            color: #1e293b;
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            border: none;
        }

        /* Page Content */
        .page-container {
            flex: 1;
            padding: 2rem 1rem;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }
        .page-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2rem;
        }
        
        /* Step Indicator */
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        .step-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .step-item.done .step-circle {
            background-color: #10b981; /* Green */
            color: white;
        }
        .step-item.active .step-circle {
            background-color: #facc15; /* Yellow */
            color: #1e293b;
        }
        .step-item.pending .step-circle {
            background-color: #e2e8f0; /* Gray */
            color: #94a3b8;
        }
        .step-label {
            font-size: 0.9rem;
            font-weight: 600;
        }
        .step-item.done .step-label { color: #1e293b; }
        .step-item.active .step-label { color: #1e293b; }
        .step-item.pending .step-label { color: #94a3b8; }
        .step-line {
            width: 40px;
            height: 2px;
            background-color: #e2e8f0;
        }
        .step-line.active { background-color: #10b981; }

        /* Card */
        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        /* Footer */
        .footer {
            background-color: #1e3a8a; /* Dark Blue */
            color: white;
            padding: 4rem 2rem 2rem;
            margin-top: 4rem;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 2rem;
        }
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        .footer-brand svg { width: 28px; height: 28px; }
        .footer p {
            color: #bfdbfe;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .social-links {
            display: flex;
            gap: 0.5rem;
        }
        .social-link {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
        }
        .footer-col h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }
        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .footer-col a {
            color: #bfdbfe;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .footer-bottom {
            max-width: 1200px;
            margin: 3rem auto 0;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            color: #93c5fd;
            font-size: 0.8rem;
        }

        /* Buttons and Inputs */
        .btn-primary {
            background-color: #facc15;
            color: #1e293b;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            width: 100%;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #eab308;
        }
        .btn-outline {
            background-color: transparent;
            color: #1e3a8a;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            border: 1px solid #1e3a8a;
            width: 100%;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-outline:hover {
            background-color: #eff6ff;
        }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1e293b;
        }
        .form-control {
            width: 100%;
            padding: 1rem;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            color: #1e293b;
        }
        .form-control:focus {
            outline: none;
            border-color: #cbd5e1;
            background-color: #fff;
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .footer-content { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 1rem; text-align: center; }
            .step-indicator { flex-wrap: wrap; }
            .step-label { display: none; }
            .card { padding: 1.5rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <a href="/" class="navbar-brand">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="16" r="16" fill="#facc15"/>
                <path d="M10 22 L16 8 L22 22" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.5 17 H19.5" stroke="#1e293b" stroke-width="2" stroke-linecap="round"/>
            </svg>
            NusaTerang
        </a>
        <div class="nav-links">
            <a href="#">Proyek</a>
            <a href="#">Tentang Kami</a>
            <a href="#">Dampak</a>
            <a href="#">Berita</a>
        </div>
        <a href="/donasi" class="btn-donate-nav">Mulai Donasi</a>
    </nav>

    <div class="page-container">
        <h1 class="page-title">Donasi Proyek</h1>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step-item {{ $step >= 1 ? ($step > 1 ? 'done' : 'active') : 'pending' }}">
                <div class="step-circle">
                    @if($step > 1) ✓ @else 1 @endif
                </div>
                <span class="step-label">Step 1</span>
            </div>
            <div class="step-line {{ $step > 1 ? 'active' : '' }}"></div>
            <div class="step-item {{ $step >= 2 ? ($step > 2 ? 'done' : 'active') : 'pending' }}">
                <div class="step-circle">
                    @if($step > 2) ✓ @else 2 @endif
                </div>
                <span class="step-label">Pembayaran</span>
            </div>
            <div class="step-line {{ $step > 2 ? 'active' : '' }}"></div>
            <div class="step-item {{ $step >= 3 ? 'active' : 'pending' }}">
                <div class="step-circle">3</div>
                <span class="step-label">Konfirmasi</span>
            </div>
        </div>

        @if($errors->any())
            <div style="background:#fee2e2; color:#b91c1c; padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
                {{ $errors->first() }}
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div>
                <div class="footer-brand">
                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="16" cy="16" r="16" fill="#facc15"/>
                        <path d="M10 22 L16 8 L22 22" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.5 17 H19.5" stroke="#1e293b" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    NusaTerang
                </div>
                <p>Platform crowdfunding energi terbarukan pertama di Indonesia yang fokus pada pemberdayaan desa tertinggal, terdepan, dan terluar.</p>
                <div class="social-links">
                    <a href="#" class="social-link">in</a>
                    <a href="#" class="social-link">tw</a>
                    <a href="#" class="social-link">ig</a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Tautan Cepat</h4>
                <ul>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Cara Kerja</a></li>
                    <li><a href="#">Daftar Proyek</a></li>
                    <li><a href="#">Penyedia Energi</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Bantuan</h4>
                <ul>
                    <li><a href="#">Pusat Bantuan</a></li>
                    <li><a href="#">Kontak Kami</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© 2024 NusaTerang. Seluruh hak cipta dilindungi.</div>
            <div>Terdaftar dan diawasi oleh Otoritas Jasa Keuangan.</div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
