@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Pengaturan')
@section('description', 'Kelola preferensi bahasa, format mata uang, mode tampilan, dan notifikasi aplikasi.')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Form Pengaturan -->
    <form method="POST" action="{{ route('settings.update') }}" class="space-y-6"> 
        @csrf
        @method('PUT')

        {{-- Bahasa & Format --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <h2 class="text-xs font-bold text-slate-800 dark:text-slate-200 tracking-wider uppercase mb-5">Bahasa &amp; Format</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">BAHASA</label>
                    <select name="bahasa" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="id" {{ old('bahasa', $settings->bahasa ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                        <option value="en" {{ old('bahasa', $settings->bahasa ?? 'id') == 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">MATA UANG</label>
                    <select name="mata_uang" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="idr" {{ old('mata_uang', $settings->mata_uang ?? 'idr') == 'idr' ? 'selected' : '' }}>IDR (Rp)</option>
                        <option value="usd" {{ old('mata_uang', $settings->mata_uang ?? 'idr') == 'usd' ? 'selected' : '' }}>USD ($)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">FORMAT TANGGAL</label>
                    <select name="format_tanggal" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="dd/mm/yyyy" {{ old('format_tanggal', $settings->format_tanggal ?? 'dd/mm/yyyy') == 'dd/mm/yyyy' ? 'selected' : '' }}>DD/MM/YYYY</option>
                        <option value="yyyy-mm-dd" {{ old('format_tanggal', $settings->format_tanggal ?? 'dd/mm/yyyy') == 'yyyy-mm-dd' ? 'selected' : '' }}>YYYY-MM-DD</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Mode Tampilan --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <h2 class="text-xs font-bold text-slate-800 dark:text-slate-200 tracking-wider uppercase mb-5">Mode Tampilan</h2>

            <div class="grid grid-cols-2 gap-4 max-w-md">
                <!-- Light Mode -->
                <label class="cursor-pointer">
                    <input type="radio" name="mode_tampilan" value="light" class="peer hidden" 
                        {{ old('mode_tampilan', $settings->mode_tampilan ?? 'light') == 'light' ? 'checked' : '' }}>
                    <div class="border-2 border-slate-200 dark:border-slate-700 peer-checked:border-slate-900 dark:peer-checked:border-blue-500 rounded-xl p-4 bg-white dark:bg-slate-950 transition hover:border-slate-300 dark:hover:border-slate-600">
                        <div class="h-16 rounded-lg bg-slate-50 border border-slate-200 mb-3 flex items-center justify-center">
                            <span class="text-xs font-semibold text-slate-400">Light</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 text-center">Light Mode</p>
                    </div>
                </label>

                <!-- Dark Mode -->
                <label class="cursor-pointer">
                    <input type="radio" name="mode_tampilan" value="dark" class="peer hidden" 
                        {{ old('mode_tampilan', $settings->mode_tampilan ?? 'light') == 'dark' ? 'checked' : '' }}>
                    <div class="border-2 border-slate-200 dark:border-slate-700 peer-checked:border-slate-900 dark:peer-checked:border-blue-500 rounded-xl p-4 bg-white dark:bg-slate-950 transition hover:border-slate-300 dark:hover:border-slate-600">
                        <div class="h-16 rounded-lg bg-slate-900 mb-3 flex items-center justify-center">
                            <span class="text-xs font-semibold text-slate-400">Dark</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 text-center">Dark Mode</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Pengaturan Notifikasi --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <h2 class="text-xs font-bold text-slate-800 dark:text-slate-200 tracking-wider uppercase mb-5">Pengaturan Notifikasi</h2>

            <!-- Notifikasi via Email -->
            <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Notifikasi via Email</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Kirim email saat ada pengajuan transaksi baru yang perlu diverifikasi.</p>
                </div>
                <button type="button" id="toggleEmailNotif" onclick="toggleSwitch('toggleEmailNotif', 'inputNotifEmail')"
                    class="relative w-10 h-6 rounded-full transition-colors bg-emerald-500 shrink-0 focus:outline-none">
                    <span class="knob absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform translate-x-4"></span>
                </button>
                <input type="hidden" name="notif_email" id="inputNotifEmail" value="{{ old('notif_email', $settings->notif_email ?? 1) }}">
            </div>

            <!-- Notifikasi Sistem / Browser -->
            <div class="flex items-center justify-between py-3">
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Notifikasi Sistem / Browser</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Tampilkan peringatan browser untuk saldo minim atau anggaran mendekati batas.</p>
                </div>
                <button type="button" id="toggleSistemNotif" onclick="toggleSwitch('toggleSistemNotif', 'inputNotifSistem')"
                    class="relative w-10 h-6 rounded-full transition-colors bg-emerald-500 shrink-0 focus:outline-none">
                    <span class="knob absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform translate-x-4"></span>
                </button>
                <input type="hidden" name="notif_sistem" id="inputNotifSistem" value="{{ old('notif_sistem', $settings->notif_sistem ?? 1) }}">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition shadow-sm">
                Simpan Preferensi
            </button>
        </div>
    </form>
</div>

<!-- Script Switch Toggle Notifikasi -->
<script>
    function toggleSwitch(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        const knob = btn.querySelector('.knob');

        if (input.value === "1") {
            input.value = "0";
            btn.classList.remove('bg-emerald-500');
            btn.classList.add('bg-slate-300');
            knob.classList.remove('translate-x-4');
            knob.classList.add('translate-x-0');
        } else {
            input.value = "1";
            btn.classList.remove('bg-slate-300');
            btn.classList.add('bg-emerald-500');
            knob.classList.remove('translate-x-0');
            knob.classList.add('translate-x-4');
        }
    }
</script>
@endsection