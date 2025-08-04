<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Barang;

class RestockReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        return Barang::whereHas('stok', function($q) {
                $q->whereRaw('jumlah_stok <= barang.reorder_point');
            })
            ->with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->get()
            ->map(function ($barang) {
                $saranBeli = $barang->eoq_qty ?? ($barang->reorder_point * 2);
                $estimasiHarga = $saranBeli * $barang->harga_beli;
                
                return [
                    $barang->kode_barang,
                    $barang->nama_barang,
                    $barang->kategori->nama_kategori ?? '-',
                    $barang->stok ? $barang->stok->jumlah_stok : 0,
                    $barang->reorder_point,
                    $saranBeli,
                    $barang->harga_beli,
                    $estimasiHarga,
                    '-', // supplier terakhir
                    $barang->deskripsi ?? '-'
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Barang',
            'Kategori',
            'Stok Saat Ini',
            'Reorder Point',
            'Saran Qty Beli',
            'Harga Beli Satuan',
            'Estimasi Total Harga',
            'Supplier Terakhir',
            'Keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:J' => ['alignment' => ['wrapText' => true]],
            'G:H' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Laporan Belanja ' . now()->format('d-m-Y');
    }
}