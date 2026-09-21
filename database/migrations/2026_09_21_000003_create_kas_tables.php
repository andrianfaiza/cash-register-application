<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek');
            $table->string('kategori_proyek');
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('pagu_anggaran')->default(0);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->unsignedBigInteger('project_lead_id')->nullable();
            $table->json('departemen')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tipe');
            $table->unsignedBigInteger('nominal');
            $table->date('tanggal');
            $table->string('kategori');
            $table->foreignId('proyek_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->string('rekening_id')->nullable();
            $table->boolean('verifikasi_langsung')->default(true);
            $table->string('status')->default('Sukses');
            $table->string('bukti')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('bahasa')->default('id');
            $table->string('mata_uang')->default('idr');
            $table->string('format_tanggal')->default('dd/mm/yyyy');
            $table->string('mode_tampilan')->default('light');
            $table->boolean('notif_email')->default(true);
            $table->boolean('notif_sistem')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('settings');
    }
};