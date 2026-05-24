<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="Daftar akun NusaTerang untuk bergabung menerangi desa-desa di Indonesia.">
<title>Daftar Akun — NusaTerang</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
  body { font-family: 'Inter', sans-serif; }
</style>
</head>
<body class="min-h-screen bg-[#fafaf7]">
  <div class="min-h-screen flex">
    <!-- Panel Kiri: Brand -->
    <aside class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-[#1a4a6e] to-[#0d2f4a] text-white p-12 flex-col">
      <!-- Logo -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center shadow-lg">
          <i data-lucide="sun" class="w-6 h-6 text-[#1a4a6e]"></i>
        </div>
        <span class="text-xl font-bold tracking-tight">NusaTerang</span>
      </div>

      <!-- Hero -->
      <div class="flex-1 flex items-center justify-center">
        <div class="relative">
          <div class="absolute inset-0 -m-10 bg-yellow-400/10 rounded-full blur-2xl"></div>
          <div class="absolute inset-0 -m-6 bg-white/5 rounded-full"></div>
          <div class="relative w-80 h-80 rounded-3xl overflow-hidden shadow-2xl ring-4 ring-white/10">
            <img
              src="https://images.unsplash.com/photo-1509391366360-2e959784a276?auto=format&fit=crop&w=800&q=80"
              alt="Panel surya energi terbarukan"
              class="w-full h-full object-cover"
            />
          </div>
        </div>
      </div>

      <!-- Tagline -->
      <div class="space-y-3">
        <h2 class="text-3xl font-bold leading-tight">Mulai Perjalanan Keberlanjutan Anda</h2>
        <p class="text-white/70 text-base leading-relaxed max-w-md">
          Daftarkan diri Anda dan jadi bagian dari komunitas yang peduli pada masa depan energi bersih Indonesia.
        </p>
      </div>
    </aside>

    <!-- Panel Kanan: Form -->
    <main class="flex-1 lg:w-1/2 flex items-center justify-center p-6 sm:p-10">
      <div class="w-full max-w-lg">
        <!-- Logo mobile -->
        <div class="flex lg:hidden items-center justify-center gap-3 mb-6">
          <div class="w-10 h-10 rounded-full bg-yellow-400 flex items-center justify-center">
            <i data-lucide="sun" class="w-6 h-6 text-[#1a4a6e]"></i>
          </div>
          <span class="text-xl font-bold text-[#1a4a6e]">NusaTerang</span>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 sm:p-10 border border-gray-100">
          <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Daftar Akun</h1>
            <p class="text-gray-500 mt-2">Buat akun baru untuk memulai perjalanan Anda.</p>
          </div>

          <form method="POST" action="{{ route('register') }}" class="space-y-4" id="register-form">
            @csrf

            <!-- Nama Lengkap -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
              <div class="relative">
                <i data-lucide="user" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input
                  id="name"
                  name="name"
                  type="text"
                  value="{{ old('name') }}"
                  required
                  autocomplete="name"
                  placeholder="Nama lengkap Anda"
                  class="w-full pl-11 pr-4 py-3 bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-[#1a4a6e] transition @error('name') border-red-500 @enderror"
                />
              </div>
              @error('name')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                  <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                  {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <div class="relative">
                <i data-lucide="mail" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input
                  id="email"
                  name="email"
                  type="email"
                  value="{{ old('email') }}"
                  required
                  autocomplete="email"
                  placeholder="nama@email.com"
                  class="w-full pl-11 pr-4 py-3 bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-[#1a4a6e] transition @error('email') border-red-500 @enderror"
                />
              </div>
              @error('email')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                  <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                  {{ $message }}
                </p>
              @enderror
            </div>

            <!-- No. Telepon -->
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
              <div class="relative">
                <i data-lucide="phone" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input
                  id="phone"
                  name="phone"
                  type="tel"
                  value="{{ old('phone') }}"
                  placeholder="08xxxxxxxxxx"
                  class="w-full pl-11 pr-4 py-3 bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-[#1a4a6e] transition @error('phone') border-red-500 @enderror"
                />
              </div>
              @error('phone')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                  <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                  {{ $message }}
                </p>
              @enderror
            </div>

            <!-- Password grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <div class="relative">
                  <i data-lucide="lock" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                  <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Password"
                    class="w-full pl-11 pr-11 py-3 bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-[#1a4a6e] transition @error('password') border-red-500 @enderror"
                  />
                  <button type="button" onclick="togglePw('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                  </button>
                </div>
                @error('password')
                  <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                    {{ $message }}
                  </p>
                @enderror
              </div>
              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi</label>
                <div class="relative">
                  <i data-lucide="lock" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                  <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="w-full pl-11 pr-11 py-3 bg-gray-100 border border-transparent rounded-xl focus:outline-none focus:bg-white focus:border-[#1a4a6e] transition"
                  />
                  <button type="button" onclick="togglePw('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                  </button>
                </div>
              </div>
            </div>

            <!-- Submit -->
            <button
              type="submit"
              id="register-button"
              class="w-full py-3 bg-yellow-400 hover:bg-yellow-500 text-[#1a4a6e] font-bold rounded-xl shadow-md hover:shadow-lg transition mt-2"
            >
              Daftar Sekarang
            </button>

            <!-- Login link -->
            <p class="text-center text-sm text-gray-600">
              Sudah punya akun?
              <a href="{{ route('login') }}" class="text-[#1a4a6e] font-semibold hover:underline" id="login-link">Masuk</a>
            </p>

            <!-- Divider -->
            <div class="flex items-center gap-3 my-2">
              <div class="flex-1 h-px bg-gray-200"></div>
              <span class="text-xs text-gray-400 font-medium">ATAU DAFTAR DENGAN</span>
              <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <!-- Social -->
            <div class="grid grid-cols-2 gap-3">
              <button type="button" id="google-register-button" class="flex items-center justify-center gap-2 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                <span class="text-sm font-medium text-gray-700">Google</span>
              </button>
              <button type="button" id="facebook-register-button" class="flex items-center justify-center gap-2 py-3 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span class="text-sm font-medium text-gray-700">Facebook</span>
              </button>
            </div>

            <!-- Terms -->
            <p class="text-center text-xs text-gray-500 pt-2 leading-relaxed">
              Dengan mendaftar, Anda menyetujui
              <a href="#" class="text-[#1a4a6e] font-medium hover:underline">Syarat & Ketentuan</a>
              dan
              <a href="#" class="text-[#1a4a6e] font-medium hover:underline">Kebijakan Privasi</a>
              kami.
            </p>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script>
    lucide.createIcons();
    function togglePw(id, btn) {
      const input = document.getElementById(id);
      const isPw = input.type === 'password';
      input.type = isPw ? 'text' : 'password';
      btn.innerHTML = '';
      const i = document.createElement('i');
      i.setAttribute('data-lucide', isPw ? 'eye-off' : 'eye');
      i.className = 'w-5 h-5';
      btn.appendChild(i);
      lucide.createIcons();
    }
  </script>
</body>
</html>
