<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan - {{ $month }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #6f42c1;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            margin: 0;
            color: #6f42c1;
            font-size: 20px;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 11px;
            margin-top: 8px;
            line-height: 1.3;
        }
        
        /* PDF-FRIENDLY Executive Summary - No Gradients */
        .executive-summary {
            background-color: #f8f9fa;
            border: 2px solid #6f42c1;
            color: #333;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .executive-summary h2 {
            margin: 0 0 15px 0;
            font-size: 16px;
            text-align: center;
            color: #6f42c1;
            font-weight: bold;
        }
        
        /* Fixed Layout untuk PDF */
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-grid td {
            width: 33.33%;
            text-align: center;
            padding: 15px 10px;
            vertical-align: top;
            border-right: 1px solid #dee2e6;
        }
        
        .summary-grid td:last-child {
            border-right: none;
        }
        
        .summary-item h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            color: #666;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .summary-item small {
            font-size: 9px;
            color: #888;
        }
        
        .profit-positive { color: #28a745 !important; }
        .profit-negative { color: #dc3545 !important; }
        
        /* Growth Section - Simplified */
        .growth-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .growth-section h3 {
            margin: 0 0 15px 0;
            color: #6f42c1;
            font-size: 14px;
            font-weight: bold;
        }
        
        .growth-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .growth-grid td {
            width: 50%;
            padding: 10px 15px;
            vertical-align: top;
            border-right: 1px solid #dee2e6;
        }
        
        .growth-grid td:last-child {
            border-right: none;
        }
        
        .growth-positive { color: #28a745; font-weight: bold; }
        .growth-negative { color: #dc3545; font-weight: bold; }
        .growth-neutral { color: #6c757d; font-weight: bold; }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section h3 {
            background-color: #6f42c1;
            color: white;
            padding: 10px 15px;
            margin: 0 0 15px 0;
            font-size: 14px;
            font-weight: bold;
            border-radius: 3px;
        }
        
        /* Table Styling - PDF Optimized */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            vertical-align: top;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tfoot tr {
            background-color: #e9ecef !important;
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* KPI Box - Simplified for PDF */
        .kpi-box {
            border: 2px solid #6f42c1;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            background-color: #f8f9fa;
            page-break-inside: avoid;
        }
        
        .kpi-box h4 {
            margin: 0 0 15px 0;
            color: #6f42c1;
            font-size: 13px;
            font-weight: bold;
        }
        
        .kpi-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .kpi-grid td {
            width: 25%;
            text-align: center;
            padding: 10px 5px;
            border-right: 1px solid #dee2e6;
        }
        
        .kpi-grid td:last-child {
            border-right: none;
        }
        
        .kpi-item .label {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .kpi-item .value {
            font-size: 13px;
            font-weight: bold;
            color: #333;
        }

        /* Top Customers - PDF Friendly */
        .top-customers {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            page-break-inside: avoid;
        }
        
        .top-customers h4 {
            margin: 0 0 15px 0;
            color: #856404;
            font-size: 12px;
            font-weight: bold;
        }
        
        .customer-item {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            background-color: white;
            border-radius: 3px;
            border: 1px solid #ffeaa7;
            border-collapse: collapse;
        }
        
        .customer-item td {
            border: none;
            padding: 5px;
            vertical-align: middle;
        }
        
        .customer-item .rank {
            width: 40px;
            text-align: center;
            font-weight: bold;
            color: #856404;
            font-size: 14px;
        }
        
        .customer-item .info {
            padding-left: 10px;
        }
        
        .customer-item .value {
            text-align: right;
            font-weight: bold;
            color: #856404;
            font-size: 12px;
        }
        
        /* Insights Box */
        .insights-box {
            background-color: #e3f2fd;
            border: 2px solid #1976d2;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            page-break-inside: avoid;
        }
        
        .insights-box h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
            font-size: 13px;
            font-weight: bold;
        }
        
        .insights-box ul {
            margin: 5px 0;
            padding-left: 20px;
            font-size: 11px;
        }
        
        .insights-box li {
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        /* Financial Summary Table */
        .financial-table {
            font-size: 11px;
            margin-top: 10px;
        }
        
        .financial-table td {
            padding: 10px;
        }
        
        .financial-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 60%;
        }
        
        .financial-table .value {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }
        
        .financial-table .total-row {
            border-top: 2px solid #333;
            font-size: 12px;
        }
        
        /* Warning/Note Box */
        .note-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 12px;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        .note-box small {
            font-size: 9px;
            color: #856404;
            line-height: 1.4;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            page-break-inside: avoid;
        }
        
        /* Page Break Controls */
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        /* Print Optimizations */
        @media print {
            body {
                margin: 15px;
                font-size: 11px;
            }
            
            .header h1 {
                font-size: 18px;
            }
            
            .executive-summary {
                padding: 15px;
            }
            
            .section h3 {
                font-size: 13px;
                padding: 8px 12px;
            }
        }
        
        /* Status Colors */
        .status-success { color: #28a745; }
        .status-warning { color: #ffc107; }
        .status-danger { color: #dc3545; }
        .status-info { color: #17a2b8; }
        
        /* Highlight important numbers */
        .highlight-number {
            background-color: #fff3cd;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN BULANAN BENGKEL</h1>
        <div class="subtitle">
            Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}<br>
            Digenerate: {{ $generated_at }} oleh {{ $generated_by }}
        </div>
    </div>

    <!-- Executive Summary - PDF FRIENDLY -->
    <div class="executive-summary no-break">
        <h2>Executive Summary</h2>
        <table class="summary-grid">
            <tr>
                <td class="summary-item">
                    <h4>Total Omzet</h4>
                    <div class="value">Rp {{ number_format($monthly['summary']['omzet'], 0, ',', '.') }}</div>
                    <small>{{ $monthly['summary']['total_transaksi'] }} transaksi</small>
                </td>
                <td class="summary-item">
                    <h4>Pengeluaran</h4>
                    <div class="value">Rp {{ number_format($monthly['summary']['pengeluaran_beli_barang'], 0, ',', '.') }}</div>
                    <small>Pembelian barang</small>
                </td>
                <td class="summary-item">
                    <h4>Profit Est.</h4>
                    <div class="value {{ $monthly['summary']['profit_estimation'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        Rp {{ number_format($monthly['summary']['profit_estimation'], 0, ',', '.') }}
                    </div>
                    <small>{{ $monthly['summary']['profit_estimation'] >= 0 ? 'Profitable' : 'Loss' }}</small>
                </td>
            </tr>
        </table>
    </div>

    <!-- Growth Comparison - SIMPLIFIED -->
    <div class="growth-section no-break">
        <h3>Perbandingan dengan Bulan Lalu</h3>
        <table class="growth-grid">
            <tr>
                <td>
                    <strong>Omzet Bulan Lalu:</strong><br>
                    <span style="font-size: 14px; font-weight: bold;">Rp {{ number_format($monthly['growth']['last_month_omzet'], 0, ',', '.') }}</span>
                </td>
                <td>
                    <strong>Growth Rate:</strong><br>
                    <span class="{{ $monthly['growth']['omzet_growth_percent'] >= 0 ? 'growth-positive' : 'growth-negative' }}" style="font-size: 14px;">
                        {{ $monthly['growth']['omzet_growth_percent'] >= 0 ? '+' : '' }}{{ $monthly['growth']['omzet_growth_percent'] }}%
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- KPI Dashboard - PDF OPTIMIZED -->
    <div class="kpi-box no-break">
        <h4>Key Performance Indicators (KPI)</h4>
        <table class="kpi-grid">
            <tr>
                <td class="kpi-item">
                    <div class="label">Avg Transaksi/Hari</div>
                    <div class="value">{{ round($monthly['summary']['total_transaksi'] / date('t', strtotime($month . '-01')), 1) }}</div>
                </td>
                <td class="kpi-item">
                    <div class="label">Avg Revenue/Hari</div>
                    <div class="value">{{ number_format($monthly['summary']['omzet'] / date('t', strtotime($month . '-01')), 0, ',', '.') }}</div>
                </td>
                <td class="kpi-item">
                    <div class="label">Avg Ticket Size</div>
                    <div class="value">{{ number_format($monthly['summary']['rata_rata_transaksi'], 0, ',', '.') }}</div>
                </td>
                <td class="kpi-item">
                    <div class="label">Profit Margin</div>
                    <div class="value {{ $monthly['summary']['profit_estimation'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ $monthly['summary']['omzet'] > 0 ? round(($monthly['summary']['profit_estimation'] / $monthly['summary']['omzet']) * 100, 1) : 0 }}%
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Daily Breakdown -->
    <div class="section">
        <h3>Breakdown Harian</h3>
        <table>
            <thead>
                <tr>
                    <th width="15%">Tanggal</th>
                    <th width="20%">Hari</th>
                    <th width="15%">Transaksi</th>
                    <th width="25%">Revenue (Rp)</th>
                    <th width="25%">Avg per Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly['daily_breakdown'] as $day)
                    @php
                        $dayOfWeek = \Carbon\Carbon::createFromFormat('Y-m', $month)->day($day['day'])->format('l');
                        $avgPerTransaction = $day['daily_transactions'] > 0 ? $day['daily_revenue'] / $day['daily_transactions'] : 0;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $day['day'] }}</td>
                        <td>{{ $dayOfWeek }}</td>
                        <td class="text-center">{{ $day['daily_transactions'] }}</td>
                        <td class="text-right font-bold">{{ number_format($day['daily_revenue'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($avgPerTransaction, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right font-bold">TOTAL:</td>
                    <td class="text-center font-bold">{{ $monthly['summary']['total_transaksi'] }}</td>
                    <td class="text-right font-bold">{{ number_format($monthly['summary']['omzet'], 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($monthly['summary']['rata_rata_transaksi'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if(count($monthly['top_customers']) > 0)
    <!-- Top Customers - PDF FRIENDLY -->
    <div class="section page-break">
        <h3>Top Customers Bulan Ini</h3>
        <div class="top-customers">
            <h4>Customer dengan spending tertinggi:</h4>
            @foreach($monthly['top_customers'] as $index => $customer)
            <table class="customer-item">
                <tr>
                    <td class="rank">{{ $index + 1 }}</td>
                    <td class="info">
                        <strong>{{ $customer['nama_customer'] }}</strong><br>
                        <small>{{ $customer['total_transaksi'] }} transaksi</small>
                    </td>
                    <td class="value">
                        Rp {{ number_format($customer['total_spending'], 0, ',', '.') }}
                    </td>
                </tr>
            </table>
            @endforeach
        </div>
    </div>
    @else
    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 15px 0;" class="no-break">
        <strong>Note:</strong> Tidak ada data customer dengan nama tercatat untuk bulan ini. 
        Pertimbangkan untuk meningkatkan pencatatan data customer untuk analisis yang lebih baik.
    </div>
    @endif

    <!-- Business Insights - IMPROVED LAYOUT -->
    <div class="section">
        <h3>Business Insights & Recommendations</h3>
        <div class="insights-box">
            <h4>Performance Analysis:</h4>
            <ul>
                @if($monthly['summary']['profit_estimation'] >= 0)
                    <li class="status-success"><strong>Positive Profit:</strong> Bulan ini profitable dengan margin <span class="highlight-number">{{ $monthly['summary']['omzet'] > 0 ? round(($monthly['summary']['profit_estimation'] / $monthly['summary']['omzet']) * 100, 1) : 0 }}%</span></li>
                @else
                    <li class="status-danger"><strong>Negative Profit:</strong> Rugi <span class="highlight-number">Rp {{ number_format(abs($monthly['summary']['profit_estimation']), 0, ',', '.') }}</span> - perlu review strategi pricing dan operational cost</li>
                @endif
                
                @if($monthly['growth']['omzet_growth_percent'] >= 0)
                    <li class="status-success"><strong>Growth Trend:</strong> Omzet naik <span class="highlight-number">{{ $monthly['growth']['omzet_growth_percent'] }}%</span> dari bulan lalu</li>
                @else
                    <li class="status-warning"><strong>Decline Trend:</strong> Omzet turun <span class="highlight-number">{{ abs($monthly['growth']['omzet_growth_percent']) }}%</span> - perlu strategi recovery</li>
                @endif
                
                <li><strong>Average Ticket:</strong> <span class="highlight-number">Rp {{ number_format($monthly['summary']['rata_rata_transaksi'], 0, ',', '.') }}</span> per transaksi</li>
                <li><strong>Daily Average:</strong> <span class="highlight-number">{{ round($monthly['summary']['total_transaksi'] / date('t', strtotime($month . '-01')), 1) }}</span> transaksi per hari</li>
            </ul>
            
            <h4>Action Items untuk Bulan Depan:</h4>
            <ul>
                @if($monthly['summary']['profit_estimation'] < 0)
                    <li>Review dan optimasi operational cost untuk meningkatkan profit margin</li>
                    <li>Evaluasi pricing strategy - pertimbangkan penyesuaian harga jual</li>
                @endif
                
                @if($monthly['growth']['omzet_growth_percent'] < 0)
                    <li>Intensifkan marketing campaign untuk boost sales</li>
                    <li>Focus pada customer retention dan upselling</li>
                @endif
                
                <li>Track KPI harian untuk monitoring performa real-time</li>
                <li>Improve data collection untuk customer analytics yang lebih baik</li>
                <li>Review inventory turnover dan optimize stock levels</li>
                
                @if(count($monthly['top_customers']) > 0)
                    <li>Develop loyalty program untuk top customers</li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Financial Summary - IMPROVED TABLE -->
    <div class="section">
        <h3>Financial Summary</h3>
        <table class="financial-table">
            <tbody>
                <tr>
                    <td class="label">Total Revenue (Omzet)</td>
                    <td class="value">Rp {{ number_format($monthly['summary']['omzet'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Total Cost of Goods Sold (COGS)</td>
                    <td class="value">Rp {{ number_format($monthly['summary']['pengeluaran_beli_barang'], 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Gross Profit (Estimation)</td>
                    <td class="value {{ $monthly['summary']['profit_estimation'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        Rp {{ number_format($monthly['summary']['profit_estimation'], 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Gross Profit Margin</td>
                    <td class="value {{ $monthly['summary']['profit_estimation'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ $monthly['summary']['omzet'] > 0 ? round(($monthly['summary']['profit_estimation'] / $monthly['summary']['omzet']) * 100, 2) : 0 }}%
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div class="note-box">
            <small>
                <strong>Note:</strong> Gross Profit calculation hanya berdasarkan revenue vs COGS. 
                Operational expenses (gaji, listrik, sewa, dll) belum diperhitungkan dalam laporan ini.
            </small>
        </div>
    </div>

    <div class="footer no-break">
        <strong>Bengkel Inventory System</strong> | {{ $generated_at }} | 
        Period: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }} | 
        Profit: <span class="{{ $monthly['summary']['profit_estimation'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
            {{ $monthly['summary']['profit_estimation'] >= 0 ? '+' : '' }}{{ number_format($monthly['summary']['profit_estimation'], 0, ',', '.') }}
        </span>
    </div>
</body>
</html>