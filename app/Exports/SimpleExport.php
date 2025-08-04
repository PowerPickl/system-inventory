<?php
// File: app/Exports/SimpleExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SimpleExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $data;
    protected $headings;
    protected $title;

    public function __construct($data, $headings, $title = 'Export')
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:Z' => ['alignment' => ['wrapText' => true]]
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    /**
     * ALTERNATIVE: Simple Excel Export (more reliable)
     */
    public function exportSimpleExcel(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        try {
            switch($type) {
                case 'daily':
                    $data = $this->getDailyExcelData($date);
                    $filename = 'laporan-harian-' . $date . '.xlsx';
                    break;
                    
                case 'stock':
                    $data = $this->getStockExcelData();
                    $filename = 'laporan-stok-' . $date . '.xlsx';
                    break;
                    
                case 'restock':
                    $data = $this->getRestockExcelData();
                    $filename = 'laporan-belanja-' . $date . '.xlsx';
                    break;
                    
                case 'monthly':
                    $month = $request->get('month', now()->format('Y-m'));
                    $data = $this->getMonthlyExcelData($month);
                    $filename = 'laporan-bulanan-' . $month . '.xlsx';
                    break;
                    
                default:
                    return response()->json(['error' => 'Invalid type'], 400);
            }
            
            return Excel::download(
                new \App\Exports\SimpleExport($data['rows'], $data['headings'], $data['title']), 
                $filename
            );
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getDailyExcelData($date)
    {
        $targetDate = Carbon::parse($date);
        
        $transactions = Transaksi::whereDate('tanggal_transaksi', $targetDate)
            ->with(['kasir'])
            ->orderBy('created_at', 'desc')
            ->get();

        $rows = [];
        foreach($transactions as $trx) {
            $rows[] = [
                $trx->nomor_transaksi,
                Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y'),
                Carbon::parse($trx->tanggal_transaksi)->format('H:i'),
                $trx->nama_customer ?? '-',
                $trx->kasir->name ?? '-',
                $trx->status_transaksi,
                $trx->total_harga,
                $trx->kendaraan ?? '-'
            ];
        }

        return [
            'rows' => $rows,
            'headings' => ['No Transaksi', 'Tanggal', 'Waktu', 'Customer', 'Kasir', 'Status', 'Total', 'Kendaraan'],
            'title' => 'Transaksi ' . $targetDate->format('d-m-Y')
        ];
    }

    private function getStockExcelData()
    {
        $barangList = Barang::with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->orderBy('nama_barang')
            ->get();

        $rows = [];
        foreach($barangList as $barang) {
            $stok = $barang->stok;
            $nilaiStok = ($stok ? $stok->jumlah_stok : 0) * $barang->harga_beli;
            
            $rows[] = [
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
        }

        return [
            'rows' => $rows,
            'headings' => ['Kode', 'Nama', 'Kategori', 'Satuan', 'Stok', 'Status', 'Harga Beli', 'Harga Jual', 'ROP', 'Nilai'],
            'title' => 'Stock ' . now()->format('d-m-Y')
        ];
    }

    private function getRestockExcelData()
    {
        $barangHarusBeli = Barang::whereHas('stok', function($q) {
                $q->whereRaw('jumlah_stok <= barang.reorder_point');
            })
            ->with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->get();

        $rows = [];
        foreach($barangHarusBeli as $barang) {
            $saranBeli = $barang->eoq_qty ?? ($barang->reorder_point * 2);
            $estimasiHarga = $saranBeli * $barang->harga_beli;
            
            $rows[] = [
                $barang->kode_barang,
                $barang->nama_barang,
                $barang->kategori->nama_kategori ?? '-',
                $barang->stok ? $barang->stok->jumlah_stok : 0,
                $barang->reorder_point,
                $saranBeli,
                $barang->harga_beli,
                $estimasiHarga
            ];
        }

        return [
            'rows' => $rows,
            'headings' => ['Kode', 'Nama', 'Kategori', 'Stok', 'ROP', 'Saran Beli', 'Harga', 'Estimasi'],
            'title' => 'Belanja ' . now()->format('d-m-Y')
        ];
    }

    private function getMonthlyExcelData($month)
    {
        $targetMonth = Carbon::createFromFormat('Y-m', $month);
        $startDate = $targetMonth->copy()->startOfMonth();
        $endDate = $targetMonth->copy()->endOfMonth();

        $transactions = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->with(['kasir'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $rows = [];
        foreach($transactions as $trx) {
            $rows[] = [
                Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y'),
                $trx->nomor_transaksi,
                $trx->nama_customer ?? '-',
                $trx->kasir->name ?? '-',
                $trx->status_transaksi,
                $trx->total_harga
            ];
        }

        return [
            'rows' => $rows,
            'headings' => ['Tanggal', 'No Transaksi', 'Customer', 'Kasir', 'Status', 'Total'],
            'title' => 'Monthly ' . $targetMonth->format('M-Y')
        ];
    }
}