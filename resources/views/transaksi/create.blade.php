<style>
    .modal-backdrop, .modal-box { transition: opacity .2s ease, transform .2s ease; }
</style>

<div id="formModal" class="fixed inset-0 z-50 flex items-center justify-center hidden px-4" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div id="modalBackdrop" onclick="closeModal()" class="modal-backdrop fixed inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0"></div>

    {{-- Modal Box --}}
    <div id="modalBox" class="modal-box relative bg-white dark:bg-slate-900 dark:border dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-[600px] z-10 overflow-hidden transform scale-95 opacity-0">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/50">
            <div>
                <h3 id="modalTitle" class="font-bold text-lg text-slate-800 dark:text-slate-100">Tambah Transaksi Baru</h3>
            </div>
            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="transaksiForm" method="POST" action="{{ route('transaksi.store') }}" enctype="multipart/form-data" onsubmit="return handleTransaksiSubmit(event)">
            @csrf
            <input type="hidden" name="tipe" id="inputTipe" value="keluar">
            <input type="hidden" name="verifikasi_langsung" id="inputVerifikasi" value="1">

            <div class="max-h-[75vh] overflow-y-auto px-6 py-6 space-y-5">

                {{-- Info banner --}}
                <div class="flex items-start gap-2 bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 rounded-lg px-4 py-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Input detail transaksi kas masuk atau kas keluar.</p>
                </div>

                {{-- Tipe transaksi: Kas Keluar / Kas Masuk --}}
                <div class="grid grid-cols-2 gap-1 bg-slate-100 dark:bg-slate-950 rounded-lg p-1">
                    <button type="button" id="tabKeluar" onclick="setTipe('keluar')"
                        class="flex items-center justify-center gap-2 py-2 rounded-md text-sm font-semibold transition-colors bg-white dark:bg-slate-800 shadow text-slate-800 dark:text-slate-100">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Kas Keluar
                    </button>
                    <button type="button" id="tabMasuk" onclick="setTipe('masuk')"
                        class="flex items-center justify-center gap-2 py-2 rounded-md text-sm font-semibold transition-colors text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Kas Masuk
                    </button>
                </div>

                {{-- Nominal & Tanggal --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                            NOMINAL TRANSAKSI
                        </label>
                        <div class="flex items-center bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 focus-within:ring-2 focus-within:ring-blue-500">
                            <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 mr-2">Rp</span>
                            <input type="number" name="nominal" min="0" step="1" placeholder="0"
                                class="w-full bg-transparent text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                            TANGGAL TRANSAKSI
                        </label>
                        <div class="relative">
                            <input type="date" name="tanggal" value="{{ now()->format('Y-m-d') }}"
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg pl-3 pr-9 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Kategori & Proyek --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                            KATEGORI
                        </label>
                        <select name="kategori" id="inputKategori" required class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-600 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" selected disabled class="text-slate-400">Pilih kategori...</option>
                            <optgroup label="Kategori Kas Keluar" data-tipe="keluar">
                                <option value="operasional">Operasional</option>
                                <option value="proyek">Proyek</option>
                                <option value="gaji">Gaji &amp; Kompensasi</option>
                                <option value="pajak">Pajak &amp; Legalitas</option>
                            </optgroup>
                            <optgroup label="Kategori Kas Masuk" data-tipe="masuk" hidden>
                                <option value="pendapatan">Pendapatan / Penjualan</option>
                                <option value="proyek">Proyek</option>
                                <option value="bunga_bank">Bunga Bank</option>
                                <option value="injeksi_modal">Injeksi Modal</option>
                                <option value="pinjaman">Pinjaman</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                            PROYEK (OPTIONAL)
                        </label>
                        <select name="proyek_id" class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Non-Proyek</option>
                            @foreach ($projects ?? [] as $project)
                                <option value="{{ $project->id }}">{{ $project->nama_proyek }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                        DESKRIPSI TRANSAKSI
                    </label>
                    <textarea name="deskripsi" rows="3" placeholder="Contoh: Pembelian material semen 100 sak..."
                        class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                {{-- Rekening & Bukti --}}
                <div class="grid grid-cols-2 gap-4 items-start">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                            REKENING / KAS SUMBER
                        </label>
                        <select name="rekening_id" class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="kas-besar" selected>Kas Besar (Tunai)</option>
                            <option value="mandiri">Bank Mandiri Corp</option>
                            <option value="bca">BCA Giral Bisnis</option>
                            <option value="petty-cash">Petty Cash Kantor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                            BUKTI TRANSAKSI
                        </label>
                        <div class="flex items-center gap-2">
                            <label class="flex-1 flex flex-col items-center justify-center gap-1 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-lg py-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12v9m0-9l-3 3m3-3l3 3M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6" />
                                </svg>
                                <span id="fileNameLabel" class="text-xs text-slate-400 text-center px-2">Klik untuk upload</span>
                                <input type="file" name="bukti" accept="image/*,.pdf" class="hidden" onchange="updateFileName(this)">
                            </label>
                            <div class="w-11 h-11 shrink-0 flex items-center justify-center border border-slate-200 dark:border-slate-800 rounded-lg bg-slate-50 dark:bg-slate-950/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h.01M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeModal()" class="text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    Batal
                </button>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Verifikasi Langsung</span>
                    <button type="button" id="toggleVerifikasi" onclick="toggleVerifikasi()"
                        class="relative w-9 h-5 rounded-full transition-colors bg-emerald-500">
                        <span id="toggleKnob" class="absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform translate-x-4"></span>
                    </button>
                </label>

                <button type="submit" class="flex items-center gap-2 bg-slate-950 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                    Simpan Transaksi
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/transaksi-modul.js') }}"></script>