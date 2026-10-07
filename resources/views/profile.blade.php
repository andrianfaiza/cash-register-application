@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('description', 'Kelola informasi pribadi, keamanan akun, dan riwayat aktivitas login.')

@section('content')

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @php
        $user = $user ?? $account;
        $user->nama = $user->nama ?? $user->name ?? 'Pengguna';
        $user->jabatan = $user->jabatan ?? 'Finance Admin';
        $user->status = $user->status ?? 'Aktif';
        $user->foto = $user->foto ?? 'no-profile.jpg';
        // Pastikan file benar-benar ada sebelum URL-nya dipakai (hindari gambar rusak).
        $fotoProfilUrl = asset($user->foto);
        if (str_starts_with($user->foto, 'foto-profil')) {
            $fotoProfilUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto)
                ? asset('storage/' . $user->foto)
                : asset('no-profile.jpg');
        }
        $user->telepon = $user->telepon ?? '-';
        $user->nip = $user->nip ?? '-';
        $user->departemen = $user->departemen ?? 'Finance & Accounting';
        $sesiAktif = [
            ['perangkat' => request()->userAgent() ?: 'Perangkat saat ini', 'ip' => request()->ip(), 'lokasi' => 'Sesi saat ini', 'waktu' => 'Sekarang', 'aktif' => true],
        ];

        $riwayatLogin = $loginHistory->map(fn ($activity) => [
            'waktu' => $activity->logged_in_at->format('d M Y, H:i'),
            'ip' => $activity->ip_address ?: '-',
            'perangkat' => $activity->user_agent ?: 'Perangkat tidak diketahui',
            'status' => $activity->status,
        ]);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

        {{-- =========================== --}}
        {{-- KOLOM KIRI: PROFILE CARD     --}}
        {{-- =========================== --}}
        <div class="app-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col items-center text-center">
            <div class="relative">
                <button type="button" id="profilePhotoMenuButton" aria-expanded="false" aria-controls="profilePhotoMenu" onclick="toggleProfilePhotoMenu(event)" class="h-28 w-28 overflow-hidden rounded-lg border border-slate-200 bg-slate-100 transition hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-slate-700 dark:bg-slate-800">
                    <img id="profileAvatar" src="{{ $fotoProfilUrl }}" onerror="this.onerror=null;this.src='{{ asset('no-profile.jpg') }}';" alt="Foto profil {{ $user->nama }}" class="h-full w-full object-cover">
                </button>
                <form id="avatarForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="hidden">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="file" id="profilePhotoInput" name="foto_profil" accept="image/*" onchange="submitProfilePhoto(this)">
                </form>

                <div id="profilePhotoMenu" class="absolute left-1/2 top-full z-30 mt-2 hidden w-52 -translate-x-1/2 overflow-hidden rounded-lg border border-slate-200 bg-white py-1 text-left shadow-xl dark:border-slate-700 dark:bg-slate-900">
                    <button type="button" onclick="openProfilePhotoPicker('camera')" class="flex w-full items-center gap-3 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8a2 2 0 012-2h2l1.5-2h7L17 6h2a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/><circle cx="12" cy="13" r="3"/></svg>
                        Ambil foto
                    </button>
                    <button type="button" onclick="openProfilePhotoPicker('file')" class="flex w-full items-center gap-3 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 4v6m8 4v6"/></svg>
                        Ganti foto
                    </button>
                    <button type="button" onclick="openProfilePhotoPreview()" class="flex w-full items-center gap-3 px-3 py-2.5 text-sm text-slate-700 transition hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-800">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7C20.268 16.057 16.477 19 12 19s-8.268-2.943-9.542-7z"/><circle cx="12" cy="12" r="3"/></svg>
                        Lihat foto
                    </button>
                    @if ($user->foto !== 'no-profile.jpg')
                        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                        <form method="POST" action="{{ route('profile.update') }}" onsubmit="return confirm('Kembalikan foto profil ke foto default?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="reset_foto" value="1">
                            <button type="submit" class="flex w-full items-center gap-3 px-3 py-2.5 text-sm text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus foto
                            </button>
                        </form>
                    @endif
                </div>

            </div>

            <h2 class="text-base font-bold text-slate-900 dark:text-slate-100 mt-4">{{ $user->nama }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $user->jabatan }}</p>

            <span class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Status Akun: {{ $user->status }}
            </span>

            <div class="w-full mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 space-y-3 text-left">
                <div class="flex items-center gap-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-slate-600 dark:text-slate-300 truncate">{{ $user->email }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="text-slate-600 dark:text-slate-300">{{ $user->telepon }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="text-slate-600 dark:text-slate-300">{{ $user->departemen }}</span>
                </div>
            </div>
        </div>

        {{-- =========================== --}}
        {{-- KOLOM KANAN                  --}}
        {{-- =========================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- 2. INFORMASI PRIBADI --}}
            <form id="profilForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="app-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                @csrf
                @method('PUT')
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 tracking-wide mb-5">INFORMASI PRIBADI</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">ALAMAT EMAIL</label>
                        <input type="email" name="email" value="{{ $user->email }}"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">NOMOR TELEPON / WHATSAPP</label>
                        <input type="tel" name="telepon" value="{{ $user->telepon }}"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">NOMOR INDUK PEGAWAI (NIP)</label>
                        <input type="text" name="nip" value="{{ $user->nip }}" placeholder="Contoh: WNJ-2024-0182"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">DEPARTEMEN / DIVISI</label>
                        <select name="departemen" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option {{ $user->departemen === 'Finance & Accounting' ? 'selected' : '' }}>Finance & Accounting</option>
                            <option>Operasional</option>
                            <option>Logistik</option>
                            <option>Human Resources</option>
                            <option>IT & Sistem</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-slate-950 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            {{-- 3. KEAMANAN AKUN --}}
            <div class="app-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 tracking-wide mb-5">KEAMANAN AKUN</h2>

                {{-- Ubah Password --}}
                <form method="POST" action="{{ route('profile.update') }}" class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                    @csrf
                    @method('PUT')
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Ubah Kata Sandi</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">PASSWORD SAAT INI</label>
                            <input type="password" name="current_password" placeholder="••••••••"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">PASSWORD BARU</label>
                            <input type="password" name="new_password" placeholder="••••••••"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide mb-1.5">KONFIRMASI PASSWORD BARU</label>
                            <input type="password" name="new_password_confirmation" placeholder="••••••••"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-slate-950 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>

            {{-- 4. AKTIVITAS & RIWAYAT LOGIN --}}
            <div class="app-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 tracking-wide mb-5">AKTIVITAS &amp; RIWAYAT LOGIN</h2>

                {{-- Sesi Aktif --}}
                <div class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Sesi Aktif</p>
                    <div class="space-y-3">
                        @foreach ($sesiAktif as $s)
                            <div class="flex items-center justify-between border border-slate-100 dark:border-slate-800 rounded-lg px-4 py-3 bg-slate-50/50 dark:bg-slate-950/40">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L15 11.75M9.75 17H6a2 2 0 01-2-2v-8a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-3.75M9.75 17v3m4.5-3v3" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $s['perangkat'] }}</p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ $s['ip'] }} &bull; {{ $s['lokasi'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ $s['waktu'] }}</span>
                                    @if (!$loop->first)
                                        <button type="button" class="text-xs font-medium text-red-500 hover:text-red-600">Akhiri Sesi</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Riwayat Log --}}
                <div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">Riwayat Log Terakhir</p>
                    <div class="hidden md:block overflow-x-auto rounded-lg border border-slate-100 dark:border-slate-800">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide bg-slate-50 dark:bg-slate-950">
                                    <th class="px-4 py-3 font-medium">Waktu</th>
                                    <th class="px-4 py-3 font-medium">Perangkat</th>
                                    <th class="px-4 py-3 font-medium">IP Address</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach ($riwayatLogin as $log)
                                    <tr>
                                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $log['waktu'] }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200 whitespace-nowrap">{{ $log['perangkat'] }}</td>
                                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ $log['ip'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($log['status'] === 'Berhasil')
                                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400">Berhasil</span>
                                            @else
                                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400">Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="md:hidden space-y-3">
                        @foreach ($riwayatLogin as $log)
                            <div class="bg-slate-50 dark:bg-slate-800/40 rounded-lg p-3 border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $log['waktu'] }}</span>
                                    @if ($log['status'] === 'Berhasil')
                                        <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400">Berhasil</span>
                                    @else
                                        <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400">Gagal</span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">{{ $log['perangkat'] }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">IP: {{ $log['ip'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="profilePhotoPreview" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/80 p-4" style="z-index: 70" onclick="closeProfilePhotoPreview()" role="dialog" aria-modal="true" aria-label="Pratinjau foto profil">
    <div class="relative max-h-full max-w-3xl rounded-lg border border-slate-700 bg-slate-900 p-3 shadow-2xl" onclick="event.stopPropagation()">
        <button type="button" onclick="closeProfilePhotoPreview()" aria-label="Tutup pratinjau foto" class="absolute right-5 top-5 z-10 rounded-md bg-slate-950/70 p-2 text-white hover:bg-slate-950">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img src="{{ $fotoProfilUrl }}" onerror="this.onerror=null;this.src='{{ asset('no-profile.jpg') }}';" alt="Foto profil {{ $user->nama }} ukuran penuh" class="max-h-[80vh] max-w-full rounded-md object-contain">
    </div>
</div>

<script>
    function toggleSwitch(id) {
        const btn = document.getElementById(id);
        const knob = btn.querySelector('.knob');
        const isOn = btn.classList.contains('bg-emerald-500');

        btn.classList.toggle('bg-emerald-500', !isOn);
        btn.classList.toggle('bg-slate-300', isOn);
        knob.classList.toggle('translate-x-4', !isOn);
        knob.classList.toggle('translate-x-0.5', isOn);
    }
</script>
@endsection