<div>
    <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">@yield('page-title')</h1>
    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">@yield('description')</p>
</div>
<div class="flex items-center gap-4">
    <form action="{{ route('search') }}" method="GET" class="relative">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
        </span>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari transaksi, proyek..." class="pl-9 pr-4 py-2 w-64 bg-slate-50 border border-slate-200 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-100 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </form>
    @include('layouts.notification')
    
    <button id="btnCalendar" type="button" class="flex items-center gap-2 bg-slate-950 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span id="calendarText"></span>
    </button>
</div>

                