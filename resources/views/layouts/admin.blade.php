<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-headline { font-family: 'Plus+Jakarta+Sans', sans-serif; }
        .glass-header { backdrop-filter: blur(20px); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        :root {
            --solar-gold: #F9D423;
            --deep-navy: #0F4C81;
            --sustainability-green: #27AE60;
        }
    </style>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0F4C81",
                        "primary-container": "#F9D423",
                        "on-primary": "#ffffff",
                        "on-primary-container": "#0F4C81",
                        "secondary": "#0F4C81",
                        "secondary-container": "#92c1fe",
                        "on-secondary": "#ffffff",
                        "tertiary": "#27AE60",
                        "tertiary-container": "#72ef99",
                        "surface": "#f7faf9",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f1f4f3",
                        "surface-container": "#ebeeed",
                        "surface-container-high": "#e6e9e8",
                        "surface-container-highest": "#e0e3e2",
                        "on-surface": "#181c1c",
                        "on-surface-variant": "#4c4733",
                        "outline": "#7e7760",
                        "outline-variant": "#cfc6ac",
                        "solar-gold": "#F9D423",
                        "deep-navy": "#0F4C81",
                        "sustainability-green": "#27AE60"
                    },
                    fontFamily: {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Inter"],
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
</head>
<body class="bg-surface text-on-surface">

<aside class="h-screen w-64 fixed left-0 top-0 overflow-y-auto bg-[#0F4C81] flex flex-col py-6 px-4 z-40 no-scrollbar">
    <div class="mb-10 px-4">
        <h1 class="text-xl font-black text-white font-headline tracking-tight">NusaTerang</h1>
        <p class="text-[10px] uppercase tracking-widest text-white/50 font-bold">Renewable Energy</p>
    </div>
    <nav class="flex-1 space-y-2">
        <a class="flex items-center gap-3 px-4 py-3 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200" href="#">
            <span class="material-symbols-outlined text-xl">dashboard</span>
            <span class="text-sm font-headline tracking-wide">Dashboard</span>
        </a>
        <a class="flex items-center gap-3 px-4 py-3 bg-white/10 text-white rounded-lg font-bold transition-all duration-200" href="{{ route('proyek.create') }}">
            <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">wb_sunny</span>
            <span class="text-sm font-headline tracking-wide">Proyek Energi</span>
        </a>
        <!-- Add others as needed -->
    </nav>
</aside>

<header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-30 bg-white/80 backdrop-blur-md shadow-sm flex justify-between items-center px-8 h-16">
    <div class="flex items-center gap-2 text-sm text-on-surface-variant font-medium">
        <span class="hover:text-primary cursor-pointer">Dashboard</span>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="hover:text-primary cursor-pointer">Proyek Energi</span>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-[#0F4C81] font-bold">Admin</span>
    </div>
</header>

<main class="ml-64 pt-24 pb-12 px-8 min-h-screen">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
