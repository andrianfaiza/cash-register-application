@extends('layouts.app')

@section('title', 'Transaksi')
@section('page-title', 'Transaksi')
@section('description', 'Pencatatan kas masuk-keluar, kategori, dan tag proyek.')
@section('content')
            <main class="flex-1 overflow-y-auto px-8 py-5 space-y-5">

                {{-- FILTER BAR --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 my-4">
                        <!-- Filter 1: Rekening -->
                        <select name="rekening_id" onchange="this.form.submit()" form="transactionFilters" class="w-full text-xs sm:text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg px-2.5 sm:px-4 py-2 text-slate-600 dark:text-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Rekening</option>
                            @foreach ($accountOptions as $account)
                                <option value="{{ $account }}" @selected(request('rekening_id') === $account)>{{ $account }}</option>
                            @endforeach
                        </select>

                        <!-- Filter 2: Kategori -->
                        <select name="kategori" onchange="this.form.submit()" form="transactionFilters" class="w-full text-xs sm:text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg px-2.5 sm:px-4 py-2 text-slate-600 dark:text-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($categoryOptions as $category)
                                <option value="{{ $category }}" @selected(request('kategori') === $category)>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>

                        <!-- Filter 3: Status (Mengambil 2 kolom di mobile agar simetris) -->
                        <select name="status" onchange="this.form.submit()" form="transactionFilters" class="col-span-2 md:col-span-1 w-full text-xs sm:text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg px-2.5 sm:px-4 py-2 text-slate-600 dark:text-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="Sukses" @selected(request('status') === 'Sukses')>Status: Sukses</option>
                            <option value="Pending" @selected(request('status') === 'Pending')>Status: Pending</option>
                            <option value="Gagal" @selected(request('status') === 'Gagal')>Status: Gagal</option>
                        </select>

                        <form id="transactionFilters" method="GET" action="{{ route('transaksi') }}"></form>
                    </div>
                    <div class="flex justify-between items-center gap-2 shrink-0">
                        <h2 class="md:hidden text-sm font-semibold text-slate-800 dark:text-slate-200">Jurnal Transaksi Kas Perusahaan</h2>
                        <button
                            type="button"
                            onclick="toggleHapusMode()"
                            id="btnHapusTransaksi"
                            title="Hapus Transaksi"
                            class="flex items-center gap-2 bg-white dark:bg-slate-900 hover:bg-red-50 dark:hover:bg-red-950/50 text-red-500 border border-red-200 dark:border-red-900/50 hover:border-red-300 text-xs sm:text-sm font-medium px-3 md:px-4 py-2 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="inline">Hapus</span>
                        </button>
                            <button 
                                onclick="openModal()" 
                                type="button" 
                                class="
                                    /* --- Tampilan Mobile (Floating / Melayang) --- */
                                    fixed bottom-20 right-10 z-50 
                                    w-12 h-12 rounded-full 
                                    flex items-center justify-center 
                                    bg-blue-600 hover:bg-blue-500 
                                    shadow-xl shadow-blue-600/40 border border-blue-400/30 
                                    active:scale-95 transition-all
                                    
                                    /* --- Tampilan Desktop (Normal / In-line) --- */
                                    sm:static sm:z-auto 
                                    sm:w-auto sm:h-auto sm:rounded-lg 
                                    sm:inline-flex sm:gap-2 
                                    sm:bg-slate-950 dark:sm:bg-blue-600 
                                    sm:hover:bg-slate-800 dark:sm:hover:bg-blue-700 
                                    sm:shadow-none sm:border-none 
                                    sm:px-4 sm:py-2 
                                    
                                    /* --- Text & Utility --- */
                                    text-white text-sm font-medium
                                "
                            >
                                <!-- Icon Plus -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 sm:w-4 sm:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>

                                <!-- Teks (Hanya Muncul di Desktop) -->
                                <span class="hidden sm:inline">Tambah Transaksi Baru</span>
                            </button>
                    </div>
                </div>

                {{-- TABLE CARD --}}
                <div class="app-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col">
                    <div class="hidden md:block px-6 py-4 border-b border-slate-100 dark:border-slate-800">
                        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Jurnal Transaksi Kas Perusahaan</h2>
                    </div>

                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide bg-slate-50/50 dark:bg-slate-950/50">
                                    <th class="px-3 py-3 font-medium hapus-col hidden w-10">
                                        <input type="checkbox" id="selectAllTransaksi" onchange="toggleSelectAllTransaksi(this)" class="h-4 w-4 accent-red-600 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:ring-red-500">
                                    </th>
                                    <th class="px-6 py-3 font-medium">Tanggal</th>
                                    <th class="px-6 py-3 font-medium">Deskripsi</th>
                                    <th class="px-6 py-3 font-medium">Kategori</th>
                                    <th class="px-6 py-3 font-medium">Proyek</th>
                                    <th class="px-6 py-3 font-medium">Status</th>
                                    <th class="px-6 py-3 font-medium text-right">Nominal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($transactions as $transaction)
                                    <tr class="table-row-enter hover:bg-slate-50 dark:hover:bg-slate-800/50" style="--motion-delay: {{ min($loop->index * 30, 360) }}ms">
                                        <td class="px-3 py-4 hapus-col hidden">
                                            <input type="checkbox" name="transaksi_ids[]" value="{{ $transaction->id }}" class="transaksi-checkbox h-4 w-4 accent-red-600 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:ring-red-500">
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="shrink-0 w-8 h-8 rounded-full {{ $transaction->tipe === 'masuk' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400' }} flex items-center justify-center">
                                                    @if ($transaction->tipe === 'masuk')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <span>{{ format_app_date($transaction->tanggal) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">{{ $transaction->deskripsi ?: 'Tanpa deskripsi' }}</td>
                                        <td class="px-6 py-4 font-medium text-orange-500 whitespace-nowrap">{{ ucfirst($transaction->kategori) }}</td>
                                        <td class="px-6 py-4 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $transaction->project?->nama_proyek ?: 'Non-Proyek' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $transaction->status === 'Sukses' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400' : ($transaction->status === 'Pending' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-400' : 'bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-400') }}">
                                                {{ $transaction->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold whitespace-nowrap {{ $transaction->tipe === 'keluar' ? 'text-red-500 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                            {{ $transaction->tipe === 'keluar' ? '-' : '+' }}{{ format_currency($transaction->nominal) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-1 justify-end">
                                                <button
                                                    type="button"
                                                    title="Lihat Detail Transaksi"
                                                    onclick="openDetailModal({{ $transaction->id }})"
                                                    class="w-7 h-7 flex items-center justify-center rounded-md text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 transition-colors"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    title="Edit Transaksi"
                                                    onclick="editTransaksi({{ $transaction->id }})"
                                                    class="w-7 h-7 flex items-center justify-center rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/50 transition-colors"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="md:hidden space-y-3">
                        @foreach ($transactions as $transaction)
                            <div class="dashboard-card table-row-enter bg-white dark:bg-slate-900 rounded-xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm" style="--motion-delay: {{ min($loop->index * 30, 360) }}ms">
                                <div class="flex items-start gap-3">
                                    <label class="hapus-col hidden shrink-0 pt-1">
                                        <input type="checkbox" name="transaksi_ids[]" value="{{ $transaction->id }}" aria-label="Pilih transaksi {{ $transaction->id }} untuk dihapus" class="transaksi-checkbox h-5 w-5 accent-red-600 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 focus:ring-red-500">
                                    </label>
                                    <!-- Icon -->
                                    <div class="shrink-0 w-10 h-10 rounded-full {{ $transaction->tipe === 'masuk' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400' }} flex items-center justify-center">
                                        @if ($transaction->tipe === 'masuk')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                            </svg>
                                        @endif
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="font-medium text-slate-800 dark:text-slate-200 text-sm truncate">{{ $transaction->deskripsi ?: 'Tanpa deskripsi' }}</p>
                                            <span class="font-semibold text-sm shrink-0 {{ $transaction->tipe === 'keluar' ? 'text-red-500 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                                {{ $transaction->tipe === 'keluar' ? '-' : '+' }}{{ format_currency($transaction->nominal) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $transaction->project?->nama_proyek ?: 'Non-Proyek' }} &bull; {{ ucfirst($transaction->kategori) }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-slate-400 dark:text-slate-500">{{ format_app_date($transaction->tanggal) }}</span>
                                            <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $transaction->status === 'Sukses' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400' : ($transaction->status === 'Pending' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-400' : 'bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-400') }}">
                                                {{ $transaction->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                                    <button
                                        type="button"
                                        title="Lihat Detail Transaksi"
                                        onclick="openDetailModal({{ $transaction->id }})"
                                        class="flex-1 flex items-center justify-center gap-1 text-xs font-medium text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/50 py-2 rounded-lg transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </button>
                                    <button
                                        type="button"
                                        title="Edit Transaksi"
                                        onclick="editTransaksi({{ $transaction->id }})"
                                        class="flex-1 flex items-center justify-center gap-1 text-xs font-medium text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/50 py-2 rounded-lg transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- PAGINATION --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 md:px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                        <p class="text-xs text-slate-400">Menampilkan {{ $transactions->count() }} dari {{ $transactions->total() }} Transaksi</p>
                        <div class="flex items-center gap-2">
                            <a href="{{ $transactions->previousPageUrl() ?: '#' }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-800 {{ $transactions->onFirstPage() ? 'pointer-events-none opacity-40' : '' }}">
                                Sebelumnya
                            </a>
                            <span class="w-7 h-7 flex items-center justify-center rounded-md bg-slate-950 dark:bg-slate-800 text-white text-xs font-semibold">{{ $transactions->currentPage() }}</span>
                            <a href="{{ $transactions->nextPageUrl() ?: '#' }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-800 {{ $transactions->hasMorePages() ? '' : 'pointer-events-none opacity-40' }}">
                                Selanjutnya
                            </a>
                        </div>
                    </div>
                </div>

                @include('transaksi.create')
                @include('transaksi.detail')

                <form id="deleteTransaksiForm" method="POST" action="{{ route('transaksi.destroy') }}" class="hidden">
                    @csrf
                    @method('DELETE')
                    <div id="deleteTransaksiIds"></div>
                </form>

                <script id="transaksiData" type="application/json">@json($transaksiData)</script>
                <script>
                    window.transaksiRoutes = {
                        store: @json(route('transaksi.store')),
                        update: @json(url('transaksi')),
                        destroy: @json(route('transaksi.destroy')),
                    };
                </script>

@endsection