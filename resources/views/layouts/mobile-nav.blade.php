<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 z-50" style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="flex items-center justify-around">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2 px-3 min-w-0 flex-1 {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-medium mt-0.5">Dashboard</span>
        </a>
        <a href="{{ route('proyek') }}" class="flex flex-col items-center py-2 px-3 min-w-0 flex-1 {{ request()->routeIs('proyek') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="text-[10px] font-medium mt-0.5">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Projects' : 'Proyek' }}</span>
        </a>
        <a href="{{ route('transaksi') }}" class="flex flex-col items-center py-2 px-3 min-w-0 flex-1 {{ request()->routeIs('transaksi') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span class="text-[10px] font-medium mt-0.5">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Transactions' : 'Transaksi' }}</span>
        </a>
        <a href="{{ route('laporan') }}" class="flex flex-col items-center py-2 px-3 min-w-0 flex-1 {{ request()->routeIs('laporan') ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9A9.002 9.002 0 0015 3.512V9h5.488z" />
            </svg>
            <span class="text-[10px] font-medium mt-0.5">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Reports' : 'Laporan' }}</span>
        </a>
    </div>
</nav>
