<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Belanja - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #366092;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #366092;
            font-size: 18px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 11px;
            margin-top: 5px;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary-box h3 {
            margin: 0 0 10px 0;
            color: #198754;
            font-size: 14px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .budget-highlight {
            font-size: 16px;
            font-weight: bold;
            color: #198754;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section h3 {
            background-color: #366092;
            color: white;
            padding: 8px 12px;
            margin: 0 0 10px 0;
            font-size: 13px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .priority-urgent {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .priority-high {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .action-items {
            background-color: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 10px;
            margin: 15px 0;
        }
        
        .action-items h4 {
            margin: 0 0 8px 0;
            color: #1976d2;
            font-size: 12px;
        }
        
        .action-items ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .action-items li {
            margin-bottom: 3px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN BELANJA BENGKEL</h1>
        <div class="subtitle">
            Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}<br>
            Digenerate: {{ $generated_at }} oleh {{ $generated_by }}
        </div>
    </div>

    <!-- Budget Summary -->
    <div class="summary-box">
        <h3>Ringkasan Budget</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Total Items Harus Dibeli:</strong><br>
                <span class="budget-highlight">{{ count($restock['barang_harus_beli']) }} items</span>
            </div>
            <div class="summary-item">
                <strong>Estimasi Budget Diperlukan:</strong><br>
                <span class="budget-highlight">Rp {{ number_format($restock['estimasi_budget'], 0, ',', '.') }}</span>
            </div>
        </div>
        <div style="margin-top: 10px;">
            <strong>Total Pembelian Bulan Ini:</strong> Rp {{ number_format($restock['total_pembelian_bulan_ini'], 0, ',', '.') }}
        </div>
    </div>

    <!-- Action Items -->
    <div class="action-items">
        <h4>Action Items</h4>
        <ul>
            <li>Update harga jual jika diperlukan setelah pembelian</li>
            <li>Review supplier alternatif untuk optimasi harga</li>
            <li>Koordinasi dengan gudang untuk penyimpanan barang</li>
            <li>Update EOQ parameter berdasarkan trend pembelian</li>
        </ul>
    </div>

    <!-- Daftar Belanja -->
    <div class="section">
        <h3>Daftar Belanja Priority</h3>
        <table>
            <thead>
                <tr>
                    <th width="8%">Priority</th>
                    <th width="12%">Kode</th>
                    <th width="25%">Nama Barang</th>
                    <th width="15%">Kategori</th>
                    <th width="8%">Stok</th>
                    <th width="8%">ROP</th>
                    <th width="8%">Saran Beli</th>
                    <th width="16%">Estimasi Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach($restock['barang_harus_beli'] as $index => $item)
                    @php
                        $stokSaatIni = $item['stok']['jumlah_stok'] ?? 0;
                        $saranBeli = $item['eoq_qty'] ?? ($item['reorder_point'] * 2);
                        $estimasiHarga = $saranBeli * $item['harga_beli'];
                        $priority = $stokSaatIni <= 0 ? 'URGENT' : ($stokSaatIni <= ($item['reorder_point'] * 0.5) ? 'HIGH' : 'MEDIUM');
                        $priorityClass = $stokSaatIni <= 0 ? 'priority-urgent' : ($stokSaatIni <= ($item['reorder_point'] * 0.5) ? 'priority-high' : '');
                    @endphp
                    <tr class="{{ $priorityClass }}">
                        <td class="text-center font-bold">{{ $priority }}</td>
                        <td class="font-bold">{{ $item['kode_barang'] }}</td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td>{{ $item['kategori']['nama_kategori'] ?? '-' }}</td>
                        <td class="text-center">{{ $stokSaatIni }}</td>
                        <td class="text-center">{{ $item['reorder_point'] }}</td>
                        <td class="text-center font-bold">{{ $saranBeli }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($estimasiHarga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="7" class="text-right">TOTAL ESTIMASI:</td>
                    <td class="text-right">Rp {{ number_format($restock['estimasi_budget'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if(count($restock['history_pembelian_bulan_ini']) > 0)
    <!-- History Pembelian -->
    <div class="section page-break">
        <h3>History Pembelian Bulan Ini</h3>
        <table>
            <thead>
                <tr>
                    <th width="15%">Tanggal</th>
                    <th width="20%">No. Masuk</th>
                    <th width="25%">Supplier</th>
                    <th width="20%">Total Nilai</th>
                    <th width="20%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($restock['history_pembelian_bulan_ini'] as $purchase)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($purchase['tanggal_masuk'])->format('d/m/Y') }}</td>
                    <td class="font-bold">{{ $purchase['nomor_masuk'] }}</td>
                    <td>{{ $purchase['supplier'] ?? '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($purchase['total_nilai'], 0, ',', '.') }}</td>
                    <td>{{ $purchase['keterangan'] ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="3" class="text-right">TOTAL PEMBELIAN BULAN INI:</td>
                    <td class="text-right">Rp {{ number_format($restock['total_pembelian_bulan_ini'], 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <!-- Rekomendasi -->
    <div class="section">
        <h3>Rekomendasi Aksi</h3>
        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 5px;">
            <p style="margin: 0; margin-bottom: 8px;"><strong>Immediate Actions:</strong></p>
            <ul style="margin: 0; padding-left: 20px;">
                @php $urgentItems = array_filter($restock['barang_harus_beli'], function($item) { return ($item['stok']['jumlah_stok'] ?? 0) <= 0; }); @endphp
                @if(count($urgentItems) > 0)
                    <li><strong>{{ count($urgentItems) }} items</strong> dengan stok habis - beli segera!</li>
                @endif
                <li>Hubungi supplier untuk konfirmasi ketersediaan dan harga</li>
                <li>Siapkan budget sebesar <strong>Rp {{ number_format($restock['estimasi_budget'], 0, ',', '.') }}</strong></li>
                <li>Print checklist ini untuk dibawa ke supplier</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        Report generated by Bengkel Inventory System | {{ $generated_at }} | 
        For internal use only - Confidential
    </div>
</body>
</html>