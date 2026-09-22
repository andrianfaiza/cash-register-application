<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center hidden px-4" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div id="detailModalBackdrop" onclick="closeDetailModal()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>

    {{-- Modal Box --}}
    <div id="detailModalBox" class="relative bg-white dark:bg-slate-900 dark:border dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-lg z-10 overflow-hidden transform scale-95 opacity-0 transition-all duration-200">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/50">
            <div class="flex items-center gap-3">
                <span id="detailBadgeTipe" class="inline-flex items-center justify-center w-9 h-9 rounded-xl font-bold text-sm"></span>
                <div>
                    <h3 class="font-bold text-base text-slate-800 dark:text-slate-100">Detail Transaksi</h3>
                    <p id="detailTanggal" class="text-xs text-slate-400"></p>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            {{-- Nominal Hero Card --}}
            <div class="bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 rounded-xl p-4 text-center">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Nominal Transaksi</p>
                <p id="detailNominal" class="text-2xl font-black"></p>
            </div>

            {{-- Grid Info --}}
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="bg-slate-50 dark:bg-slate-950/40 p-3 rounded-lg border border-slate-100 dark:border-slate-800/80">
                    <span class="text-slate-400 block mb-0.5">Kategori</span>
                    <span id="detailKategori" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/40 p-3 rounded-lg border border-slate-100 dark:border-slate-800/80">
                    <span class="text-slate-400 block mb-0.5">Proyek</span>
                    <span id="detailProyek" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/40 p-3 rounded-lg border border-slate-100 dark:border-slate-800/80">
                    <span class="text-slate-400 block mb-0.5">Rekening / Kas Sumber</span>
                    <span id="detailRekening" class="font-semibold text-slate-800 dark:text-slate-200 uppercase"></span>
                </div>
                <div class="bg-slate-50 dark:bg-slate-950/40 p-3 rounded-lg border border-slate-100 dark:border-slate-800/80">
                    <span class="text-slate-400 block mb-0.5">Status Verifikasi</span>
                    <span id="detailStatus" class="inline-block font-semibold px-2 py-0.5 rounded text-[11px]"></span>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <span class="text-xs font-semibold text-slate-400 block mb-1">Deskripsi</span>
                <p id="detailDeskripsi" class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-950/40 p-3 rounded-lg border border-slate-100 dark:border-slate-800/80 whitespace-pre-line"></p>
            </div>

            {{-- Bukti Transaksi --}}
            <div>
                <span class="text-xs font-semibold text-slate-400 block mb-1">Bukti Transaksi</span>
                <div id="detailBuktiContainer" class="bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800/80 rounded-lg p-3">
                    <p id="detailNoBukti" class="text-xs text-slate-400 italic">Tidak ada lampiran bukti transaksi.</p>
                    <div id="detailBuktiContent" class="hidden flex-col items-center gap-3">
                        <img id="detailBuktiImg" src="" alt="Bukti Transaksi" class="max-h-48 rounded border border-slate-200 dark:border-slate-700 object-contain hidden">
                        <a id="detailBuktiLink" href="#" target="_blank" class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Buka / Unduh Lampiran Bukti
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end bg-slate-50/50 dark:bg-slate-950/50">
            <button type="button" onclick="closeDetailModal()" class="bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
