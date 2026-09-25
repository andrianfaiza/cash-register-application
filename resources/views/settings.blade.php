@extends('layouts.app')

@section('title', ($appSettings->bahasa ?? 'id') === 'en' ? 'Settings' : 'Pengaturan')
@section('page-title', ($appSettings->bahasa ?? 'id') === 'en' ? 'Settings' : 'Pengaturan')
@section('description', ($appSettings->bahasa ?? 'id') === 'en' ? 'Manage language preferences, currency format, display mode, and application notifications.' : 'Kelola preferensi bahasa, format mata uang, mode tampilan, dan notifikasi aplikasi.')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- ALERT SUKSES / ERROR --}}
    @if (session('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 text-sm animate-fadeIn">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 text-red-800 dark:text-red-300 text-sm">
            <p class="font-semibold mb-1">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'There were some errors:' : 'Terjadi beberapa kesalahan:' }}</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Pengaturan -->
    <form method="POST" action="{{ route('settings.update') }}" class="space-y-6"> 
        @csrf
        @method('PUT')

        {{-- Bahasa & Format --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xs font-bold text-slate-800 dark:text-slate-200 tracking-wider uppercase">
                    {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Language & Formatting' : 'Bahasa & Format' }}
                </h2>
                <span class="text-xs text-slate-400">
                    {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'App Localization' : 'Lokalisasi Sistem' }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Pilihan Bahasa --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 tracking-wide mb-2">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'LANGUAGE' : 'BAHASA' }}
                    </label>
                    <select name="bahasa" id="selectBahasa" onchange="updatePreview()" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="id" {{ old('bahasa', $settings->bahasa ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia (ID)</option>
                        <option value="en" {{ old('bahasa', $settings->bahasa ?? 'id') == 'en' ? 'selected' : '' }}>English (US)</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Language for greeting & interface.' : 'Bahasa untuk salam & antarmuka.' }}
                    </p>
                </div>

                {{-- Pilihan Mata Uang --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 tracking-wide mb-2">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'CURRENCY' : 'MATA UANG' }}
                    </label>
                    <select name="mata_uang" id="selectMataUang" onchange="updatePreview()" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="idr" {{ old('mata_uang', $settings->mata_uang ?? 'idr') == 'idr' ? 'selected' : '' }}>IDR (Rp) - Rupiah Indonesia</option>
                        <option value="usd" {{ old('mata_uang', $settings->mata_uang ?? 'idr') == 'usd' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Symbol & number separator for all cash numbers.' : 'Simbol & pemisah ribuan nominal kas.' }}
                    </p>
                </div>

                {{-- Pilihan Format Tanggal --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 tracking-wide mb-2">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'DATE FORMAT' : 'FORMAT TANGGAL' }}
                    </label>
                    <select name="format_tanggal" id="selectFormatTanggal" onchange="updatePreview()" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="dd/mm/yyyy" {{ old('format_tanggal', $settings->format_tanggal ?? 'dd/mm/yyyy') == 'dd/mm/yyyy' ? 'selected' : '' }}>DD/MM/YYYY (25/09/2026)</option>
                        <option value="yyyy-mm-dd" {{ old('format_tanggal', $settings->format_tanggal ?? 'dd/mm/yyyy') == 'yyyy-mm-dd' ? 'selected' : '' }}>YYYY-MM-DD (2026-09-25)</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Format for reports & transaction dates.' : 'Format tanggal tabel & laporan transaksi.' }}
                    </p>
                </div>
            </div>

            {{-- LIVE PREVIEW BOX --}}
            <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Live Format Preview' : 'Pratinjau Format Langsung' }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 bg-slate-50 dark:bg-slate-950/80 rounded-lg border border-slate-200 dark:border-slate-800/80">
                    <div class="p-2.5 bg-white dark:bg-slate-900 rounded-md border border-slate-200 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Greeting Preview' : 'Contoh Salam' }}</span>
                        <span id="previewGreeting" class="text-sm font-bold text-blue-600 dark:text-blue-400 mt-0.5 block">Selamat Pagi, Admin</span>
                    </div>
                    <div class="p-2.5 bg-white dark:bg-slate-900 rounded-md border border-slate-200 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Currency Preview' : 'Contoh Saldo Kas' }}</span>
                        <span id="previewCurrency" class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 block">Rp 150.000.000</span>
                    </div>
                    <div class="p-2.5 bg-white dark:bg-slate-900 rounded-md border border-slate-200 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">{{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Date Preview' : 'Contoh Tanggal' }}</span>
                        <span id="previewDate" class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">25/09/2026</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mode Tampilan --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <h2 class="text-xs font-bold text-slate-800 dark:text-slate-200 tracking-wider uppercase mb-5">
                {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Theme Mode' : 'Mode Tampilan' }}
            </h2>

            <div class="grid grid-cols-2 gap-4 max-w-md">
                <!-- Light Mode -->
                <label class="cursor-pointer">
                    <input type="radio" name="mode_tampilan" value="light" class="peer hidden" 
                        {{ old('mode_tampilan', $settings->mode_tampilan ?? 'light') == 'light' ? 'checked' : '' }}>
                    <div class="border-2 border-slate-200 dark:border-slate-700 peer-checked:border-slate-900 dark:peer-checked:border-blue-500 rounded-xl p-4 bg-white dark:bg-slate-950 transition hover:border-slate-300 dark:hover:border-slate-600">
                        <div class="h-16 rounded-lg bg-slate-50 border border-slate-200 mb-3 flex items-center justify-center">
                            <span class="text-xs font-semibold text-slate-500">Light Mode</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 text-center">Light Mode</p>
                    </div>
                </label>

                <!-- Dark Mode -->
                <label class="cursor-pointer">
                    <input type="radio" name="mode_tampilan" value="dark" class="peer hidden" 
                        {{ old('mode_tampilan', $settings->mode_tampilan ?? 'light') == 'dark' ? 'checked' : '' }}>
                    <div class="border-2 border-slate-200 dark:border-slate-700 peer-checked:border-slate-900 dark:peer-checked:border-blue-500 rounded-xl p-4 bg-white dark:bg-slate-950 transition hover:border-slate-300 dark:hover:border-slate-600">
                        <div class="h-16 rounded-lg bg-slate-900 border border-slate-800 mb-3 flex items-center justify-center">
                            <span class="text-xs font-semibold text-slate-400">Dark Mode</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 text-center">Dark Mode</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Pengaturan Notifikasi --}}
        @php
            $isEmailNotif = (bool) old('notif_email', $settings->notif_email ?? true);
            $isSistemNotif = (bool) old('notif_sistem', $settings->notif_sistem ?? true);
        @endphp
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
            <h2 class="text-xs font-bold text-slate-800 dark:text-slate-200 tracking-wider uppercase mb-5">
                {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Notification Preferences' : 'Pengaturan Notifikasi' }}
            </h2>

            <!-- Notifikasi via Email -->
            <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Email Notifications' : 'Notifikasi via Email' }}
                    </p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Send email alerts when new pending transactions require review.' : 'Kirim email saat ada pengajuan transaksi baru yang perlu diverifikasi.' }}
                    </p>
                </div>
                <button type="button" id="toggleEmailNotif" onclick="toggleSwitch('toggleEmailNotif', 'inputNotifEmail')"
                    class="relative w-11 h-6 rounded-full transition-colors {{ $isEmailNotif ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700' }} shrink-0 focus:outline-none">
                    <span class="knob absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform {{ $isEmailNotif ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
                <input type="hidden" name="notif_email" id="inputNotifEmail" value="{{ $isEmailNotif ? '1' : '0' }}">
            </div>

            <!-- Notifikasi Sistem / Browser -->
            <div class="flex items-center justify-between py-3">
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'System & Browser Alerts' : 'Notifikasi Sistem / Browser' }}
                    </p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Display browser alert banners for low liquidity or budget limits.' : 'Tampilkan peringatan browser untuk saldo minim atau anggaran mendekati batas.' }}
                    </p>
                </div>
                <button type="button" id="toggleSistemNotif" onclick="toggleSwitch('toggleSistemNotif', 'inputNotifSistem')"
                    class="relative w-11 h-6 rounded-full transition-colors {{ $isSistemNotif ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700' }} shrink-0 focus:outline-none">
                    <span class="knob absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition-transform {{ $isSistemNotif ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
                <input type="hidden" name="notif_sistem" id="inputNotifSistem" value="{{ $isSistemNotif ? '1' : '0' }}">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition shadow-sm">
                {{ ($appSettings->bahasa ?? 'id') === 'en' ? 'Save Preferences' : 'Simpan Preferensi' }}
            </button>
        </div>
    </form>
</div>

<!-- Script Switch Toggle & Live Preview -->
<script>
    function toggleSwitch(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        const knob = btn.querySelector('.knob');

        if (input.value === "1") {
            input.value = "0";
            btn.classList.remove('bg-emerald-500');
            btn.classList.add('bg-slate-300', 'dark:bg-slate-700');
            knob.classList.remove('translate-x-5');
            knob.classList.add('translate-x-0');
        } else {
            input.value = "1";
            btn.classList.remove('bg-slate-300', 'dark:bg-slate-700');
            btn.classList.add('bg-emerald-500');
            knob.classList.remove('translate-x-0');
            knob.classList.add('translate-x-5');
        }
    }

    function updatePreview() {
        const bahasa = document.getElementById('selectBahasa').value;
        const mataUang = document.getElementById('selectMataUang').value;
        const formatTanggal = document.getElementById('selectFormatTanggal').value;

        // Update Currency Preview
        const previewCurrency = document.getElementById('previewCurrency');
        if (previewCurrency) {
            previewCurrency.textContent = mataUang === 'usd' ? '$ 150,000,000' : 'Rp 150.000.000';
        }

        // Update Date Preview
        const previewDate = document.getElementById('previewDate');
        if (previewDate) {
            previewDate.textContent = formatTanggal === 'yyyy-mm-dd' ? '2026-09-25' : '25/09/2026';
        }

        // Update Greeting Preview
        const previewGreeting = document.getElementById('previewGreeting');
        if (previewGreeting) {
            previewGreeting.textContent = bahasa === 'en' ? 'Good Morning, Admin' : 'Selamat Pagi, Admin';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();
    });
</script>
@endsection