<button onclick="toggleNotificationMenu()" class="relative text-slate-500 hover:text-slate-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
<!-- Dropdown Pop-up Notifikasi -->
    <div id="notificationDropdown" class="hidden absolute mt-2 w-80 bg-white rounded-xl shadow-2xl border border-slate-100 z-50 overflow-hidden transition-all">
        
        <!-- Header Dropdown -->
        <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Notifikasi</h3>
                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">2 Baru</span>
            </div>
            <button type="button" class="text-[11px] font-medium text-indigo-600 hover:text-indigo-800 transition">
                Tandai dibaca
            </button>
        </div>

        <!-- Daftar Notifikasi -->
        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            
            <!-- Notifikasi 1 (Peringatan / Warning) -->
            <a href="#" class="flex gap-3 p-3 bg-amber-50/50 hover:bg-slate-50 transition">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800">Peringatan Anggaran</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-snug truncate">Proyek Hub Logistik telah mencapai 85% anggaran.</p>
                    <span class="text-[10px] text-slate-400 mt-1 block">10 menit lalu</span>
                </div>
            </a>

            <!-- Notifikasi 2 (Sukses / Verifikasi) -->
            <a href="#" class="flex gap-3 p-3 hover:bg-slate-50 transition">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800">Pemasukan Berhasil</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-snug truncate">Penerimaan Invoice #982 (Rp 150.000.000) diverifikasi.</p>
                    <span class="text-[10px] text-slate-400 mt-1 block">1 jam lalu</span>
                </div>
            </a>

            <!-- Notifikasi 3 (Info Umum) -->
            <a href="#" class="flex gap-3 p-3 hover:bg-slate-50 transition">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800">Laporan Bulanan Ready</p>
                    <p class="text-xs text-slate-500 mt-0.5 leading-snug truncate">Laporan kas periode Agustus sudah tersedia.</p>
                    <span class="text-[10px] text-slate-400 mt-1 block">Kemarin</span>
                </div>
            </a>

        </div>

        <!-- Footer Dropdown -->
        <a href="#" class="block text-center py-2.5 text-xs font-semibold text-indigo-600 bg-slate-50 hover:bg-slate-100 border-t border-slate-100 transition">
            Lihat Semua Notifikasi
        </a>
    </div>