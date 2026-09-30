<!DOCTYPE html>
<html lang="{{ $appSettings->bahasa ?? 'id' }}" class="{{ ($appSettings->mode_tampilan ?? 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
        window.__APP_SETTINGS__ = {
            bahasa: "{{ $appSettings->bahasa ?? 'id' }}",
            mata_uang: "{{ $appSettings->mata_uang ?? 'idr' }}",
            format_tanggal: "{{ $appSettings->format_tanggal ?? 'dd/mm/yyyy' }}",
            mode_tampilan: "{{ $appSettings->mode_tampilan ?? 'light' }}"
        };
    </script>
    <script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/date.js') }}"></script>
    <script src="{{ asset('js/notification.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 h-screen overflow-hidden">
    <div class="flex h-full w-full overflow-hidden">

        {{-- SIDEBAR - Desktop only --}}
        <aside id="sidebar" class="hidden lg:flex lg:static w-16 group md:hover:w-64 transition-all duration-300 ease-in-out bg-slate-950 text-white flex flex-col justify-between shrink-0">
            @include('layouts.sidebar')
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
            <header class="flex items-center justify-between bg-white dark:bg-slate-950 dark:border-slate-800 border-b border-slate-200 px-4 md:px-8 py-3 md:py-5">
                @include('layouts.topbar')
            </header>

            <main class="flex-1 px-4 md:px-8 py-3 space-y-4 overflow-y-auto pb-20 lg:pb-0">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Search Bar (Hidden by default) -->
    <div id="mobileSearchBar" class="hidden sm:hidden fixed top-0 left-0 right-0 z-50 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 px-4 py-3 shadow-lg">
        <form action="{{ route('search') }}" method="GET" class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari transaksi, proyek..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-100 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="button" onclick="toggleSearchBar()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </form>
    </div>

    <script>
        function toggleSearchBar() {
            const bar = document.getElementById('mobileSearchBar');
            if (bar) {
                bar.classList.toggle('hidden');
                if (!bar.classList.contains('hidden')) {
                    bar.querySelector('input').focus();
                }
            }
        }
    </script>

    @include('layouts.mobile-nav')
</body>
</html>
