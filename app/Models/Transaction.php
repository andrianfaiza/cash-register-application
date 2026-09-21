<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'tipe', 'nominal', 'tanggal', 'kategori', 'proyek_id', 'deskripsi',
        'rekening_id', 'verifikasi_langsung', 'status', 'bukti',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'nominal' => 'integer', 'verifikasi_langsung' => 'boolean'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'proyek_id');
    }
}