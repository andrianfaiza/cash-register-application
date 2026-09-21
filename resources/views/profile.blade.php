@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('description', 'Kelola informasi pribadi, keamanan akun, dan riwayat aktivitas login.')

@section('content')

    @php
        $user = $user ?? $account;
        $user->nama = $user->nama ?? $user->name ?? 'Pengguna';
        $user->jabatan = $user->jabatan ?? 'Finance Admin';
        $user->status = $user->status ?? 'Aktif';
        $user->foto = $user->foto ?? 'no-profile.jpg';
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
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col items-center text-center">
            <div class="relative">
                <img src="{{ $user->foto }}" alt="{{ $user->nama }}" class="w-24 h-24 rounded-full object-cover">
                <label class="absolute bottom-0 right-0 w-8 h-8 flex items-center justify-center bg-slate-950 text-white rounded-full cursor-pointer hover:bg-slate-800 border-2 border-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 13a3 3 0 100 6 3 3 0 000-6z" />
                    </svg>
                    <input type="file" name="foto_profil" accept="image/*" class="hidden" onchange="document.getElementById('profilForm').submit()">
                </label>
            </div>

            <h2 class="text-base font-bold text-slate-900 mt-4">{{ $user->nama }}</h2>
            <p class="text-sm text-slate-500">{{ $user->jabatan }}</p>

            <span class="inline-flex items-center gap-1.5 mt-3 text-xs font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Status Akun: {{ $user->status }}
            </span>

            <div class="w-full mt-6 pt-5 border-t border-slate-100 space-y-3 text-left">
                <div class="flex items-center gap-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-slate-600 truncate">{{ $user->email }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="text-slate-600">{{ $user->telepon }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="text-slate-600">{{ $user->departemen }}</span>
                </div>
            </div>
        </div>

        {{-- =========================== --}}
        {{-- KOLOM KANAN                  --}}
        {{-- =========================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- 2. INFORMASI PRIBADI --}}
            <form id="profilForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-slate-200 p-6">
                @csrf
                @method('PUT')
                <h2 class="text-sm font-bold text-slate-800 tracking-wide mb-5">INFORMASI PRIBADI</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 tracking-wide mb-1.5">ALAMAT EMAIL</label>
                        <input type="email" name="email" value="{{ $user->email }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 tracking-wide mb-1.5">NOMOR TELEPON / WHATSAPP</label>
                        <input type="tel" name="telepon" value="{{ $user->telepon }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 tracking-wide mb-1.5">NOMOR INDUK PEGAWAI (NIP)</label>
                        <input type="text" name="nip" value="{{ $user->nip }}" placeholder="Contoh: WNJ-2024-0182"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 tracking-wide mb-1.5">DEPARTEMEN / DIVISI</label>
                        <select name="departemen" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option {{ $user->departemen === 'Finance & Accounting' ? 'selected' : '' }}>Finance & Accounting</option>
                            <option>Operasional</option>
                            <option>Logistik</option>
                            <option>Human Resources</option>
                            <option>IT & Sistem</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-slate-950 hover:bg-slate-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            {{-- 3. KEAMANAN AKUN --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-sm font-bold text-slate-800 tracking-wide mb-5">KEAMANAN AKUN</h2>

                {{-- Ubah Password --}}
                <form method="POST" action="{{ route('profile.update') }}" class="mb-6 pb-6 border-b border-slate-100">
                    @csrf
                    @method('PUT')
                    <p class="text-sm font-semibold text-slate-700 mb-3">Ubah Kata Sandi</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 tracking-wide mb-1.5">PASSWORD SAAT INI</label>
                            <input type="password" name="current_password" placeholder="••••••••"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 tracking-wide mb-1.5">PASSWORD BARU</label>
                            <input type="password" name="new_password" placeholder="••••••••"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 tracking-wide mb-1.5">KONFIRMASI PASSWORD BARU</label>
                            <input type="password" name="new_password_confirmation" placeholder="••••••••"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-slate-950 hover:bg-slate-800 text-white text-sm font-semibold px-5 py-2.5 rounded-lg">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>

            

            {{-- 4. AKTIVITAS & RIWAYAT LOGIN --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-sm font-bold text-slate-800 tracking-wide mb-5">AKTIVITAS &amp; RIWAYAT LOGIN</h2>

                {{-- Sesi Aktif --}}
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-700 mb-3">Sesi Aktif</p>
                    <div class="space-y-3">
                        @foreach ($sesiAktif as $s)
                            <div class="flex items-center justify-between border border-slate-100 rounded-lg px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L15 11.75M9.75 17H6a2 2 0 01-2-2v-8a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2h-3.75M9.75 17v3m4.5-3v3" />
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">{{ $s['perangkat'] }}</p>
                                        <p class="text-xs text-slate-400">{{ $s['ip'] }} &bull; {{ $s['lokasi'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-semibold text-emerald-600">{{ $s['waktu'] }}</span>
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
                    <p class="text-sm font-semibold text-slate-700 mb-3">Riwayat Log Terakhir</p>
                    <div class="overflow-x-auto rounded-lg border border-slate-100">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-slate-400 uppercase tracking-wide bg-slate-50">
                                    <th class="px-4 py-3 font-medium">Waktu</th>
                                    <th class="px-4 py-3 font-medium">Perangkat</th>
                                    <th class="px-4 py-3 font-medium">IP Address</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($riwayatLogin as $log)
                                    <tr>
                                        <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $log['waktu'] }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-700 whitespace-nowrap">{{ $log['perangkat'] }}</td>
                                        <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $log['ip'] }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if ($log['status'] === 'Berhasil')
                                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Berhasil</span>
                                            @else
                                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-600">Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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