<style>
    .modal-backdrop, .modal-box { transition: opacity .2s ease, transform .2s ease; }
</style>

<div id="proyekModal" class="fixed inset-0 z-50 flex items-center justify-center hidden px-4" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div id="proyekModalBackdrop" onclick="closeProyekModal()" class="modal-backdrop fixed inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0"></div>

    {{-- Modal Box --}}
    <div id="proyekModalBox" class="modal-box relative bg-white dark:bg-slate-900 dark:border dark:border-slate-800 rounded-2xl shadow-xl w-full max-w-[750px] z-10 overflow-hidden transform scale-95 opacity-0">

        <form id="proyekForm" method="POST" action="{{ route('proyek.store') }}" onsubmit="return handleProyekSubmit(event)">
            @csrf

            <div class="max-h-[85vh] overflow-y-auto px-6 py-6 space-y-6">

                {{-- SECTION: IDENTITAS PROYEK --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 flex items-center justify-center rounded bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3.586a1 1 0 01.707.293l1.414 1.414a1 1 0 00.707.293H19a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 tracking-wide">IDENTITAS PROYEK</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                                NAMA PROYEK
                            </label>
                            <input type="text" name="nama_proyek" required placeholder="Masukkan nama proyek..."
                                class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                                KATEGORI PROYEK
                            </label>
                            <select name="kategori_proyek" required class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-600 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" selected disabled>Pilih kategori...</option>
                                <option value="infrastruktur">Infrastruktur</option>
                                <option value="teknologi">Teknologi &amp; Sistem</option>
                                <option value="renovasi">Renovasi</option>
                                <option value="k3">K3 &amp; Lingkungan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                            DESKRIPSI SINGKAT
                        </label>
                        <textarea name="deskripsi" rows="3" placeholder="Jelaskan cakupan proyek dan tujuan yang ingin dicapai..."
                            class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-slate-800">

                {{-- SECTION: ANGGARAN & JADWAL --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 flex items-center justify-center rounded bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 tracking-wide">ANGGARAN &amp; JADWAL</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                                ALOKASI PAGU ANGGARAN
                            </label>
                            <div class="flex items-center bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 focus-within:ring-2 focus-within:ring-blue-500">
                                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400 mr-2">Rp</span>
                                <input type="number" name="pagu_anggaran" min="0" step="1" placeholder="0"
                                    class="w-full bg-transparent text-sm font-medium text-slate-800 dark:text-slate-100 focus:outline-none">
                            </div>
                            <p class="text-xs text-orange-500 font-medium mt-1.5">Sisa Saldo Tersedia: Rp {{ number_format($remainingBudget ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                                TARGET WAKTU
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="date" name="tanggal_mulai"
                                    class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="text-slate-300 dark:text-slate-600">-</span>
                                <input type="date" name="tanggal_selesai"
                                    class="w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-slate-800">

                {{-- SECTION: PENANGGUNG JAWAB --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-6 h-6 flex items-center justify-center rounded bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </span>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 tracking-wide">PENANGGUNG JAWAB</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                                PROJECT LEAD
                            </label>
                            <button type="button" onclick="document.getElementById('projectLeadSelect').classList.toggle('hidden')"
                                class="w-full flex items-center gap-2 bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-400 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                                <span class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-300 text-xs">+</span>
                                Pilih personil...
                            </button>
                            <select id="projectLeadSelect" name="project_lead_id" class="hidden mt-2 w-full bg-slate-50 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih personil...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">
                                DEPARTEMEN TERKAIT
                            </label>
                            <div id="departemenTags" class="flex flex-wrap items-center gap-2">
                                <span class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-medium px-3 py-1.5 rounded-full">
                                    Logistik
                                    <input type="hidden" name="departemen[]" value="Logistik">
                                    <button type="button" onclick="this.closest('span').remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
                                </span>
                                <span class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-medium px-3 py-1.5 rounded-full">
                                    Operasional
                                    <input type="hidden" name="departemen[]" value="Operasional">
                                    <button type="button" onclick="this.closest('span').remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">&times;</button>
                                </span>
                                <button type="button" onclick="addDepartemen()"
                                    class="w-6 h-6 flex items-center justify-center rounded-full border border-slate-200 dark:border-slate-700 text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeProyekModal()" class="text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
                    Batal
                </button>

                <button type="submit" class="flex items-center gap-2 bg-slate-950 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                    Simpan Proyek
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/proyek-modul.js') }}"></script>