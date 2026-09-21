@extends('layouts.app')

@section('title', 'Transaksi')
@section('page-title', 'Transaksi')
@section('description', 'Pencatatan kas masuk-keluar, kategori, dan tag proyek.')
@section('content')
            <main class="flex-1 overflow-y-auto px-8 py-5 space-y-5">

                {{-- FILTER BAR --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <select name="rekening_id" onchange="this.form.submit()" form="transactionFilters" class="text-sm bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Rekening</option>
                            @foreach ($accountOptions as $account)
                                <option value="{{ $account }}" @selected(request('rekening_id') === $account)>{{ $account }}</option>
                            @endforeach
                        </select>
                        <select name="kategori" onchange="this.form.submit()" form="transactionFilters" class="text-sm bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            @foreach ($categoryOptions as $category)
                                <option value="{{ $category }}" @selected(request('kategori') === $category)>{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                        <select name="status" onchange="this.form.submit()" form="transactionFilters" class="text-sm bg-white border border-slate-200 rounded-lg px-4 py-2.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="Sukses" @selected(request('status') === 'Sukses')>Status: Sukses</option>
                            <option value="Pending" @selected(request('status') === 'Pending')>Status: Pending</option>
                            <option value="Gagal" @selected(request('status') === 'Gagal')>Status: Gagal</option>
                        </select>
                        <form id="transactionFilters" method="GET" action="{{ route('transaksi') }}"></form>
                    </div>
                    <div class="flex justife-between gap-3">
                        <button
                                type="button"
                                onclick="toggleHapusMode()"
                                id="btnHapusTransaksi"
                                title="Hapus Transaksi"
                                class="flex items-center gap-2 bg-white hover:bg-red-50 text-red-500 border border-red-200 hover:border-red-300 text-sm font-medium px-4 py-2.5 rounded-lg transition-colors"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        <button onclick="openModal()" type="button" class="inline-flex items-center gap-2 bg-slate-950 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2.5 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Transaksi Baru
                        </button>
                    </div>
                </div>

                {{-- TABLE CARD --}}
                <div class="bg-white rounded-xl border border-slate-200 flex flex-col">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h2 class="text-sm font-semibold text-slate-800">Jurnal Transaksi Kas Perusahaan</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide">
                                    <th class="px-3 py-3 font-medium hapus-col hidden w-10">
                                        <input type="checkbox" id="selectAllTransaksi" onchange="toggleSelectAllTransaksi(this)" class="rounded border-slate-300">
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
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($transactions as $transaction)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-4 hapus-col hidden">
                                            <input type="checkbox" name="transaksi_ids[]" value="{{ $transaction->id }}" class="transaksi-checkbox rounded border-slate-300">
                                        </td>
                                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $transaction->tanggal->format('d M Y') }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-800">{{ $transaction->deskripsi ?: 'Tanpa deskripsi' }}</td>
                                        <td class="px-6 py-4 font-medium text-orange-500 whitespace-nowrap">{{ ucfirst($transaction->kategori) }}</td>
                                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $transaction->project?->nama_proyek ?: 'Non-Proyek' }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $transaction->status === 'Sukses' ? 'bg-emerald-100 text-emerald-700' : ($transaction->status === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $transaction->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-semibold whitespace-nowrap {{ $transaction->tipe === 'keluar' ? 'text-red-500' : 'text-emerald-600' }}">
                                            {{ $transaction->tipe === 'keluar' ? '- ' : '+ ' }}Rp {{ number_format($transaction->nominal, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <button
                                                    type="button"
                                                    title="Edit Transaksi"
                                                    onclick="editTransaksi({{ $transaction->id }})"
                                                    class="w-7 h-7 flex items-center justify-center rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-slate-100">
                        <p class="text-xs text-slate-400">Menampilkan {{ $transactions->count() }} dari {{ $transactions->total() }} Transaksi</p>
                        <div class="flex items-center gap-2">
                            <a href="{{ $transactions->previousPageUrl() ?: '#' }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 px-3 py-1.5 rounded-md border border-slate-200 {{ $transactions->onFirstPage() ? 'pointer-events-none opacity-40' : '' }}">
                                Sebelumnya
                            </a>
                            <span class="w-7 h-7 flex items-center justify-center rounded-md bg-slate-950 text-white text-xs font-semibold">{{ $transactions->currentPage() }}</span>
                            <a href="{{ $transactions->nextPageUrl() ?: '#' }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 px-3 py-1.5 rounded-md border border-slate-200 {{ $transactions->hasMorePages() ? '' : 'pointer-events-none opacity-40' }}">
                                Selanjutnya
                            </a>
                        </div>
                    </div>
                </div>

                @include('transaksi.create')

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