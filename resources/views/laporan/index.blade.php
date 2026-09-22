@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')
@section('description', 'Analisis cash flow, pembagian pengeluaran, dan ekspor laporan.')
@section('content')

{{-- PERIODE & EXPORT BAR --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Periode Laporan:</span>
        <button class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2">
            {{ $start->translatedFormat('d F Y') }} - {{ $end->translatedFormat('d F Y') }}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </button>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('laporan.export-pdf') }}" target="_blank" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-200 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
            </svg>
            Ekspor PDF / Cetak
        </a>
        <a href="{{ route('laporan.export-excel') }}" class="flex items-center gap-2 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
            </svg>
            Ekspor Excel (CSV/XLS)
        </a>
    </div>
</div>

{{-- CHART + KOMPOSISI --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Fluktuasi Arus Kas Bersih --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-6">Fluktuasi Arus Kas Bersih (Net Cashflow)</h2>

        <div class="flex items-end justify-between h-40 px-1">
            @forelse ($flux as $f)
                <div class="flex flex-col items-center gap-2 flex-1">
                    <div class="w-4 rounded-t bg-slate-900 dark:bg-blue-600" style="height: {{ $f['height'] }}%"></div>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ $f['label'] }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada arus kas bulan ini.</p>
            @endforelse
        </div>
    </div>

    {{-- Komposisi Pengeluaran --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-5">Komposisi Pengeluaran</h2>
        <div class="space-y-4">
            @forelse ($composition as $k)
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500 mt-1.5"></span>
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ ucfirst($k['name']) }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Rp {{ number_format($k['amount'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $k['percent'] }}%</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada pengeluaran.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- RINGKASAN BUKU KAS BULANAN --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Ringkasan Buku Kas Bulanan</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide bg-slate-50/50 dark:bg-slate-950/50">
                    <th class="px-6 py-3 font-medium">Rekening Kas</th>
                    <th class="px-6 py-3 font-medium text-right">Saldo Awal</th>
                    <th class="px-6 py-3 font-medium text-right">Pemasukan</th>
                    <th class="px-6 py-3 font-medium text-right">Pengeluaran</th>
                    <th class="px-6 py-3 font-medium text-right">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($recap as $r)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ $r['account'] }}</td>
                        <td class="px-6 py-4 text-right text-slate-500 dark:text-slate-400 whitespace-nowrap">Rp {{ number_format($r['initial'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-emerald-600 dark:text-emerald-400 font-medium whitespace-nowrap">Rp {{ number_format($r['income'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-red-500 dark:text-red-400 font-medium whitespace-nowrap">Rp {{ number_format($r['expense'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900 dark:text-slate-100 whitespace-nowrap">Rp {{ number_format($r['final'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-4 text-sm text-slate-400">Belum ada data laporan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection