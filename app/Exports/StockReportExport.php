<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Barang;

class StockReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        return Barang::with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->orderBy('nama_barang')
            ->get()
            ->map(function ($barang) {
                $stok = $barang->stok;
                $nilaiStok = ($stok ? $stok->jumlah_stok : 0) * $barang->harga_beli;
                
                return [
                    $barang->kode_barang,
                    $barang->nama_barang,
                    $barang->kategori->nama_kategori ?? '-',
                    $barang->satuan,
                    $stok ? $stok->jumlah_stok : 0,
                    $stok ? $stok->status_stok : 'No Stock',
                    $barang->harga_beli,
                    $barang->harga_jual,
                    $barang->reorder_point,
                    $nilaiStok
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Jumlah Stok',
            'Status Stok',
            'Harga Beli',
            'Harga Jual',
            'Reorder Point',
            'Nilai Stok'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:J' => ['alignment' => ['wrapText' => true]],
            'G:H' => ['numberFormat' => ['formatCode' => '#,##0']],
            'J:J' => ['numberFormat' => ['formatCode' => '#,##0']]
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok ' . now()->format('d-m-Y');
    }
}
