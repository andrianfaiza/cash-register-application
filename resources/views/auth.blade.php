<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ ($mode ?? 'login') === 'register' ? 'Daftar' : 'Masuk' }} - KAS | PT. Winner Nusantara Jaya</title>

    {{-- Tailwind CSS (CDN) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Alpine: sembunyikan elemen ber- x-cloak sampai Alpine siap */
        [x-cloak] { display: none !important; }

        /* ============================================================
           Transisi dual-form (fade + slide horizontal).
           Kedua form menempati cell grid yang sama sehingga tinggi
           kartu selalu = form tertinggi (tanpa layout jumping).
           ============================================================ */
        .auth-form {
            opacity: 0;
            pointer-events: none;
            z-index: 1;
        }

        .auth-form.is-active {
            opacity: 1;
            transform: translateX(0);
            z-index: 2;
            pointer-events: auto;
        }

        #loginForm:not(.is-active)    { transform: translateX(-1.5rem); }
        #registerForm:not(.is-active) { transform: translateX(1.5rem); }

        /* Indikator pill tab: lebar setengah container dikurangi jarak padding */
        #tabIndicator { width: calc(50% - 0.25rem); }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }
    </style>

    <script>
        /* ============================================================
           State autentikasi untuk Alpine.js.
           Didefinisikan sebelum Alpine dimuat (Alpine pakai defer).
           ============================================================ */
        function authApp(initialMode) {
            return {
                mode: initialMode === 'register' ? 'register' : 'login',
                forgotOpen: false,
                showLoginPw: false,
                showRegisterPw: false,
                showConfirmPw: false,

                urls: {
                    login: @json(route('login.page')),
                    register: @json(route('register.page')),
                },

                copy: {
                    login: {
                        title: 'Selamat Datang',
                        subtitle: 'Masuk ke sistem kas perusahaan Winner Nusantara Jaya Tbk',
                    },
                    register: {
                        title: 'Buat Akun Baru',
                        subtitle: 'Daftarkan akun untuk mulai mengelola kas perusahaan.',
                    },
                },

                /* Pindah tab: ubah state mode, sinkronkan URL & judul halaman */
                setMode(next) {
                    this.mode = next === 'register' ? 'register' : 'login';

                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, '', this.urls[this.mode]);
                    }
                    document.title = (this.mode === 'register' ? 'Daftar' : 'Masuk')
                        + ' - KAS | PT. Winner Nusantara Jaya';
                },

                /* Jalankan ulang setiap kali `mode` berubah (x-effect) */
                syncForms(m) {
                    const apply = (id, active) => {
                        const el = document.getElementById(id);
                        if (!el) return;
                        el.setAttribute('aria-hidden', active ? 'false' : 'true');
                        if ('inert' in el) el.inert = !active; // cegah fokus keyboard ke form tersembunyi
                    };
                    apply('loginForm', m !== 'register');
                    apply('registerForm', m === 'register');
                },
            };
        }
    </script>

    {{-- Alpine.js (CDN, defer agar state sudah terdefinisi saat inisialisasi) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

@php
    $initialMode = $mode ?? 'login';

    // Pesan error per form (first() mengembalikan string kosong bila tidak ada).
    $loginError = $errors->first('username') ?: $errors->first('password');
    $registerError = $errors->first('name') ?: $errors->first('email') ?: $errors->first('password') ?: $errors->first('password_confirmation');

    // Jika ada input register yang dikirim ulang (validasi gagal), tampilkan tab register.
    if (filled(old('name')) || filled(old('password_confirmation')) || $errors->has('name') || $errors->has('password_confirmation')) {
        $initialMode = 'register';
    } elseif (filled(old('username')) || $errors->has('username')) {
        $initialMode = 'login';
    }
@endphp

<body class="min-h-screen bg-slate-100 text-slate-900 antialiased px-4 py-8 sm:px-6 sm:py-12 flex justify-center">

    <div id="authRoot"
         data-mode="{{ $initialMode }}"
         x-data="authApp('{{ $initialMode }}')"
         x-effect="syncForms(mode)"
         class="w-full max-w-md my-auto">

        {{-- ===================== KARTU UTAMA ===================== --}}
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xl shadow-slate-200/70 p-6 sm:p-8">

            {{-- Header: logo + nama aplikasi --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl bg-violet-800 flex items-center justify-center shadow-lg shadow-violet-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h11a2 2 0 012 2v1h1a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 13h2" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-900 leading-tight">KAS ERP</p>
                    <p class="text-xs text-slate-500 leading-tight">PT. Winner Nusantara Jaya Tbk</p>
                </div>
            </div>

            {{-- ============ TAB SWITCHER PILL (animated) ============ --}}
            <div class="relative grid grid-cols-2 p-1 mb-6 rounded-xl bg-slate-100 border border-slate-200"
                 role="tablist" aria-label="Pilih halaman autentikasi">
                <span id="tabIndicator" aria-hidden="true"
                      class="absolute left-1 top-1 bottom-1 rounded-lg bg-white border border-slate-200 shadow-sm transition-transform duration-300 ease-out"
                      style="transform: translateX(0);"
                      :style="mode === 'register' ? 'transform: translateX(100%)' : 'transform: translateX(0)'"></span>
                <button type="button" role="tab" id="tabLogin"
                        @click="setMode('login')"
                        :aria-selected="mode === 'login'"
                        :class="mode === 'login' ? 'text-slate-900' : 'text-slate-500'"
                        class="tab-btn relative z-10 py-2.5 text-sm font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-600">
                    Masuk
                </button>
                <button type="button" role="tab" id="tabRegister"
                        @click="setMode('register')"
                        :aria-selected="mode === 'register'"
                        :class="mode === 'register' ? 'text-slate-900' : 'text-slate-500'"
                        class="tab-btn relative z-10 py-2.5 text-sm font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-600">
                    Daftar
                </button>
            </div>

            {{-- Judul dinamis --}}
            <h1 id="authTitle" class="text-2xl font-bold text-slate-900 mb-1"
                x-text="copy[mode].title">{{ $initialMode === 'register' ? 'Buat Akun Baru' : 'Selamat Datang' }}</h1>
            <p id="authSubtitle" class="text-sm text-slate-500 mb-6"
               x-text="copy[mode].subtitle">{{ $initialMode === 'register'
                   ? 'Daftarkan akun untuk mulai mengelola kas perusahaan.'
                   : 'Masuk ke sistem kas perusahaan Winner Nusantara Jaya Tbk' }}</p>

            {{-- Status session --}}
            @if (session('status'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" role="status">
                    {{ session('status') }}
                </div>
            @endif

            {{-- ============ CONTAINER DUAL FORM (tinggi stabil) ============ --}}
            <div class="grid grid-cols-1">

                {{-- ---------------- FORM LOGIN ---------------- --}}
                <form id="loginForm"
                      class="auth-form col-start-1 row-start-1 space-y-5 transition-all duration-300 ease-out {{ $initialMode === 'login' ? 'is-active' : '' }}"
                      :class="{ 'is-active': mode === 'login' }"
                      method="POST" action="{{ route('login') }}">
                    @csrf

                    @if ($initialMode === 'login' && $loginError)
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600" role="alert">
                            {{ $loginError }}
                        </div>
                    @endif

                    {{-- Username / Email --}}
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Username / Email
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                value="{{ old('username') }}"
                                placeholder="email@perusahaan.co.id"
                                autocomplete="username"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:bg-white focus:border-violet-600 focus:ring-2 focus:ring-violet-600"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-slate-700">
                                Password
                            </label>
                            <a href="#" id="forgotLink"
                               @click.prevent="forgotOpen = !forgotOpen"
                               class="text-xs font-semibold text-violet-700 hover:text-violet-900 transition-colors">
                                Lupa password?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="••••••••••••••"
                                autocomplete="current-password"
                                :type="showLoginPw ? 'text' : 'password'"
                                class="w-full pl-10 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:bg-white focus:border-violet-600 focus:ring-2 focus:ring-violet-600"
                            >
                            <button
                                type="button"
                                @click="showLoginPw = !showLoginPw"
                                aria-label="Tampilkan atau sembunyikan password"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-violet-700 transition-colors"
                            >
                                <svg class="w-5 h-5" x-show="!showLoginPw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg class="w-5 h-5" x-show="showLoginPw" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p id="forgotHint"
                           x-show="forgotOpen"
                           x-cloak
                           x-transition.duration.150ms
                           class="mt-2 text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            Silakan hubungi administrator sistem untuk mereset kata sandi Anda.
                        </p>
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center gap-2 pt-1">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="w-4 h-4 rounded border-slate-300 accent-violet-800 cursor-pointer"
                        >
                        <label for="remember" class="text-sm text-slate-600 cursor-pointer select-none">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full py-3.5 mt-2 rounded-xl bg-violet-800 hover:bg-violet-900 text-white text-sm font-bold transition-all duration-200 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-600 focus-visible:ring-offset-2"
                    >
                        Masuk ke Dashboard
                    </button>
                </form>

                {{-- ---------------- FORM REGISTER ---------------- --}}
                <form id="registerForm"
                      class="auth-form col-start-1 row-start-1 space-y-5 transition-all duration-300 ease-out {{ $initialMode === 'register' ? 'is-active' : '' }}"
                      :class="{ 'is-active': mode === 'register' }"
                      method="POST" action="{{ route('register') }}">
                    @csrf

                    @if ($initialMode === 'register' && $registerError)
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600" role="alert">
                            {{ $registerError }}
                        </div>
                    @endif

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name') }}"
                                placeholder="Nama lengkap Anda"
                                autocomplete="name"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:bg-white focus:border-violet-600 focus:ring-2 focus:ring-violet-600"
                            >
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                placeholder="email@perusahaan.co.id"
                                autocomplete="email"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:bg-white focus:border-violet-600 focus:ring-2 focus:ring-violet-600"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password_register" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="password_register"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                :type="showRegisterPw ? 'text' : 'password'"
                                class="w-full pl-10 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:bg-white focus:border-violet-600 focus:ring-2 focus:ring-violet-600"
                            >
                            <button
                                type="button"
                                @click="showRegisterPw = !showRegisterPw"
                                aria-label="Tampilkan atau sembunyikan password"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-violet-700 transition-colors"
                            >
                                <svg class="w-5 h-5" x-show="!showRegisterPw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg class="w-5 h-5" x-show="showRegisterPw" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                                :type="showConfirmPw ? 'text' : 'password'"
                                class="w-full pl-10 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:bg-white focus:border-violet-600 focus:ring-2 focus:ring-violet-600"
                            >
                            <button
                                type="button"
                                @click="showConfirmPw = !showConfirmPw"
                                aria-label="Tampilkan atau sembunyikan konfirmasi password"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-violet-700 transition-colors"
                            >
                                <svg class="w-5 h-5" x-show="!showConfirmPw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg class="w-5 h-5" x-show="showConfirmPw" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full py-3.5 mt-2 rounded-xl bg-violet-800 hover:bg-violet-900 text-white text-sm font-bold transition-all duration-200 active:scale-[0.98] focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-600 focus-visible:ring-offset-2"
                    >
                        Daftar Sekarang
                    </button>
                </form>
            </div>

            <noscript>
                <p class="mt-4 text-xs text-center text-slate-500">
                    Aktifkan JavaScript untuk berpindah antara halaman Masuk dan Daftar.
                </p>
            </noscript>
        </div>

        {{-- Footer keamanan --}}
        <p class="mt-6 flex items-center justify-center gap-1.5 text-xs text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Dilindungi enkripsi SSL 256-bit
        </p>
    </div>
</body>
</html>
