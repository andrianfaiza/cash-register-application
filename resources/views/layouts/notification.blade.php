<div class="relative inline-block" id="notificationContainer">
    {{-- Tombol Lonceng Notifikasi --}}
    <button type="button" onclick="toggleNotificationMenu()" id="btnNotification"
        class="relative p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors focus:outline-none"
        title="{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Notifications' : 'Notifikasi' }}">
        
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Badge Indicator --}}
        @if (($unreadNotificationCount ?? 0) > 0)
            <span id="notifBadge" class="absolute top-1.5 right-1.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-slate-950 animate-pulse">
                {{ $unreadNotificationCount }}
            </span>
        @else
            <span id="notifBadge" class="hidden absolute top-1.5 right-1.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-slate-950">
                0
            </span>
        @endif
    </button>

    {{-- Dropdown Pop-up Notifikasi --}}
    <div id="notificationDropdown"
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-800 z-50 overflow-hidden transition-all duration-200 transform origin-top-right">
        
        <!-- Header Dropdown -->
        <div class="flex items-center justify-between px-4 py-3 bg-slate-50/80 dark:bg-slate-950/80 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                    {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Notifications' : 'Notifikasi' }}
                </h3>
                <span id="notifCountPill" class="bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 text-[10px] font-bold px-2 py-0.5 rounded-full">
                    {{ ($unreadNotificationCount ?? 0) }} {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'New' : 'Baru' }}
                </span>
            </div>
            <button type="button" onclick="tandaiSemuaDibaca()" class="text-[11px] font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition focus:outline-none">
                {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Mark all as read' : 'Tandai dibaca' }}
            </button>
        </div>

        <!-- Daftar Notifikasi -->
        <div id="notificationList" class="divide-y divide-slate-100 dark:divide-slate-800 max-h-80 overflow-y-auto">
            @forelse ($appNotifications ?? [] as $notif)
                <a href="{{ $notif['url'] ?? '#' }}"
                    data-notif-id="{{ $notif['id'] ?? '' }}"
                    class="notif-item flex gap-3 p-3.5 transition-colors {{ ($notif['unread'] ?? false) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }} hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    
                    {{-- Icon Berdasarkan Tipe --}}
                    @if (($notif['type'] ?? '') === 'pending')
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    @elseif (($notif['type'] ?? '') === 'budget_alert')
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400 flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    @else
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                {{ ($appSettings->bahasa ?? 'id') === 'en' ? ($notif['title_en'] ?? $notif['title']) : $notif['title'] }}
                            </p>
                            @if ($notif['unread'] ?? false)
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0 unread-dot"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-snug line-clamp-2">
                            {{ $notif['message'] }}
                        </p>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 block">
                            {{ $notif['time'] }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-xs text-slate-400 dark:text-slate-500">
                    {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'No new notifications.' : 'Tidak ada notifikasi baru.' }}
                </div>
            @endforelse
        </div>

        <!-- Footer Dropdown -->
        <a href="{{ route('transaksi') }}" class="block text-center py-2.5 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-slate-50/80 dark:bg-slate-950/80 hover:bg-slate-100 dark:hover:bg-slate-800/80 border-t border-slate-100 dark:border-slate-800 transition">
            {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'View All Transactions' : 'Lihat Semua Transaksi' }} &rarr;
        </a>
    </div>
</div>