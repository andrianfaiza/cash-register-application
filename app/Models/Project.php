<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'nama_proyek', 'kategori_proyek', 'deskripsi', 'pagu_anggaran', 'tanggal_mulai',
        'tanggal_selesai', 'project_lead_id', 'departemen', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date', 'tanggal_selesai' => 'date',
            'pagu_anggaran' => 'integer', 'departemen' => 'array',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'proyek_id');
    }
}