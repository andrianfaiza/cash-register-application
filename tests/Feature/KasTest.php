<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
