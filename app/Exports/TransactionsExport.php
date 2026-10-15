<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return Transaction::query()
            ->with('project')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');
    }

    public function headings(): array
    {
        return ['ID', 'Tanggal', 'Tipe', 'Kategori', 'Proyek', 'Rekening', 'Status', 'Nominal', 'Deskripsi'];
    }

    /**
     * @param  Transaction  $transaction
     */
    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->tanggal->format('Y-m-d'),
            ucfirst($transaction->tipe),
            ucfirst($transaction->kategori),
            $transaction->project?->nama_proyek ?: 'Non-Proyek',
            $transaction->rekening_id ?? '-',
            $transaction->status,
            $transaction->nominal,
            $transaction->deskripsi,
        ];
    }
}
