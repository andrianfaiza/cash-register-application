@extends('layouts.app')

@section('title', 'Hasil Pencarian')
@section('page-title', 'Hasil Pencarian')
@section('description', 'Menampilkan hasil pencarian untuk kata kunci: "' . $query . '"')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">
            Hasil Pencarian: "{{ $query }}"
        </h2>
        @if ($query === '')
            <p class="text-sm text-slate-500">Masukkan kata kunci pada kolom pencarian topbar untuk mencari transaksi atau proyek.</p>
        @else
            <p class="text-sm text-slate-500 mb-6">Ditemukan {{ $transactions->count() }} transaksi dan {{ $projects->count() }} proyek matching.</p>
        @endif
    </div>

    @if ($query !== '')
        {{-- TRANSAKSI MATCHES --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-md font-bold text-slate-800 dark:text-slate-200 mb-4">Transaksi Kas</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 uppercase border-b border-slate-100 dark:border-slate-700">
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Deskripsi</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse ($transactions as $t)
                            <tr>
                                <td class="py-3 text-slate-500 whitespace-nowrap">{{ $t->tanggal->format('d M Y') }}</td>
                                <td class="py-3 font-medium text-slate-800 dark:text-slate-200">{{ $t->deskripsi ?: 'Tanpa deskripsi' }}</td>
                                <td class="py-3 text-orange-500 whitespace-nowrap">{{ ucfirst($t->kategori) }}</td>
                                <td class="py-3">
                                    <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $t->status === 'Sukses' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right font-semibold whitespace-nowrap {{ $t->tipe === 'keluar' ? 'text-red-500' : 'text-emerald-600' }}">
                                    {{ $t->tipe === 'keluar' ? '- ' : '+ ' }}Rp {{ number_format($t->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400">Tidak ada transaksi yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PROYEK MATCHES --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-md font-bold text-slate-800 dark:text-slate-200 mb-4">Proyek Kas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($projects as $p)
                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $p->nama_proyek }}</p>
                        <p class="text-xs text-slate-400 mt-1 mb-3">{{ $p->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Pagu: Rp {{ number_format($p->pagu_anggaran, 0, ',', '.') }}</span>
                            <span class="text-orange-500 font-semibold">{{ ucfirst($p->kategori_proyek) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 col-span-3">Tidak ada proyek yang cocok.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection
