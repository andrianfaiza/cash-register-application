@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('description', 'Pantau real-time likuiditas kas, pemasukan, pengeluaran, dan progres anggaran.')
@section('content')
    
{{-- SUMMARY CARDS --}}
<div class="flex">
    <h1 id="greetingText" data-user-name="{{ auth()->user()->name ?? 'Guest' }}" class="text-lg font-bold"></h1>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    {{-- Saldo Konsolidasi --}}
    <div class="bg-slate-950 text-white rounded-xl p-5 border border-slate-800">
        <p class="text-xs font-semibold text-orange-500 tracking-wide">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'TOTAL CONSOLIDATED BALANCE' : 'TOTAL SALDO KONSOLIDASI' }}</p>
        <p class="text-2xl font-bold mt-3">{{ format_currency($saldoKonsolidasi) }}</p>
        <p class="text-xs text-slate-400 mt-3">
            {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Net balance from successful transactions' : 'Saldo bersih dari transaksi sukses' }}
            <span class="text-orange-500">&bull; {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Healthy Liquidity' : 'Likuiditas Sehat' }}</span>
        </p>
    </div>

    {{-- Pemasukan --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'TOTAL INCOME' : 'TOTAL PEMASUKAN' }}</p>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-3">{{ format_currency($pemasukan) }}</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'From ' . $jumlahTransaksiMasuk . ' successful transactions' : 'Dari ' . $jumlahTransaksiMasuk . ' transaksi sukses' }}</p>
    </div>

    {{-- Pengeluaran --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'TOTAL EXPENSE' : 'TOTAL PENGELUARAN' }}</p>
        <p class="text-2xl font-bold text-red-500 dark:text-red-400 mt-3">{{ format_currency($pengeluaran) }}</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'From ' . $jumlahPosKeluar . ' operational & project posts' : 'Dari ' . $jumlahPosKeluar . ' pos operasional & proyek' }}</p>
    </div>
</div>

{{-- CHARTS ROW (Weekly Trend 62% + Project Doughnut Gauge 38%) --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

    {{-- Tren Arus Kas Mingguan (Left 60%-65%) --}}
    <div class="lg:col-span-8 xl:col-span-8 bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Weekly Cashflow Trend' : 'Tren Arus Kas Mingguan' }}</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Comparison of income & expense over 5 weeks' : 'Perbandingan arus kas masuk & keluar 5 minggu terakhir' }}</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Income' : 'Pemasukan' }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Expense' : 'Pengeluaran' }}</span>
            </div>
        </div>

        <div class="relative h-64 sm:h-72 w-full">
            <canvas id="weeklyCashflowChart"></canvas>
        </div>
    </div>

    {{-- Alokasi & Progres Proyek (Right 35%-40% with Doughnut Central Gauge) --}}
    <div class="lg:col-span-4 xl:col-span-4 bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Active Project Allocation' : 'Alokasi & Progres Proyek' }}</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Distribution & budget realization' : 'Distribusi pagu & realisasi biaya proyek' }}</p>
            </div>
            <a class="text-xs underline text-blue-600 dark:text-blue-400 font-medium" href="{{ route('proyek')}}">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'View Details' : 'Lihat Detail' }}</a>
        </div>

        {{-- Doughnut Chart with Central Progress / Text --}}
        <div class="relative flex items-center justify-center my-2">
            <div class="relative w-48 h-48 sm:w-52 sm:h-52">
                <canvas id="projectDoughnutChart"></canvas>

                {{-- Central Text / Gauge Metric --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center px-4">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 dark:text-slate-500">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Total Budget' : 'Total Pagu' }}
                    </span>
                    <span class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white leading-tight mt-0.5">
                        {{ format_currency($totalProjectBudget) }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-orange-500 mt-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        {{ $avgProgress }}% {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Realized' : 'Terserap' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Project Legend Breakdown List --}}
        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 space-y-2 max-h-40 overflow-y-auto pr-1">
            @php
                $projectColors = [
                    '#3b82f6', '#f97316', '#10b981', '#8b5cf6', '#06b6d4',
                    '#ec4899', '#f59e0b', '#6366f1', '#14b8a6', '#e11d48',
                    '#84cc16', '#a855f7'
                ];
            @endphp
            @forelse ($activeProjects as $index => $project)
                @php
                    $color = $projectColors[$index % count($projectColors)];
                @endphp
                <div class="flex items-center justify-between text-xs py-0.5 hover:bg-slate-50 dark:hover:bg-slate-800/40 rounded px-1 transition">
                    <div class="flex items-center gap-2 min-w-0 pr-2">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $color }};"></span>
                        <span class="font-medium text-slate-700 dark:text-slate-300 truncate" title="{{ $project->nama_proyek }}">
                            {{ $project->nama_proyek }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-slate-400 dark:text-slate-500 text-[11px]">{{ format_currency($project->spent) }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold" style="background-color: {{ $color }}18; color: {{ $color }};">
                            {{ $project->progress }}%
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-2">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'No active projects.' : 'Belum ada proyek aktif.' }}</p>
            @endforelse
        </div>
    </div>
</div>

{{-- TRANSAKSI TERBARU --}}
<div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Recent Transactions' : 'Transaksi Terbaru' }}</h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Latest cash flow records & verification status' : 'Catatan mutasi kas masuk dan keluar terkini' }}</p>
        </div>
        <a class="text-xs underline text-blue-600 dark:text-blue-400 font-medium" href="{{ route('transaksi')}}">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'View All Transactions' : 'Lihat Semua Transaksi' }} &rarr;</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide border-b border-slate-100 dark:border-slate-800">
                    <th class="pb-3 font-medium">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Date' : 'Tanggal' }}</th>
                    <th class="pb-3 font-medium">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Description' : 'Deskripsi' }}</th>
                    <th class="pb-3 font-medium">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Category' : 'Kategori' }}</th>
                    <th class="pb-3 font-medium">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Project' : 'Proyek' }}</th>
                    <th class="pb-3 font-medium">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Status' : 'Status' }}</th>
                    <th class="pb-3 font-medium text-right">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Amount' : 'Nominal' }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($recentTransactions as $transaction)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3.5 text-slate-500 dark:text-slate-400 whitespace-nowrap text-xs">{{ format_app_date($transaction->tanggal) }}</td>
                        <td class="py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $transaction->deskripsi ?: 'Tanpa deskripsi' }}</td>
                        <td class="py-3.5 text-orange-500 font-medium whitespace-nowrap text-xs">{{ ucfirst($transaction->kategori) }}</td>
                        <td class="py-3.5 text-slate-500 dark:text-slate-400 whitespace-nowrap text-xs">{{ $transaction->project?->nama_proyek ?: 'Non-Proyek' }}</td>
                        <td class="py-3.5">
                            <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $transaction->status === 'Sukses' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-400' }}">
                                {{ $transaction->status }}
                            </span>
                        </td>
                        <td class="py-3.5 text-right font-semibold whitespace-nowrap {{ $transaction->tipe === 'masuk' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500 dark:text-red-400' }}">
                            {{ $transaction->tipe === 'masuk' ? '+' : '-' }}{{ format_currency($transaction->nominal) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-sm text-slate-400">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'No transactions yet.' : 'Belum ada transaksi.' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#94a3b8' : '#64748b';
        const gridColor = isDarkMode ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.8)';

        // 1. Line Chart: Tren Arus Kas Mingguan
        const lineCtx = document.getElementById('weeklyCashflowChart');
        if (lineCtx) {
            const weeksData = @json($weeks);
            const labels = weeksData.map(w => w.label);
            const masukData = weeksData.map(w => w.masuk);
            const keluarData = weeksData.map(w => w.keluar);

            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: masukData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#10b981',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Pengeluaran',
                            data: keluarData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.12)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#ef4444',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }
                    ]
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
                                    const sym = (window.__APP_SETTINGS__ && window.__APP_SETTINGS__.mata_uang === 'usd') ? '$' : 'Rp';
                                    const locale = (window.__APP_SETTINGS__ && window.__APP_SETTINGS__.mata_uang === 'usd') ? 'en-US' : 'id-ID';
                                    return ' ' + context.dataset.label + ': ' + sym + ' ' + new Intl.NumberFormat(locale).format(context.raw);
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
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
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
                                    if (value >= 1000000) {
                                        return sym + ' ' + (value / 1000000).toFixed(value % 1000000 === 0 ? 0 : 1) + suffixM;
                                    } else if (value >= 1000) {
                                        return sym + ' ' + (value / 1000).toFixed(value % 1000 === 0 ? 0 : 1) + suffixK;
                                    }
                                    return sym + ' ' + value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Doughnut Chart: Alokasi & Progres Proyek (Central Gauge)
        const doughnutCtx = document.getElementById('projectDoughnutChart');
        if (doughnutCtx) {
            const projectData = @json($activeProjects);
            const projectLabels = projectData.map(p => p.nama_proyek);
            const projectBudgets = projectData.map(p => Number(p.pagu_anggaran) || 0);
            const projectSpent = projectData.map(p => Number(p.spent) || 0);
            const projectProgress = projectData.map(p => Number(p.progress) || 0);

            const colors = [
                '#3b82f6', '#f97316', '#10b981', '#8b5cf6', '#06b6d4',
                '#ec4899', '#f59e0b', '#6366f1', '#14b8a6', '#e11d48',
                '#84cc16', '#a855f7'
            ];
            const bgColors = projectLabels.map((_, i) => colors[i % colors.length]);

            // Hitung total pagu anggaran
            const totalBudget = projectBudgets.reduce((acc, curr) => acc + curr, 0);

            // Memberikan minimum visual slice agar proyek dengan pagu kecil (seperti Rp 10.000)
            // tetap memiliki irisan warna nyata di Doughnut Chart tanpa terhimpit border.
            const chartValues = projectBudgets.map(b => {
                if (totalBudget > 0) {
                    const minSlice = totalBudget * 0.04;
                    return b > 0 ? Math.max(b, minSlice) : minSlice;
                }
                return 1;
            });

            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: projectLabels,
                    datasets: [{
                        data: chartValues.length > 0 ? chartValues : [1],
                        backgroundColor: bgColors.length > 0 ? bgColors : ['#3b82f6'],
                        borderWidth: 2,
                        borderColor: isDarkMode ? '#0f172a' : '#ffffff',
                        hoverOffset: 5,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '74%',
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
                                    const index = context.dataIndex;
                                    const isUsd = (window.__APP_SETTINGS__ && window.__APP_SETTINGS__.mata_uang === 'usd');
                                    const sym = isUsd ? '$' : 'Rp';
                                    const locale = isUsd ? 'en-US' : 'id-ID';
                                    const budget = new Intl.NumberFormat(locale).format(projectBudgets[index] || 0);
                                    const spent = new Intl.NumberFormat(locale).format(projectSpent[index] || 0);
                                    const prog = projectProgress[index] || 0;
                                    return [
                                        ' Pagu: ' + sym + ' ' + budget,
                                        ' Terserap: ' + sym + ' ' + spent + ' (' + prog + '%)'
                                    ];
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection