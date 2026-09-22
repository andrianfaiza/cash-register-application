@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('description', 'Pantau real-time likuiditas kas, pemasukan, pengeluaran, dan progres anggaran.')
@section('content')
    
{{-- SUMMARY CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    {{-- Saldo Konsolidasi --}}
    <div class="bg-slate-950 text-white rounded-xl p-5 border border-slate-800">
        <p class="text-xs font-semibold text-orange-500 tracking-wide">TOTAL SALDO KONSOLIDASI</p>
        <p class="text-2xl font-bold mt-3">Rp {{ number_format($saldoKonsolidasi, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 mt-3">
            Saldo bersih dari transaksi sukses
            <span class="text-orange-500">&bull; Likuiditas Sehat</span>
        </p>
    </div>

    {{-- Pemasukan --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide">TOTAL PEMASUKAN</p>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-3">Rp {{ number_format($pemasukan, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Dari {{ $jumlahTransaksiMasuk }} transaksi sukses</p>
    </div>

    {{-- Pengeluaran --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide">TOTAL PENGELUARAN</p>
        <p class="text-2xl font-bold text-red-500 dark:text-red-400 mt-3">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Dari {{ $jumlahPosKeluar }} pos operasional &amp; proyek</p>
    </div>
</div>

{{-- CHART + REKENING --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Tren Arus Kas Mingguan --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Tren Arus Kas Mingguan</h2>
            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Masuk</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500"></span> Keluar</span>
            </div>
        </div>

        <div class="flex items-end justify-between h-40 px-2">
            @foreach ($weeks as $week)
                <div class="flex flex-col items-center gap-2 w-16">
                    <div class="flex items-end gap-1.5 h-32">
                        <div class="w-3.5 rounded-t bg-emerald-500" style="height: {{ $week['masukHeight'] }}%"></div>
                        <div class="w-3.5 rounded-t bg-red-500" style="height: {{ $week['keluarHeight'] }}%"></div>
                    </div>
                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ $week['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Daftar Rekening Kas --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-4">Daftar Rekening Kas</h2>
        <div class="space-y-4">
            @forelse ($accounts as $account)
                <div class="pb-4 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-800' : '' }}">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $account->rekening_id }}</p>
                        <span class="text-[11px] font-medium text-orange-500">Saldo berjalan</span>
                    </div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Rekening transaksi</p>
                    <p class="text-sm font-bold text-slate-900 dark:text-slate-100 mt-1">Rp {{ number_format($account->saldo, 0, ',', '.') }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada transaksi rekening.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- TRANSAKSI TERBARU + ALOKASI PROYEK --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Transaksi Terbaru --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-4">Transaksi Terbaru</h2>
        <div class="space-y-4">
            @forelse ($recentTransactions as $transaction)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $transaction->deskripsi ?: 'Tanpa deskripsi' }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ ucfirst($transaction->kategori) }}</p>
                    </div>
                    <p class="text-sm font-semibold {{ $transaction->tipe === 'masuk' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                        {{ $transaction->tipe === 'masuk' ? '+' : '-' }}Rp {{ number_format($transaction->nominal, 0, ',', '.') }}
                    </p>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada transaksi.</p>
            @endforelse
        </div>
    </div>

    {{-- Alokasi Proyek Aktif --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-4">Alokasi Proyek Aktif</h2>
        <div class="space-y-4">
            @forelse ($activeProjects as $project)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $project->nama_proyek }}</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($project->spent, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500 rounded-full" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada proyek aktif.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection