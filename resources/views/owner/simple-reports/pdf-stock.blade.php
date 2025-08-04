<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #ffc107;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            color: #ffc107;
            font-size: 18px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 11px;
            margin-top: 5px;
        }
        
        .summary-cards {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 10px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            background-color: #f8f9fa;
        }
        
        .summary-card h4 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #666;
        }
        
        .summary-card .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        
        .summary-card.aman .value { color: #198754; }
        .summary-card.restock .value { color: #fd7e14; }
        .summary-card.habis .value { color: #dc3545; }
        .summary-card.total .value { color: #0d6efd; }
        
        .value-highlight {
            background-color: #e3f2fd;
            border: 1px solid #1976d2;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        
        .value-highlight h3 {
            margin: 0 0 5px 0;
            color: #1976d2;
            font-size: 14px;
        }
        
        .value-highlight .amount {
            font-size: 18px;
            font-weight: bold;
            color: #1976d2;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section h3 {
            background-color: #ffc107;
            color: #333;
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
        
        .status-aman { background-color: #d1edff; color: #0c5460; }
        .status-restock { background-color: #fff3cd; color: #856404; }
        .status-habis { background-color: #f8d7da; color: #721c24; }
        
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
        
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
        }
        
        .alert-box h4 {
            margin: 0 0 8px 0;
            color: #856404;
            font-size: 12px;
        }
        
        .top-items {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .top-items .item {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            padding: 5px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            background-color: white;
        }
        
        .top-items .item .rank {
            display: table-cell;
            width: 30px;
            text-align: center;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .top-items .item .info {
            display: table-cell;
            vertical-align: middle;
        }
        
        .top-items .item .value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1> LAPORAN STOK INVENTORY</h1>
        <div class="subtitle">
            Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}<br>
            Digenerate: {{ $generated_at }} oleh {{ $generated_by }}
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card total">
            <h4>Total Items</h4>
            <div class="value">{{ $stock['summary']['total_items'] }}</div>
        </div>
        <div class="summary-card aman">
            <h4>Stok Aman</h4>
            <div class="value">{{ $stock['summary']['stok_aman'] }}</div>
        </div>
        <div class="summary-card restock">
            <h4>Perlu Restock</h4>
            <div class="value">{{ $stock['summary']['stok_perlu_restock'] }}</div>
        </div>
        <div class="summary-card habis">
            <h4>Stok Habis</h4>
            <div class="value">{{ $stock['summary']['stok_habis'] }}</div>
        </div>
    </div>

    <!-- Total Value -->
    <div class="value-highlight">
        <h3> Total Nilai Inventory</h3>
        <div class="amount">Rp {{ number_format($stock['summary']['total_value'], 0, ',', '.') }}</div>
    </div>

    @if($stock['summary']['stok_habis'] > 0 || $stock['summary']['stok_perlu_restock'] > 0)
    <!-- Alert Box -->
    <div class="alert-box">
        <h4> Attention Required</h4>
        @if($stock['summary']['stok_habis'] > 0)
            <p style="margin: 5px 0; color: #dc3545; font-weight: bold;">
                 {{ $stock['summary']['stok_habis'] }} items habis stok - perlu restock segera!
            </p>
        @endif
        @if($stock['summary']['stok_perlu_restock'] > 0)
            <p style="margin: 5px 0; color: #fd7e14; font-weight: bold;">
                 {{ $stock['summary']['stok_perlu_restock'] }} items perlu restock dalam waktu dekat
            </p>
        @endif
    </div>
    @endif

    <!-- Breakdown per Kategori -->
    <div class="section">
        <h3>Breakdown per Kategori</h3>
        <table>
            <thead>
                <tr>
                    <th width="25%">Kategori</th>
                    <th width="12%">Total Items</th>
                    <th width="12%">Total Qty</th>
                    <th width="20%">Nilai (Rp)</th>
                    <th width="31%">Status Distribution</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stock['per_kategori'] as $kategori)
                <tr>
                    <td class="font-bold">{{ $kategori['nama_kategori'] }}</td>
                    <td class="text-center">{{ $kategori['total_items'] }}</td>
                    <td class="text-center">{{ number_format($kategori['total_qty']) }}</td>
                    <td class="text-right font-bold">{{ number_format($kategori['total_value'], 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span style="color: #198754;">{{ $kategori['stok_aman'] }} Aman</span> | 
                        <span style="color: #fd7e14;">{{ $kategori['stok_restock'] }} Restock</span> | 
                        <span style="color: #dc3545;">{{ $kategori['stok_habis'] }} Habis</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top Value Items -->
    <div class="section">
        <h3>Top 10 Items by Value</h3>
        <div class="top-items">
            @foreach($stock['top_value_items'] as $index => $item)
            <div class="item">
                <div class="rank">{{ $index + 1 }}</div>
                <div class="info">
                    <strong>{{ $item['nama_barang'] }}</strong><br>
                    <small>{{ $item['kode_barang'] }} • {{ $item['nama_kategori'] }}</small><br>
                    <small>{{ number_format($item['jumlah_stok']) }} × Rp {{ number_format($item['harga_beli'], 0, ',', '.') }}</small>
                </div>
                <div class="value">
                    Rp {{ number_format($item['total_value'], 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if(count($stock['slow_moving']) > 0)
    <!-- Slow Moving Items -->
    <div class="section page-break">
        <h3>Barang Slow Moving (30 hari terakhir)</h3>
        <p style="font-size: 11px; color: #666; margin-bottom: 10px;">
            Items berikut tidak ada pergerakan dalam 30 hari terakhir. Pertimbangkan untuk promosi atau evaluasi pricing.
        </p>
        <table>
            <thead>
                <tr>
                    <th width="15%">Kode</th>
                    <th width="30%">Nama Barang</th>
                    <th width="20%">Kategori</th>
                    <th width="12%">Stok</th>
                    <th width="23%">Nilai Stok</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stock['slow_moving'] as $item)
                <tr style="background-color: #fff3cd;">
                    <td class="font-bold">{{ $item['kode_barang'] }}</td>
                    <td>{{ $item['nama_barang'] }}</td>
                    <td>{{ $item['nama_kategori'] }}</td>
                    <td class="text-center">{{ number_format($item['jumlah_stok']) }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($item['nilai_stok'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td colspan="4" class="text-right">TOTAL NILAI SLOW MOVING:</td>
                    <td class="text-right">Rp {{ number_format(array_sum(array_column($stock['slow_moving'], 'nilai_stok')), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        
        <div style="background-color: #e3f2fd; border: 1px solid #1976d2; padding: 10px; border-radius: 5px; margin-top: 10px;">
            <h4 style="margin: 0 0 8px 0; color: #1976d2;"> Rekomendasi untuk Slow Moving Items:</h4>
            <ul style="margin: 0; padding-left: 20px; font-size: 10px;">
                <li>Buat promosi khusus atau diskon untuk mempercepat perputaran</li>
                <li>Review pricing strategy - mungkin harga terlalu tinggi</li>
                <li>Pertimbangkan bundling dengan items yang fast-moving</li>
                <li>Evaluasi apakah items ini masih dibutuhkan di inventory</li>
            </ul>
        </div>
    </div>
    @else
    <div style="background-color: #d1edff; border: 1px solid #0c5460; padding: 10px; border-radius: 5px; margin: 15px 0; text-align: center;">
        <strong>Excellent! Semua barang bergerak dengan baik dalam 30 hari terakhir!</strong>
    </div>
    @endif

    <!-- Action Items -->
    <div class="section">
        <h3>Action Items</h3>
        <div style="background-color: #e3f2fd; border: 1px solid #1976d2; padding: 10px; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 20px; font-size: 11px;">
                <li>Review items dengan status "Perlu Restock" untuk planning pembelian</li>
                <li>Immediate action untuk {{ $stock['summary']['stok_habis'] }} items yang habis stok</li>
                @if(count($stock['slow_moving']) > 0)
                    <li>Buat strategy untuk {{ count($stock['slow_moving']) }} slow-moving items</li>
                @endif
                <li>Monitor total inventory value: Rp {{ number_format($stock['summary']['total_value'], 0, ',', '.') }}</li>
                <li>Update EOQ parameters berdasarkan trend pergerakan stok</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        Report generated by Bengkel Inventory System | {{ $generated_at }} | 
        Total Inventory Value: Rp {{ number_format($stock['summary']['total_value'], 0, ',', '.') }}
    </div>
</body>
</html>