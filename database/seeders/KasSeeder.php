<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class KasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks sementara untuk truncate bersih
        Schema::disableForeignKeyConstraints();
        Transaction::truncate();
        Project::truncate();
        Setting::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. SEED / UPDATE USER ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Keuangan',
                'password' => Hash::make('12345678'),
                'nip' => 'KAS-2026-001',
                'departemen' => 'Keuangan & Akuntansi',
                'telepon' => '081234567890',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Andrian Pratama',
                'password' => Hash::make('12345678'),
                'nip' => 'KAS-2026-002',
                'departemen' => 'Operasional Kas',
                'telepon' => '082198765432',
            ]
        );

        // 2. SEED SETTING
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'bahasa' => 'id',
                'mata_uang' => 'idr',
                'format_tanggal' => 'dd/mm/yyyy',
                'mode_tampilan' => 'light',
                'notif_email' => true,
                'notif_sistem' => true,
            ]
        );

        // 3. SEED PROYEK CONTOH
        $proyekERP = Project::create([
            'nama_proyek' => 'Pengembangan Sistem ERP & Kas',
            'kategori_proyek' => 'IT & Software',
            'deskripsi' => 'Pengembangan aplikasi pencatatan keuangan dan integrasi inventaris perusahaan.',
            'pagu_anggaran' => 85000000,
            'tanggal_mulai' => now()->startOfMonth()->subMonths(1),
            'tanggal_selesai' => now()->addMonths(2),
            'project_lead_id' => $admin->id,
            'departemen' => ['IT', 'Finance'],
            'status' => 'aktif',
        ]);

        $proyekRenovasi = Project::create([
            'nama_proyek' => 'Renovasi Ruang Kantor & Server',
            'kategori_proyek' => 'Infrastruktur',
            'deskripsi' => 'Peremajaan ruang kerja lantai 2 dan instalasi rack server baru.',
            'pagu_anggaran' => 50000000,
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->addMonths(1),
            'project_lead_id' => $staff->id,
            'departemen' => ['Umum', 'Logistik'],
            'status' => 'aktif',
        ]);

        $proyekMarketing = Project::create([
            'nama_proyek' => 'Kampanye Digital Marketing Q3',
            'kategori_proyek' => 'Marketing',
            'deskripsi' => 'Pemasaran produk lewat media sosial, Google Ads, dan event promosi.',
            'pagu_anggaran' => 35000000,
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->endOfMonth(),
            'project_lead_id' => $admin->id,
            'departemen' => ['Marketing', 'Komersial'],
            'status' => 'aktif',
        ]);

        $proyekHardware = Project::create([
            'nama_proyek' => 'Pengadaan Laptop & Perangkat IT',
            'kategori_proyek' => 'Pengadaan',
            'deskripsi' => 'Pengadaan workstation developer dan perangkat display meeting.',
            'pagu_anggaran' => 60000000,
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->addMonth(),
            'project_lead_id' => $staff->id,
            'departemen' => ['IT', 'Operasional'],
            'status' => 'aktif',
        ]);

        // Rekening kas
        $rekBCA = 'BCA - 8820192301';
        $rekMandiri = 'Mandiri - 1370009988';
        $rekBNI = 'BNI - 0891238472';
        $rekTunai = 'Kas Tunai (Petty Cash)';

        // 4. DATA TRANSAKSI REALISTIS
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        $transactions = [
            // --- MINGGU-MINGGU LALU & AWAL BULAN (Pemasukan Modal & Saldo Awal) ---
            [
                'tipe' => 'masuk',
                'nominal' => 150000000,
                'tanggal' => $now->copy()->subWeeks(4)->startOfWeek()->format('Y-m-d'),
                'kategori' => 'injeksi_modal',
                'proyek_id' => null,
                'deskripsi' => 'Suntikan modal kerja awal kuartal dari pemegang saham',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'masuk',
                'nominal' => 75000000,
                'tanggal' => $now->copy()->subWeeks(4)->startOfWeek()->addDays(2)->format('Y-m-d'),
                'kategori' => 'pendapatan',
                'proyek_id' => null,
                'deskripsi' => 'Pembayaran Termin 1 Klien PT Citra Digital Mandiri',
                'rekening_id' => $rekMandiri,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 12500000,
                'tanggal' => $now->copy()->subWeeks(4)->startOfWeek()->addDays(4)->format('Y-m-d'),
                'kategori' => 'operasional',
                'proyek_id' => null,
                'deskripsi' => 'Biaya sewa kantor bulanan dan maintenance gedung',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],

            // --- MINGGU KE-3 LALU ---
            [
                'tipe' => 'masuk',
                'nominal' => 48000000,
                'tanggal' => $now->copy()->subWeeks(3)->startOfWeek()->format('Y-m-d'),
                'kategori' => 'pendapatan',
                'proyek_id' => null,
                'deskripsi' => 'Pelunasan Invoice Proyek Integrasi API Klien CV Maju',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 25000000,
                'tanggal' => $now->copy()->subWeeks(3)->startOfWeek()->addDays(2)->format('Y-m-d'),
                'kategori' => 'proyek',
                'proyek_id' => $proyekERP->id,
                'deskripsi' => 'Pembayaran lisensi database, cloud VPS AWS, & software tools ERP',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 5400000,
                'tanggal' => $now->copy()->subWeeks(3)->startOfWeek()->addDays(3)->format('Y-m-d'),
                'kategori' => 'operasional',
                'proyek_id' => null,
                'deskripsi' => 'Tagihan internet fiber optic & listrik operasional kantor',
                'rekening_id' => $rekTunai,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],

            // --- MINGGU KE-2 LALU ---
            [
                'tipe' => 'masuk',
                'nominal' => 32000000,
                'tanggal' => $now->copy()->subWeeks(2)->startOfWeek()->addDays(1)->format('Y-m-d'),
                'kategori' => 'pendapatan',
                'proyek_id' => null,
                'deskripsi' => 'Pembayaran maintenance & support sistem bulanan klien retail',
                'rekening_id' => $rekMandiri,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'masuk',
                'nominal' => 1750000,
                'tanggal' => $now->copy()->subWeeks(2)->startOfWeek()->addDays(2)->format('Y-m-d'),
                'kategori' => 'bunga_bank',
                'proyek_id' => null,
                'deskripsi' => 'Pendapatan bagi hasil / bunga rekening giro deposito',
                'rekening_id' => $rekMandiri,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 18000000,
                'tanggal' => $now->copy()->subWeeks(2)->startOfWeek()->addDays(3)->format('Y-m-d'),
                'kategori' => 'proyek',
                'proyek_id' => $proyekRenovasi->id,
                'deskripsi' => 'Pembelian partisi aluminium, material gypsum & instalasi kabel lan',
                'rekening_id' => $rekMandiri,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 8500000,
                'tanggal' => $now->copy()->subWeeks(2)->startOfWeek()->addDays(4)->format('Y-m-d'),
                'kategori' => 'proyek',
                'proyek_id' => $proyekMarketing->id,
                'deskripsi' => 'Top-up saldo campaign Meta Ads (Facebook & Instagram) & Google Search',
                'rekening_id' => $rekMandiri,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],

            // --- MINGGU LALU (1 Minggu Lalu) ---
            [
                'tipe' => 'masuk',
                'nominal' => 55000000,
                'tanggal' => $now->copy()->subWeeks(1)->startOfWeek()->format('Y-m-d'),
                'kategori' => 'pendapatan',
                'proyek_id' => null,
                'deskripsi' => 'DP 50% Kontrak Pengembangan Aplikasi Mobile PT Sinar Berkah',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 38000000,
                'tanggal' => $now->copy()->subWeeks(1)->startOfWeek()->addDays(2)->format('Y-m-d'),
                'kategori' => 'gaji',
                'proyek_id' => null,
                'deskripsi' => 'Payroll transfer gaji pokok tim developer, staf finance & admin',
                'rekening_id' => $rekBNI,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 36000000,
                'tanggal' => $now->copy()->subWeeks(1)->startOfWeek()->addDays(3)->format('Y-m-d'),
                'kategori' => 'proyek',
                'proyek_id' => $proyekHardware->id,
                'deskripsi' => 'Pembelian 2 unit laptop MacBook M3 Pro dan 1 ThinkPad Developer',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 6800000,
                'tanggal' => $now->copy()->subWeeks(1)->startOfWeek()->addDays(4)->format('Y-m-d'),
                'kategori' => 'pajak',
                'proyek_id' => null,
                'deskripsi' => 'Penyetoran PPh Pasal 21 dan PPN Masa Badan Usaha',
                'rekening_id' => $rekMandiri,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],

            // --- MINGGU INI (Transaksi Terbaru & Terkini) ---
            [
                'tipe' => 'masuk',
                'nominal' => 28000000,
                'tanggal' => $now->copy()->startOfWeek()->format('Y-m-d'),
                'kategori' => 'pendapatan',
                'proyek_id' => null,
                'deskripsi' => 'Pemasukan penjualan lisensi tahunan modul kas register',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 15000000,
                'tanggal' => $now->copy()->startOfWeek()->addDays(1)->format('Y-m-d'),
                'kategori' => 'proyek',
                'proyek_id' => $proyekERP->id,
                'deskripsi' => 'Jasa konsultan UI/UX dan pengujian penetrasi keamanan sistem',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 2200000,
                'tanggal' => $now->copy()->startOfWeek()->addDays(2)->format('Y-m-d'),
                'kategori' => 'operasional',
                'proyek_id' => null,
                'deskripsi' => 'Belanja alat tulis kantor (ATK), tinta printer, dan snack pantry',
                'rekening_id' => $rekTunai,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 4200000,
                'tanggal' => $now->copy()->startOfWeek()->addDays(3)->format('Y-m-d'),
                'kategori' => 'proyek',
                'proyek_id' => $proyekMarketing->id,
                'deskripsi' => 'Cetak banner promosi, merchandise, dan konsumsi tim expo',
                'rekening_id' => $rekMandiri,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'masuk',
                'nominal' => 15000000,
                'tanggal' => $now->format('Y-m-d'),
                'kategori' => 'pendapatan',
                'proyek_id' => null,
                'deskripsi' => 'Penerimaan jasa kustomisasi fitur kasir toko cabang',
                'rekening_id' => $rekBCA,
                'verifikasi_langsung' => true,
                'status' => 'Sukses',
            ],
            [
                'tipe' => 'keluar',
                'nominal' => 3500000,
                'tanggal' => $now->format('Y-m-d'),
                'kategori' => 'operasional',
                'proyek_id' => null,
                'deskripsi' => 'Biaya transportasi dinas dan akomodasi meeting luar kota',
                'rekening_id' => $rekTunai,
                'verifikasi_langsung' => false,
                'status' => 'Pending',
            ],
        ];

        foreach ($transactions as $data) {
            Transaction::create($data);
        }
    }
}
