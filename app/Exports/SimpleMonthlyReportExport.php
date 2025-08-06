<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Transaksi;
use Carbon\Carbon;

class SimpleMonthlyReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function collection()
    {
        $targetMonth = Carbon::createFromFormat('Y-m', $this->month);
        $startDate = $targetMonth->copy()->startOfMonth();
        $endDate = $targetMonth->copy()->endOfMonth();

        return Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->with(['kasir'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get()
            ->map(function ($trx) {
                return [
                    Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y'),
                    $trx->nomor_transaksi,
                    $trx->nama_customer ?? '-',
                    $trx->kasir->name ?? '-',
                    $trx->status_transaksi,
                    $trx->total_harga
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No Transaksi', 
            'Customer',
            'Kasir',
            'Status',
            'Total Harga'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:F' => ['alignment' => ['wrapText' => true]],
            'F:F' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Laporan Bulanan ' . Carbon::createFromFormat('Y-m', $this->month)->format('M-Y');
    }
}
