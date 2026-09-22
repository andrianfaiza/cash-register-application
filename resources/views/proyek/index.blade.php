@extends('layouts.app')

@section('title', 'Proyek')
@section('page-title', 'Proyek')
@section('description', 'Memantau dana khusus proyek, penyerapan anggaran secara berkala, dan sisa alokasi.')
@section('content')

{{-- SUMMARY CARDS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide">TOTAL PAGU ANGGARAN PROYEK</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-3">Rp {{ number_format($totalBudget, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Dianggarkan dalam {{ $projects->where('status', 'aktif')->count() }} proyek aktif</p>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide">REALISASI PENYERAPAN</p>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-3">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">{{ $persentaseRealisasi }} dari total pagu</p>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-800">
        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide">SISA SALDO PROYEK</p>
        <p class="text-2xl font-bold text-orange-500 mt-3">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-3">Tersedia untuk dicairkan</p>
    </div>
</div>

{{-- DAFTAR PROYEK --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-200">Daftar Proyek Aktif &amp; Penyerapan</h2>
        <div class="flex items-center gap-2">
            <button
                type="button"
                onclick="toggleHapusMode()"
                id="btnHapusProyek"
                title="Hapus Proyek"
                class="flex items-center gap-2 bg-white dark:bg-slate-900 hover:bg-red-50 dark:hover:bg-red-950/50 text-red-500 border border-red-200 dark:border-red-900/50 hover:border-red-300 text-sm font-medium px-4 py-2 rounded-lg transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Hapus
            </button>
            <button onclick="openProyekModal()" class="flex items-center gap-2 bg-slate-950 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Inisiasi Proyek
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($projects as $project)
            <div class="border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 rounded-xl p-4 relative">
                <div class="hapus-col hidden absolute top-3 left-3">
                    <input type="checkbox" name="proyek_ids[]" value="{{ $project->id }}" class="proyek-checkbox rounded border-slate-300">
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $project->nama_proyek }}</p>
                    <button
                        type="button"
                        title="Edit Proyek"
                        onclick="editProyek({{ $project->id }})"
                        class="w-7 h-7 flex items-center justify-center rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/50 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 mb-4">{{ $project->deskripsi ?: 'Tidak ada deskripsi.' }}</p>

                <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1.5">
                    <span>Penyerapan</span>
                    <span class="font-semibold text-orange-500">{{ $project->progress }}%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden mb-4">
                    <div class="h-full bg-orange-500 rounded-full" style="width: {{ $project->progress }}%"></div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div>
                        <p class="text-slate-400 dark:text-slate-500">Terserap</p>
                        <p class="font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($project->spent, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-400 dark:text-slate-500">Total Pagu</p>
                        <p class="font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($project->pagu_anggaran, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">Belum ada proyek.</p>
        @endforelse
    </div>
</div>

@include('proyek.create')

<form id="deleteProyekForm" method="POST" action="{{ route('proyek.destroy') }}" class="hidden">
    @csrf
    @method('DELETE')
    <div id="deleteProyekIds"></div>
</form>

<script id="proyekData" type="application/json">@json($proyekData)</script>
<script>
    window.proyekRoutes = {
        store: @json(route('proyek.store')),
        update: @json(url('proyek')),
        destroy: @json(route('proyek.destroy')),
    };
</script>
@endsection