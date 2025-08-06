<?php
// File: app/Exports/SimpleDailyReportExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Transaksi;
use Carbon\Carbon;

class SimpleDailyReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        $targetDate = Carbon::parse($this->date);
        
        return Transaksi::whereDate('tanggal_transaksi', $targetDate)
            ->with(['kasir'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($trx) {
                return [
                    $trx->nomor_transaksi,
                    Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y H:i'),
                    $trx->nama_customer ?? '-',
                    $trx->kasir->name ?? '-',
                    $trx->status_transaksi,
                    $trx->total_harga,
                    $trx->kendaraan ?? '-'
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No Transaksi',
            'Tanggal & Waktu',
            'Customer',
            'Kasir',
            'Status',
            'Total Harga',
            'Kendaraan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:G' => ['alignment' => ['wrapText' => true]],
            'F:F' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Laporan Harian ' . Carbon::parse($this->date)->format('d-m-Y');
    }
}