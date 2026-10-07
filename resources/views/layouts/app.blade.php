<!DOCTYPE html>
<html lang="{{ $appSettings->bahasa ?? 'id' }}" class="{{ ($appSettings->mode_tampilan ?? 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title')</title>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js" defer></script>
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
    <style>
        @keyframes dashboard-rise-in {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes chart-scale-in {
            from { opacity: 0; transform: scale(.96); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes table-row-in {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-enter {
            animation: dashboard-rise-in 420ms cubic-bezier(.16, 1, .3, 1) backwards;
            animation-delay: var(--motion-delay, 0ms);
        }

        .chart-enter {
            animation: chart-scale-in 460ms cubic-bezier(.16, 1, .3, 1) backwards;
            animation-delay: var(--motion-delay, 0ms);
        }

        .table-row-enter {
            animation: table-row-in 360ms ease-out backwards;
            animation-delay: var(--motion-delay, 0ms);
        }

        /* .app-content > *:not(script),
        .app-content > main > *:not(script) {
            animation: dashboard-rise-in 420ms cubic-bezier(.16, 1, .3, 1) both;
        } */

        .app-content > *:nth-child(2),
        .app-content > main > *:nth-child(2) {
            animation-delay: 60ms;
        }

        .app-content > *:nth-child(3),
        .app-content > main > *:nth-child(3) {
            animation-delay: 120ms;
        }

        .app-content > *:nth-child(4),
        .app-content > main > *:nth-child(4) {
            animation-delay: 180ms;
        }

        .app-card {
            animation: dashboard-rise-in 420ms cubic-bezier(.16, 1, .3, 1) backwards;
            transition: transform 180ms ease-out, box-shadow 180ms ease-out;
        }

        .dashboard-card {
            transition: transform 180ms ease-out, box-shadow 180ms ease-out;
        }

        .dark .project-budget-total {
            color: #fff;
        }

        .app-content .app-card:nth-of-type(2) { animation-delay: 70ms; }
        .app-content .app-card:nth-of-type(3) { animation-delay: 140ms; }

        .app-content tbody tr:not(.table-row-enter) {
            animation: table-row-in 360ms ease-out both;
        }

        .app-content tbody tr:not(.table-row-enter):nth-child(2) { animation-delay: 30ms; }
        .app-content tbody tr:not(.table-row-enter):nth-child(3) { animation-delay: 60ms; }
        .app-content tbody tr:not(.table-row-enter):nth-child(4) { animation-delay: 90ms; }
        .app-content tbody tr:not(.table-row-enter):nth-child(5) { animation-delay: 120ms; }
        .app-content tbody tr:not(.table-row-enter):nth-child(6) { animation-delay: 150ms; }

        @media (hover: hover) and (prefers-reduced-motion: no-preference) {
            .app-card:hover, .dashboard-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px -12px rgb(15 23 42 / .28);
            }
        }

        button, select {
            transition-duration: 180ms;
            transition-timing-function: ease-out;
        }

        /*
         * Ruang ekstra di bawah konten mobile agar tidak tertutup
         * bottom navigation (termasuk safe area iPhone / Android gesture).
         */
        #appContent {
            padding-bottom: calc(5.5rem + env(safe-area-inset-bottom));
        }

        @media (min-width: 1024px) {
            #appContent {
                padding-bottom: 2rem;
            }
        }

        button:active {
            transform: scale(.95);
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                animation-delay: 0ms !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
    <script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/date.js') }}"></script>
    <script src="{{ asset('js/notification.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.cashflowLineRevealPlugin = {
            id: 'cashflowLineReveal',
            beforeDatasetsDraw(chart) {
                if (!chart.chartArea) return;
                const { ctx, chartArea } = chart;
                const progress = chart.$lineRevealProgress ?? 0;
                ctx.save();
                ctx.beginPath();
                ctx.rect(chartArea.left, chartArea.top, chartArea.width * progress, chartArea.height);
                ctx.clip();
                chart.$lineRevealClipped = true;
            },
            afterDatasetsDraw(chart) {
                if (chart.$lineRevealClipped) {
                    chart.ctx.restore();
                    chart.$lineRevealClipped = false;
                }
            }
        };

        window.animateCashflowLine = function (chart) {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                chart.$lineRevealProgress = 1;
                chart.draw();
                return;
            }

            const startedAt = performance.now();
            const drawFrame = (timestamp) => {
                const linearProgress = Math.min((timestamp - startedAt) / 900, 1);
                chart.$lineRevealProgress = linearProgress * linearProgress * (3 - 2 * linearProgress);
                chart.draw();
                if (linearProgress < 1) requestAnimationFrame(drawFrame);
            };
            requestAnimationFrame(drawFrame);
        };
    </script>
</head>
<body class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 h-screen overflow-hidden">
    <div class="flex h-full w-full overflow-hidden">

        {{-- SIDEBAR - Desktop only --}}
        <aside id="sidebar" class="hidden lg:flex lg:static w-16 shrink-0 bg-slate-950 text-white flex-col justify-between">
            @include('layouts.sidebar')
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- TOPBAR --}}
            <header class="flex items-center justify-between bg-white dark:bg-slate-950 dark:border-slate-800 border-b border-slate-200 px-4 md:px-8 py-3 md:py-5">
                @include('layouts.topbar')
            </header>

            <main id="appContent" class="app-content flex-1 px-4 md:px-8 py-3 space-y-4 overflow-y-auto">
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
