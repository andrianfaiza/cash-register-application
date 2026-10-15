<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KasTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_user_can_create_and_manage_transactions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/transaksi', [
            'tipe' => 'masuk',
            'nominal' => 500000,
            'tanggal' => '2026-09-22',
            'kategori' => 'pendapatan',
            'deskripsi' => 'Setoran Awal Kas',
            'rekening_id' => 'kas-besar',
            'verifikasi_langsung' => 1,
        ]);

        $response->assertRedirect('/transaksi');
        $this->assertDatabaseHas('transactions', [
            'nominal' => 500000,
            'deskripsi' => 'Setoran Awal Kas',
        ]);
    }

    public function test_user_can_create_and_manage_projects(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/proyek', [
            'nama_proyek' => 'Proyek Gedung Baru',
            'kategori_proyek' => 'renovasi',
            'pagu_anggaran' => 100000000,
            'deskripsi' => 'Renovasi gedung utama',
        ]);

        $response->assertRedirect('/proyek');
        $this->assertDatabaseHas('projects', [
            'nama_proyek' => 'Proyek Gedung Baru',
        ]);
    }

    public function test_user_can_update_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/settings', [
            'bahasa' => 'id',
            'mata_uang' => 'idr',
            'format_tanggal' => 'dd/mm/yyyy',
            'mode_tampilan' => 'dark',
            'notif_email' => 1,
            'notif_sistem' => 1,
        ]);

        $response->assertRedirect('/settings');
        $this->assertDatabaseHas('settings', [
            'mode_tampilan' => 'dark',
        ]);
    }

    public function test_user_can_search(): void
    {
        $user = User::factory()->create();
        Transaction::create([
            'tipe' => 'masuk',
            'nominal' => 250000,
            'tanggal' => '2026-09-22',
            'kategori' => 'operasional',
            'deskripsi' => 'Pembelian Semen khusus',
            'status' => 'Sukses',
            'verifikasi_langsung' => 1,
        ]);

        $response = $this->actingAs($user)->get('/search?q=Semen');
        $response->assertStatus(200);
        $response->assertSee('Pembelian Semen khusus');
    }

    public function test_search_paginates_matching_transactions(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 18) as $i) {
            Transaction::create([
                'tipe' => 'masuk',
                'nominal' => 1000 * $i,
                'tanggal' => '2026-09-22',
                'kategori' => 'operasional',
                'deskripsi' => "Pembelian material batch {$i}",
                'status' => 'Sukses',
                'verifikasi_langsung' => 1,
            ]);
        }

        $firstPage = $this->actingAs($user)->get('/search?q=batch');
        $firstPage->assertStatus(200);
        $firstPage->assertSee('Ditemukan 18 transaksi');
        $firstPage->assertSee('Menampilkan 15 dari 18 transaksi');

        $secondPage = $this->actingAs($user)->get('/search?q=batch&transaksi_page=2');
        $secondPage->assertStatus(200);
        $secondPage->assertSee('Menampilkan 3 dari 18 transaksi');
    }

    public function test_search_treats_like_wildcards_as_literal_text(): void
    {
        $user = User::factory()->create();

        Transaction::create([
            'tipe' => 'masuk',
            'nominal' => 250000,
            'tanggal' => '2026-09-22',
            'kategori' => 'operasional',
            'deskripsi' => 'Transaksi biasa',
            'status' => 'Sukses',
            'verifikasi_langsung' => 1,
        ]);

        // Tanda "%" harus dicari sebagai karakter literal, bukan wildcard.
        $response = $this->actingAs($user)->get('/search?q=%25');

        $response->assertStatus(200);
        $response->assertSee('Ditemukan 0 transaksi dan 0 proyek');
    }

    public function test_deleting_transaction_soft_deletes_records_activity_and_keeps_proof(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('bukti-transaksi/receipt.jpg', 'dummy-content');

        $user = User::factory()->create();
        $transaction = Transaction::create([
            'tipe' => 'keluar',
            'nominal' => 150000,
            'tanggal' => '2026-09-22',
            'kategori' => 'operasional',
            'deskripsi' => 'Pembelian ATK',
            'status' => 'Sukses',
            'verifikasi_langsung' => 1,
            'bukti' => 'bukti-transaksi/receipt.jpg',
        ]);

        $response = $this->actingAs($user)->delete('/transaksi', ['ids' => [$transaction->id]]);

        $response->assertRedirect('/transaksi');
        $this->assertSoftDeleted('transactions', ['id' => $transaction->id]);

        // Bukti tetap tersimpan agar transaksi masih bisa dipulihkan.
        Storage::disk('public')->assertExists('bukti-transaksi/receipt.jpg');

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'transaksi',
            'event' => 'deleted',
            'subject_type' => Transaction::class,
            'subject_id' => $transaction->id,
        ]);
    }

    public function test_force_deleting_transaction_removes_proof_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('bukti-transaksi/receipt.jpg', 'dummy-content');

        $transaction = Transaction::create([
            'tipe' => 'keluar',
            'nominal' => 150000,
            'tanggal' => '2026-09-22',
            'kategori' => 'operasional',
            'deskripsi' => 'Pembelian ATK',
            'status' => 'Sukses',
            'verifikasi_langsung' => 1,
            'bukti' => 'bukti-transaksi/receipt.jpg',
        ]);

        $transaction->forceDelete();

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        Storage::disk('public')->assertMissing('bukti-transaksi/receipt.jpg');
    }

    public function test_user_can_export_reports(): void
    {
        $user = User::factory()->create();

        $responseExcel = $this->actingAs($user)->get('/laporan/export-excel');
        $responseExcel->assertStatus(200);
        $responseExcel->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $responsePdf = $this->actingAs($user)->get('/laporan/export-pdf');
        $responsePdf->assertStatus(200);
        $responsePdf->assertSee('LAPORAN KAS PERUSAHAAN');
    }
}
