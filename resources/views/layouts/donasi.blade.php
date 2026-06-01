<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Donasi untuk proyek elektrifikasi desa melalui platform NusaTerang">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Donasi') — NusaTerang</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --clr-primary:     #059669;
            --clr-primary-dk:  #047857;
            --clr-primary-lt:  #d1fae5;
            --clr-accent:      #f59e0b;
            --clr-bg:          #f0fdf4;
            --clr-surface:     #ffffff;
            --clr-border:      #e2e8f0;
            --clr-text:        #1e293b;
            --clr-muted:       #64748b;
            --clr-danger:      #ef4444;
            --clr-danger-lt:   #fef2f2;
            --clr-warning:     #f59e0b;
            --clr-warning-lt:  #fffbeb;
            --clr-success:     #10b981;
            --clr-success-lt:  #ecfdf5;
            --radius:          16px;
            --radius-sm:       10px;
            --shadow:          0 4px 24px rgba(0,0,0,.08);
            --shadow-lg:       0 8px 40px rgba(0,0,0,.14);
            --transition:      all .25s cubic-bezier(.4,0,.2,1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--clr-bg);
            color: var(--clr-text);
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background: var(--clr-surface);
            border-bottom: 1px solid var(--clr-border);
            padding: 0 1.5rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 8px rgba(0,0,0,.06);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: .625rem;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--clr-primary-dk);
        }
        .navbar-brand svg { width: 32px; height: 32px; }
        .navbar-right { display: flex; align-items: center; gap: 1rem; }
        .nav-user {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .875rem;
            color: var(--clr-muted);
        }
        .nav-user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: var(--clr-primary-lt);
            color: var(--clr-primary-dk);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
        }
        .btn-logout {
            background: none;
            border: 1px solid var(--clr-border);
            border-radius: 8px;
            padding: .375rem .875rem;
            font-size: .8rem;
            color: var(--clr-muted);
            cursor: pointer;
            font-family: inherit;
            transition: var(--transition);
        }
        .btn-logout:hover { border-color: var(--clr-danger); color: var(--clr-danger); }

        /* ── Step indicator ── */
        .step-bar {
            background: var(--clr-surface);
            border-bottom: 1px solid var(--clr-border);
            padding: 1rem 1.5rem;
        }
        .step-bar-inner {
            max-width: 680px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 0;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 18px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--clr-border);
            z-index: 0;
        }
        .step-item.done:not(:last-child)::after,
        .step-item.active:not(:last-child)::after {
            background: var(--clr-primary);
        }
        .step-circle {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid var(--clr-border);
            background: var(--clr-surface);
            color: var(--clr-muted);
            font-weight: 700;
            font-size: .875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            transition: var(--transition);
        }
        .step-item.done .step-circle {
            background: var(--clr-primary);
            border-color: var(--clr-primary);
            color: #fff;
        }
        .step-item.active .step-circle {
            background: var(--clr-primary);
            border-color: var(--clr-primary);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(5,150,105,.2);
        }
        .step-label {
            margin-top: .375rem;
            font-size: .72rem;
            font-weight: 600;
            color: var(--clr-muted);
            text-align: center;
        }
        .step-item.active .step-label,
        .step-item.done .step-label { color: var(--clr-primary-dk); }

        /* ── Page wrapper ── */
        .page-wrapper {
            max-width: 680px;
            margin: 2.5rem auto;
            padding: 0 1rem;
        }

        /* ── Card ── */
        .card {
            background: var(--clr-surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--clr-border);
            overflow: hidden;
        }
        .card-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid var(--clr-border);
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        .card-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--clr-primary-dk);
        }
        .card-header p {
            margin-top: .375rem;
            font-size: .9rem;
            color: var(--clr-muted);
        }
        .card-body { padding: 2rem; }

        /* ── Form ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block;
            font-size: .875rem;
            font-weight: 600;
            color: var(--clr-text);
            margin-bottom: .5rem;
        }
        .form-label .required { color: var(--clr-danger); margin-left: 2px; }
        .form-control {
            width: 100%;
            padding: .75rem 1rem;
            border: 1.5px solid var(--clr-border);
            border-radius: var(--radius-sm);
            font-size: .9375rem;
            font-family: inherit;
            color: var(--clr-text);
            background: var(--clr-surface);
            transition: var(--transition);
            outline: none;
        }
        .form-control:focus {
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(5,150,105,.15);
        }
        .form-control.is-invalid {
            border-color: var(--clr-danger);
        }
        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239,68,68,.15);
        }
        .invalid-feedback {
            font-size: .8rem;
            color: var(--clr-danger);
            margin-top: .375rem;
            display: flex;
            align-items: center;
            gap: .25rem;
        }
        .form-hint {
            font-size: .78rem;
            color: var(--clr-muted);
            margin-top: .375rem;
        }
        textarea.form-control { resize: vertical; min-height: 90px; }

        /* ── Amount chips ── */
        .amount-chips {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: .75rem;
        }
        .chip {
            padding: .375rem .875rem;
            border: 1.5px solid var(--clr-border);
            border-radius: 100px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            background: var(--clr-surface);
            color: var(--clr-muted);
            user-select: none;
        }
        .chip:hover { border-color: var(--clr-primary); color: var(--clr-primary); }
        .chip.selected {
            background: var(--clr-primary);
            border-color: var(--clr-primary);
            color: #fff;
        }

        /* ── Amount input prefix ── */
        .input-prefix-wrap {
            display: flex;
            border: 1.5px solid var(--clr-border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            transition: var(--transition);
        }
        .input-prefix-wrap:focus-within {
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(5,150,105,.15);
        }
        .input-prefix-wrap.is-invalid { border-color: var(--clr-danger); }
        .input-prefix {
            padding: .75rem 1rem;
            background: #f8fafc;
            border-right: 1.5px solid var(--clr-border);
            font-weight: 700;
            color: var(--clr-muted);
            font-size: .9rem;
            white-space: nowrap;
        }
        .input-prefix-wrap .form-control {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0;
        }
        .input-prefix-wrap .form-control:focus { box-shadow: none !important; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .875rem 1.75rem;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
        }
        .btn-primary {
            background: var(--clr-primary);
            color: #fff;
            width: 100%;
            margin-top: .5rem;
        }
        .btn-primary:hover { background: var(--clr-primary-dk); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(5,150,105,.3); }
        .btn-primary:active { transform: translateY(0); }
        .btn-outline {
            background: transparent;
            color: var(--clr-muted);
            border: 1.5px solid var(--clr-border);
        }
        .btn-outline:hover { border-color: var(--clr-primary); color: var(--clr-primary); }
        .btn-lg { padding: 1rem 2rem; font-size: 1.0625rem; }

        /* ── Alert ── */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-sm);
            border-left: 4px solid;
            margin-bottom: 1.25rem;
            font-size: .875rem;
        }
        .alert-danger { background: var(--clr-danger-lt); border-color: var(--clr-danger); color: #b91c1c; }

        /* ── Footer ── */
        .page-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: .8rem;
            color: var(--clr-muted);
            padding-bottom: 2rem;
        }
        .page-footer a { color: var(--clr-primary); text-decoration: none; }

        @media (max-width: 600px) {
            .card-header, .card-body { padding: 1.25rem; }
            .page-wrapper { margin-top: 1.5rem; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="{{ url('/') }}" class="navbar-brand">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="16" cy="16" r="16" fill="#059669"/>
                <path d="M10 22 L16 8 L22 22" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12.5 17 H19.5" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            </svg>
            NusaTerang
        </a>
        <div class="navbar-right">
            @auth
            <div class="nav-user">
                <div class="nav-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
            @endauth
        </div>
    </nav>

    <!-- Step Indicator -->
    <div class="step-bar">
        <div class="step-bar-inner">
            <div class="step-item {{ $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' }}">
                <div class="step-circle">
                    @if($step > 1) ✓ @else 1 @endif
                </div>
                <span class="step-label">Isi Data</span>
            </div>
            <div class="step-item {{ $step >= 2 ? ($step > 2 ? 'done' : 'active') : '' }}">
                <div class="step-circle">
                    @if($step > 2) ✓ @else 2 @endif
                </div>
                <span class="step-label">Pembayaran</span>
            </div>
            <div class="step-item {{ $step >= 3 ? 'active' : '' }}">
                <div class="step-circle">3</div>
                <span class="step-label">Konfirmasi</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="page-wrapper">
        @if($errors->has('payment'))
            <div class="alert alert-danger">⚠️ {{ $errors->first('payment') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="page-footer">
        🔒 Transaksi aman diproses oleh <a href="https://midtrans.com" target="_blank">Midtrans</a> &nbsp;·&nbsp;
        <a href="{{ url('/') }}">Kembali ke Beranda</a>
    </footer>

    @stack('scripts')
</body>
</html>
