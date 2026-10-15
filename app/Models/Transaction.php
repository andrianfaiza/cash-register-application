<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property-read Project|null $project
 */
class Transaction extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'tipe', 'nominal', 'tanggal', 'kategori', 'proyek_id', 'deskripsi',
        'rekening_id', 'verifikasi_langsung', 'status', 'bukti',
    ];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'nominal' => 'integer', 'verifikasi_langsung' => 'boolean'];
    }

    /**
     * Audit trail perubahan transaksi kas (siapa mengubah/menghapus apa).
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tipe', 'nominal', 'tanggal', 'kategori', 'proyek_id', 'deskripsi', 'rekening_id', 'status', 'bukti'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('transaksi');
    }

    /**
     * Hapus file bukti hanya saat transaksi dihapus permanen (force delete),
     * supaya transaksi yang di-soft delete masih bisa dipulihkan beserta buktinya.
     */
    protected static function booted(): void
    {
        static::forceDeleted(function (Transaction $transaction): void {
            if ($transaction->bukti) {
                Storage::disk(config('filesystems.receipts'))->delete($transaction->bukti);
            }
        });
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'proyek_id');
    }
}
