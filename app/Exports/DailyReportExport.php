<?php
// File: app/Exports/DailyReportExport.php

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




class DailyReportExport implements WithMultipleSheets
{
    use Exportable;
    
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function sheets(): array
    {
        return [
            new DailyTransactionsSheet($this->date),
            new DailyItemsSheet($this->date),
        ];
    }
}

class DailyTransactionsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
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
                    Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y'),
                    Carbon::parse($trx->tanggal_transaksi)->format('H:i'),
                    $trx->nama_customer ?? '-',
                    $trx->kasir->name ?? '-',
                    $trx->status_transaksi,
                    $trx->total_harga,
                    $trx->kendaraan ?? '-',
                    $trx->keterangan ?? '-'
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No Transaksi',
            'Tanggal',
            'Waktu',
            'Customer',
            'Kasir',
            'Status',
            'Total Harga',
            'Kendaraan',
            'Keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:I' => ['alignment' => ['wrapText' => true]],
            'G:G' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Transaksi ' . Carbon::parse($this->date)->format('d-m-Y');
    }
}

class DailyItemsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        $targetDate = Carbon::parse($this->date);
        
        return DetailTransaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
            ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
            ->leftJoin('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
            ->whereDate('transaksi.tanggal_transaksi', $targetDate)
            ->select([
                'transaksi.nomor_transaksi',
                'barang.kode_barang',
                'barang.nama_barang',
                'kategori_barang.nama_kategori',
                'detail_transaksi.qty',
                'detail_transaksi.harga_satuan',
                'detail_transaksi.subtotal',
                'detail_transaksi.status_permintaan'
            ])
            ->orderBy('transaksi.nomor_transaksi')
            ->get()
            ->map(function ($detail) {
                return [
                    $detail->nomor_transaksi,
                    $detail->kode_barang ?? '-',
                    $detail->nama_barang ?? '-',
                    $detail->nama_kategori ?? '-',
                    $detail->qty,
                    $detail->harga_satuan,
                    $detail->subtotal,
                    $detail->status_permintaan
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No Transaksi',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Qty',
            'Harga Satuan',
            'Subtotal',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:H' => ['alignment' => ['wrapText' => true]],
            'F:G' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Detail Items ' . Carbon::parse($this->date)->format('d-m-Y');
    }

    
    

}