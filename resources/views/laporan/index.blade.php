@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')
@section('description', 'Analisis cash flow, pembagian pengeluaran, dan ekspor laporan.')
@section('content')

{{-- PERIODE & EXPORT BAR --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-3">
        <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Report Period:' : 'Periode Laporan:' }}</span>
        <button class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-4 py-2">
            {{ format_app_date($start) }} - {{ format_app_date($end) }}
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
            {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Export PDF / Print' : 'Ekspor PDF / Cetak' }}
        </a>
        <a href="{{ route('laporan.export-excel') }}" class="flex items-center gap-2 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg px-4 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
            </svg>
            {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Export Excel (CSV)' : 'Ekspor Excel (CSV/XLS)' }}
        </a>
    </div>
</div>

{{-- CHART + KOMPOSISI --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Fluktuasi Arus Kas Bersih --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Net Cashflow Fluctuation' : 'Fluktuasi Arus Kas Bersih (Net Cashflow)' }}</h2>
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Net Cashflow' : 'Arus Kas Bersih' }}
            </div>
        </div>

        @if (count($flux) > 0)
            <div class="relative h-48 w-full">
                <canvas id="netCashflowChart"></canvas>
            </div>
        @else
            <div class="flex items-center justify-center h-48 text-sm text-slate-400">
                {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'No cashflow recorded this month.' : 'Belum ada arus kas bulan ini.' }}
            </div>
        @endif
    </div>

    {{-- Komposisi Pengeluaran --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-5">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Expense Breakdown' : 'Komposisi Pengeluaran' }}</h2>
        <div class="space-y-4">
            @forelse ($composition as $k)
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full bg-orange-500 mt-1.5"></span>
                        <div>
                            <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ ucfirst($k['name']) }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">{{ format_currency($k['amount']) }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $k['percent'] }}%</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'No expenses recorded.' : 'Belum ada pengeluaran.' }}</p>
            @endforelse
        </div>
    </div>
</div>

{{-- RINGKASAN BUKU KAS BULANAN --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800">
    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Monthly Cash Book Summary' : 'Ringkasan Buku Kas Bulanan' }}</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide bg-slate-50/50 dark:bg-slate-950/50">
                    <th class="px-6 py-3 font-medium">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Cash Account' : 'Rekening Kas' }}</th>
                    <th class="px-6 py-3 font-medium text-right">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Initial Balance' : 'Saldo Awal' }}</th>
                    <th class="px-6 py-3 font-medium text-right">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Income' : 'Pemasukan' }}</th>
                    <th class="px-6 py-3 font-medium text-right">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Expense' : 'Pengeluaran' }}</th>
                    <th class="px-6 py-3 font-medium text-right">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Final Balance' : 'Saldo Akhir' }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($recap as $r)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ $r['account'] }}</td>
                        <td class="px-6 py-4 text-right text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ format_currency($r['initial']) }}</td>
                        <td class="px-6 py-4 text-right text-emerald-600 dark:text-emerald-400 font-medium whitespace-nowrap">{{ format_currency($r['income']) }}</td>
                        <td class="px-6 py-4 text-right text-red-500 dark:text-red-400 font-medium whitespace-nowrap">{{ format_currency($r['expense']) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900 dark:text-slate-100 whitespace-nowrap">{{ format_currency($r['final']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-4 text-sm text-slate-400">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'No report data available.' : 'Belum ada data laporan.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if (count($flux) > 0)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('netCashflowChart');
        if (!ctx) return;

        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#94a3b8' : '#64748b';
        const gridColor = isDarkMode ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.8)';

        const fluxData = @json($flux);
        const labels = fluxData.map(f => f.label);
        const values = fluxData.map(f => f.value);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Net Cashflow',
                    data: values,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: '#2563eb',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? '#0f172a' : '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#f8fafc',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (context) {
                                const val = context.raw;
                                const isUsd = (window.__APP_SETTINGS__ && window.__APP_SETTINGS__.mata_uang === 'usd');
                                const sym = isUsd ? '$' : 'Rp';
                                const locale = isUsd ? 'en-US' : 'id-ID';
                                const formatted = new Intl.NumberFormat(locale).format(Math.abs(val));
                                return ' ' + (val >= 0 ? '+' : '-') + sym + ' ' + formatted;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: gridColor
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 11
                            },
                            callback: function (value) {
                                const isUsd = (window.__APP_SETTINGS__ && window.__APP_SETTINGS__.mata_uang === 'usd');
                                const sym = isUsd ? '$' : 'Rp';
                                const suffixM = isUsd ? 'M' : ' jt';
                                const suffixK = isUsd ? 'K' : ' rb';
                                if (Math.abs(value) >= 1000000) {
                                    return (value < 0 ? '-' : '') + sym + ' ' + (Math.abs(value) / 1000000).toFixed(Math.abs(value) % 1000000 === 0 ? 0 : 1) + suffixM;
                                } else if (Math.abs(value) >= 1000) {
                                    return (value < 0 ? '-' : '') + sym + ' ' + (Math.abs(value) / 1000).toFixed(Math.abs(value) % 1000 === 0 ? 0 : 1) + suffixK;
                                }
                                return sym + ' ' + value;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection