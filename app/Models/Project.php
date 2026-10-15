<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read int $spent
 * @property-read int $progress
 */
class Project extends Model
{
    use LogsActivity;

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

    /**
     * Audit trail perubahan proyek (anggaran, status, dsb).
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_proyek', 'kategori_proyek', 'pagu_anggaran', 'status', 'project_lead_id', 'tanggal_mulai', 'tanggal_selesai'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('proyek');
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'proyek_id');
    }

    /**
     * Total realisasi belanja proyek. Diisi oleh subquery withSum(), bernilai 0
     * jika proyek dimuat tanpa subquery tersebut.
     */
    protected function spent(): Attribute
    {
        return Attribute::get(fn (): int => (int) ($this->attributes['spent'] ?? 0));
    }

    /**
     * Persentase realisasi anggaran proyek (0-100).
     */
    protected function progress(): Attribute
    {
        return Attribute::get(fn (): int => $this->pagu_anggaran > 0
            ? (int) min(100, round(($this->spent / $this->pagu_anggaran) * 100))
            : 0);
    }
}
