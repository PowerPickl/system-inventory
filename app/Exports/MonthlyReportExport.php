<?php
// File: app/Exports/MonthlyReportExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use App\Models\BarangMasuk;
use Carbon\Carbon;




class MonthlyReportExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function sheets(): array
    {
        return [
            new MonthlyTransactionsSheet($this->month),
            new MonthlyPurchasesSheet($this->month),
            new MonthlySummarySheet($this->month),
        ];
    }
}

class MonthlyTransactionsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
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
                    $trx->total_harga,
                    $trx->kendaraan ?? '-'
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
            'Total Harga',
            'Kendaraan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:G' => ['alignment' => ['wrapText' => true]],
            'F:F' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Transaksi ' . Carbon::createFromFormat('Y-m', $this->month)->format('M-Y');
    }
}

class MonthlyPurchasesSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
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

        return BarangMasuk::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->orderBy('tanggal_masuk', 'desc')
            ->get()
            ->map(function ($purchase) {
                return [
                    Carbon::parse($purchase->tanggal_masuk)->format('d/m/Y'),
                    $purchase->nomor_masuk,
                    $purchase->supplier ?? '-',
                    $purchase->total_nilai,
                    $purchase->keterangan ?? '-'
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No Masuk',
            'Supplier',
            'Total Nilai',
            'Keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:E' => ['alignment' => ['wrapText' => true]],
            'D:D' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Pembelian ' . Carbon::createFromFormat('Y-m', $this->month)->format('M-Y');
    }
}

class MonthlySummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle
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

        // Calculate summary
        $totalOmzet = Transaksi::where('status_transaksi', 'Selesai')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('total_harga');

        $totalPengeluaran = BarangMasuk::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->sum('total_nilai');

        $totalTransaksi = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->count();

        $profitEstimation = $totalOmzet - $totalPengeluaran;

        return collect([
            ['Metric', 'Value'],
            ['Total Omzet', $totalOmzet],
            ['Total Pengeluaran Beli Barang', $totalPengeluaran],
            ['Profit Estimation', $profitEstimation],
            ['Total Transaksi', $totalTransaksi],
            ['Periode', $targetMonth->format('F Y')]
        ]);
    }

    public function headings(): array
    {
        return []; // No additional headings since we include them in data
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:B' => ['alignment' => ['wrapText' => true]],
            'B2:B5' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Summary ' . Carbon::createFromFormat('Y-m', $this->month)->format('M-Y');
    }
}