<!-- Wrapper bertipe relative agar posisi dropdown bertumpu pada tombol ini -->
<div class="relative inline-block w-full">

    <!-- Tombol Profil -->
    <button type="button" onclick="toggleAccountMenu()" title="Profil {{ auth()->user()?->name ?? 'Pengguna' }}" class="flex w-full flex-col items-center gap-1 rounded-lg p-1.5 text-center transition-colors hover:bg-slate-900">
        <img src="{{ auth()->user()?->foto ? (str_starts_with(auth()->user()->foto, 'foto-profil') ? asset('storage/' . auth()->user()->foto) : asset(auth()->user()->foto)) : asset('no-profile.jpg') }}" onerror="this.onerror=null;this.src='{{ asset('no-profile.jpg') }}';" alt="{{ auth()->user()?->name ?? 'Pengguna' }}"  class="w-9 h-9 rounded-full object-cover">
        <span class="text-[9px] leading-3">Profil</span>
    </button>

    <!-- Dropdown Menu -->
    <div id="accountDropdown" class="hidden absolute bottom-0 left-full z-50 ml-2 w-56 rounded-xl border border-slate-200 bg-white py-1 shadow-xl transition-all dark:border-slate-800 dark:bg-slate-900">
        
        <!-- Header Ringkas -->
        <div class="border-b border-slate-100 px-4 py-2.5 dark:border-slate-800">
            <p class="text-xs text-slate-400">Masuk sebagai</p>
            <p class="truncate text-xs font-bold text-slate-800 dark:text-slate-200">{{ auth()->user()?->email ?? '-' }}</p>
        </div>

        <!-- Menu Pilihan -->
        <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-200 dark:hover:bg-slate-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profil Saya
        </a>

        <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-200 dark:hover:bg-slate-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan Akun
        </a>

        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>

        <!-- Tombol Logout Laravel -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2 text-left text-xs font-medium text-rose-600 transition hover:bg-rose-50 dark:hover:bg-rose-950/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar / Logout
            </button>
        </form>
    </div>

</div>