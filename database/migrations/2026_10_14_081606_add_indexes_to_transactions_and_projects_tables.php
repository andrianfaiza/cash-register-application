<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Mempercepat filter & agregat dashboard/laporan (status, tipe, tanggal, kategori, rekening).
            $table->index('tanggal');
            $table->index('status');
            $table->index('tipe');
            $table->index('kategori');
            $table->index('rekening_id');
            $table->index(['status', 'tanggal']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('status');
            $table->index('project_lead_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['status']);
            $table->dropIndex(['tipe']);
            $table->dropIndex(['kategori']);
            $table->dropIndex(['rekening_id']);
            $table->dropIndex(['status', 'tanggal']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['project_lead_id']);
        });
    }
};
