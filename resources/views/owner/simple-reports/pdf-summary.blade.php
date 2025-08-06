<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Harian Bengkel - {{ $date }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 9px;
        }
        .summary-section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            padding: 3px 0;
            border-bottom: 1px solid #ddd;
            color: #2563eb;
        }
        .summary-grid {
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-row {
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        .summary-cell {
            display: table-cell;
            width: 25%;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            background: #f9f9f9;
            vertical-align: top;
        }
        .summary-value {
            font-size: 12px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 3px;
        }
        .summary-label {
            font-size: 8px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-size: 8px;
        }
        table th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .status-selesai {
            color: #059669;
            font-weight: bold;
        }
        .status-progress {
            color: #d97706;
            font-weight: bold;
        }
        .alert-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 8px;
            margin: 8px 0;
            font-size: 8px;
        }
        .alert-item {
            margin: 3px 0;
        }
        .footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        .two-column {
            width: 100%;
        }
        .column {
            width: 48%;
            float: left;
            margin-right: 2%;
        }
        .column:last-child {
            margin-right: 0;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 15px;
            font-size: 9px;
        }
        .clear {
            clear: both;
        }
        .info-box {
            background: #e0f2fe;
            border: 1px solid #0284c7;
            padding: 8px;
            margin: 8px 0;
            font-size: 8px;
        }
        .success-box {
            background: #d1fae5;
            border: 1px solid #059669;
            padding: 8px;
            text-align: center;
            color: #059669;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN HARIAN BENGKEL</h1>
        <p>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</p>
        <p>Digenerate: {{ $generated_at }} oleh {{ $generated_by }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-section">
        <div class="section-title">RINGKASAN HARI INI</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-value">{{ $daily['summary']['total_transaksi'] ?? 0 }}</div>
                    <div class="summary-label">Total Transaksi</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-value">{{ $daily['summary']['transaksi_selesai'] ?? 0 }}</div>
                    <div class="summary-label">Transaksi Selesai</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-value">Rp {{ number_format($daily['summary']['total_omzet'] ?? 0, 0, ',', '.') }}</div>
                    <div class="summary-label">Total Omzet</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-value">Rp {{ number_format($daily['summary']['rata_rata_transaksi'] ?? 0, 0, ',', '.') }}</div>
                    <div class="summary-label">Rata-rata Transaksi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="two-column">
        <!-- Left Column: Transactions -->
        <div class="column">
            <div class="section-title">TRANSAKSI HARI INI</div>
            @if(isset($daily['transactions']) && count($daily['transactions']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($daily['transactions'], 0, 8) as $trx)
                        <tr>
                            <td>{{ $trx['nomor_transaksi'] ?? '-' }}</td>
                            <td>{{ $trx['nama_customer'] ?? '-' }}</td>
                            <td class="{{ ($trx['status_transaksi'] ?? '') === 'Selesai' ? 'status-selesai' : 'status-progress' }}">
                                {{ $trx['status_transaksi'] ?? '-' }}
                            </td>
                            <td>Rp {{ number_format($trx['total_harga'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(count($daily['transactions']) > 8)
                    <p style="font-size: 7px; color: #666; font-style: italic;">
                        * Menampilkan 8 transaksi teratas dari {{ count($daily['transactions']) }} total transaksi
                    </p>
                @endif
            @else
                <div class="no-data">Tidak ada transaksi hari ini</div>
            @endif

            <!-- Inventory Summary -->
            <div style="margin-top: 15px;">
                <div class="section-title">RINGKASAN INVENTORY</div>
                <div class="info-box">
                    <table style="border: none; font-size: 8px;">
                        <tr style="border: none;">
                            <td style="border: none; font-weight: bold;">Total Items:</td>
                            <td style="border: none; text-align: right;">{{ $stock['summary']['total_items'] ?? 0 }}</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; font-weight: bold;">Stok Aman:</td>
                            <td style="border: none; text-align: right; color: #059669;">{{ $stock['summary']['stok_aman'] ?? 0 }}</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; font-weight: bold;">Perlu Restock:</td>
                            <td style="border: none; text-align: right; color: #d97706;">{{ $stock['summary']['stok_perlu_restock'] ?? 0 }}</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; font-weight: bold;">Stok Habis:</td>
                            <td style="border: none; text-align: right; color: #dc2626;">{{ $stock['summary']['stok_habis'] ?? 0 }}</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; font-weight: bold;">Total Nilai:</td>
                            <td style="border: none; text-align: right; font-weight: bold;">Rp {{ number_format($stock['summary']['total_value'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Stock Alerts -->
        <div class="column">
            <div class="section-title">STOK PERLU PERHATIAN</div>
            @if(isset($daily['stok_perlu_perhatian']) && count($daily['stok_perlu_perhatian']) > 0)
                <div class="alert-box">
                    @foreach(array_slice($daily['stok_perlu_perhatian'], 0, 10) as $item)
                    <div class="alert-item">
                        <strong>{{ $item['nama_barang'] ?? 'Unknown' }}</strong><br>
                        Stok: {{ $item['stok']['jumlah_stok'] ?? 0 }} | ROP: {{ $item['reorder_point'] ?? 0 }}
                        @if(($item['stok']['jumlah_stok'] ?? 0) <= 0)
                            <span style="color: red; font-weight: bold;"> [HABIS!]</span>
                        @endif
                    </div>
                    @endforeach
                    @if(count($daily['stok_perlu_perhatian']) > 10)
                        <div class="alert-item" style="color: #666; font-style: italic;">
                            ... dan {{ count($daily['stok_perlu_perhatian']) - 10 }} item lainnya
                        </div>
                    @endif
                </div>
            @else
                <div class="success-box">
                    Semua stok dalam kondisi aman!
                </div>
            @endif

            <!-- Action Items -->
            <div style="margin-top: 15px;">
                <div class="section-title">ACTION ITEMS</div>
                <div style="background: #f3f4f6; padding: 8px; font-size: 8px;">
                    @if(isset($daily['stok_perlu_perhatian']) && count($daily['stok_perlu_perhatian']) > 0)
                        <div style="margin-bottom: 5px;">
                            ☐ Review {{ count($daily['stok_perlu_perhatian']) }} item yang perlu restock
                        </div>
                    @endif
                    
                    @if(($daily['summary']['total_transaksi'] ?? 0) > ($daily['summary']['transaksi_selesai'] ?? 0))
                        <div style="margin-bottom: 5px;">
                            ☐ Follow up {{ ($daily['summary']['total_transaksi'] ?? 0) - ($daily['summary']['transaksi_selesai'] ?? 0) }} transaksi yang masih progress
                        </div>
                    @endif
                    
                    <div style="margin-bottom: 5px;">
                        ☐ Update harga jual jika diperlukan
                    </div>
                    
                    <div>
                        ☐ Review slow moving items untuk promotional strategy
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clear"></div>

    <!-- Footer -->
    <div class="footer">
        <p>Report ini digenerate secara otomatis dari sistem inventory bengkel</p>
        <p>Untuk informasi lebih detail, silakan cek dashboard online</p>
    </div>
</body>
</html>