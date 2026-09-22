<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kas Perusahaan - {{ $start->format('d M Y') }} s/d {{ $end->format('d M Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0f172a; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #0f172a; }
        .header p { margin: 4px 0 0 0; font-size: 12px; color: #64748b; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .card { border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; width: 30%; }
        .card-title { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .card-value { font-size: 16px; font-weight: bold; margin-top: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; }
        th { background-color: #f8fafc; font-size: 11px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .masuk { color: #16a34a; }
        .keluar { color: #dc2626; }
        .print-btn { background: #0f172a; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 12px; margin-bottom: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
    </div>

    <div class="header">
        <h1>LAPORAN KAS PERUSAHAAN</h1>
        <p>PT. WINNER NUSANTARA JAYA</p>
        <p>Periode: {{ $start->translatedFormat('d F Y') }} - {{ $end->translatedFormat('d F Y') }}</p>
    </div>

    <table style="width: 100%; margin-bottom: 25px;">
        <tr>
            <td style="border:none; padding: 0;">
                <div class="card" style="width: 90%;">
                    <div class="card-title">Total Pemasukan</div>
                    <div class="card-value masuk">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="border:none; padding: 0;">
                <div class="card" style="width: 90%;">
                    <div class="card-title">Total Pengeluaran</div>
                    <div class="card-value keluar">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="border:none; padding: 0;">
                <div class="card" style="width: 90%;">
                    <div class="card-title">Saldo Bersih</div>
                    <div class="card-value">Rp {{ number_format($totalMasuk - $totalKeluar, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <h3>Rincian Transaksi Kas Sukses</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $index => $t)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($t->tipe) }}</td>
                    <td>{{ ucfirst($t->kategori) }}</td>
                    <td>{{ $t->deskripsi ?: '-' }}</td>
                    <td class="text-right {{ $t->tipe === 'masuk' ? 'masuk' : 'keluar' }}">
                        {{ $t->tipe === 'masuk' ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
