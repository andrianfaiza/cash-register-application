<!-- Wrapper bertipe relative agar posisi dropdown bertumpu pada tombol ini -->
<div class="relative inline-block w-full">

    <!-- Tombol Profil -->
    <button type="button" onclick="toggleAccountMenu()" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-800 transition w-full text-left">
        <img src="{{ auth()->user()?->foto ? (str_starts_with(auth()->user()->foto, 'foto-profil') ? asset('storage/' . auth()->user()->foto) : asset(auth()->user()->foto)) : asset('no-profile.jpg') }}" alt="{{ auth()->user()?->name ?? 'Pengguna' }}"  class="w-9 h-9 rounded-full object-cover">
        <div class="flex-1 min-w-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
            <p class="text-sm font-semibold leading-tight text-white truncate">{{ auth()->user()?->name ?? 'Pengguna' }}</p>
            <p class="text-xs text-slate-400 truncate">{{ auth()->user()?->departemen ?? 'Finance Admin' }}</p>
        </div>
        <!-- Ikon Panah (Opsional) -->
        <svg class="w-4 h-4 text-slate-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div id="accountDropdown" class="hidden absolute bottom-full mb-2 left-0 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50 transition-all">
        
        <!-- Header Ringkas -->
        <div class="px-4 py-2.5 border-b border-slate-100">
            <p class="text-xs text-slate-400">Masuk sebagai</p>
            <p class="text-xs font-bold text-slate-800 truncate">{{ auth()->user()?->email ?? '-' }}</p>
        </div>

        <!-- Menu Pilihan -->
        <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profil Saya
        </a>

        <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan Akun
        </a>

        <div class="border-t border-slate-100 my-1"></div>

        <!-- Tombol Logout Laravel -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-2.5 w-full text-left px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Keluar / Logout
            </button>
        </form>
    </div>

</div>