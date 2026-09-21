<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/profile.js"></script>
    <script src="js/date.js"></script>
    <script src="js/notification.js"></script>
    <script src="js/settings.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
</head>
<body class="bg-slate-100 h-screen overflow-hidden">
    <div class="flex h-full w-full overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-slate-950 text-white flex flex-col justify-between shrink-0">
            @include('layouts.sidebar')
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col">

            {{-- TOPBAR --}}
            <header class="flex items-center justify-between bg-white border-b border-slate-200 px-8 py-5">
                @include('layouts.topbar')
            </header>

            {{-- CONTENT --}}
            <main class="flex-1 px-8 py-4 space-y-4 overflow-y-auto">
                @yield('content')
                
            </main>
        </div>
    </div>
</body>
</html>