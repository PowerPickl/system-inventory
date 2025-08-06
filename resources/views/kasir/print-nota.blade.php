<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Transaksi - {{ $transaksiData['nomor_transaksi'] }}</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 20px; 
            line-height: 1.5;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 28px; 
            color: #007bff;
            font-weight: bold;
        }
        .header p { 
            margin: 5px 0; 
            color: #666; 
            font-size: 14px;
        }
        .nota-number {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            display: inline-block;
        }
        .info-section { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 30px; 
            margin: 30px 0;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .info-item { 
            margin-bottom: 8px; 
            display: flex;
        }
        .info-label { 
            font-weight: bold; 
            color: #495057;
            min-width: 120px;
        }
        .info-value {
            color: #333;
        }
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 30px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .items-table th, .items-table td { 
            border: 1px solid #dee2e6; 
            padding: 12px 8px; 
            text-align: left; 
        }
        .items-table th { 
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .items-table tr:hover {
            background-color: #e3f2fd;
        }
        .total-row { 
            font-weight: bold; 
            background: linear-gradient(135deg, #28a745, #1e7e34) !important;
            color: white;
            font-size: 16px;
        }
        .total-row td {
            border-color: #28a745;
        }
        .footer { 
            text-align: center; 
            margin-top: 40px; 
            color: #666; 
            font-size: 12px;
            border-top: 2px solid #dee2e6;
            padding-top: 20px;
        }
        .print-controls {
            margin-bottom: 30px;
            text-align: center;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,123,255,0.3);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        .btn-secondary:hover {
            background: linear-gradient(135deg, #495057, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108,117,125,0.3);
        }
        .currency {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        .status-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        @media print {
            body { margin: 0; }
            .print-controls { display: none; }
            .items-table { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Print Nota
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            ✖️ Tutup
        </button>
    </div>

    <div class="header">
        <h1>BENGKEL INVENTORY</h1>
        <p>Service Center & Spare Parts</p>
        <p>Nota Service Kendaraan</p>
        <div class="nota-number">{{ $transaksiData['nomor_transaksi'] }}</div>
    </div>

    <div class="info-section">
        <div>
            <div class="info-item">
                <span class="info-label">Customer:</span>
                <span class="info-value">{{ $transaksiData['nama_customer'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Kendaraan:</span>
                <span class="info-value">{{ $transaksiData['kendaraan'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jenis Service:</span>
                <span class="info-value">{{ $transaksiData['jenis_transaksi'] }}</span>
            </div>
        </div>
        <div>
            <div class="info-item">
                <span class="info-label">Tanggal:</span>
                <span class="info-value">{{ $transaksiData['tanggal_transaksi'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">User:</span>
                <span class="info-value">{{ $transaksiData['kasir_name'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="status-badge">SELESAI</span>
            </div>
        </div>
    </div>

    @if(count($items) > 0)
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode</th>
                <th style="width: 40%;">Nama Barang</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Harga</th>
                <th style="width: 15%; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td><strong>{{ $item['kode_barang'] }}</strong></td>
                <td>{{ $item['nama_barang'] }}</td>
                <td style="text-align: center;"><strong>{{ $item['qty'] }}</strong></td>
                <td style="text-align: right;" class="currency">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                <td style="text-align: right;" class="currency">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" style="text-align: right; font-size: 16px;">
                    <strong>TOTAL PEMBAYARAN:</strong>
                </td>
                <td style="text-align: right; font-size: 18px;" class="currency">
                    <strong>Rp {{ number_format($transaksiData['total_harga'], 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px; margin: 30px 0;">
        <p style="font-size: 16px; color: #666;">Tidak ada item yang approved untuk ditampilkan.</p>
    </div>
    @endif

    <div class="footer">
        <p><strong>Terima kasih atas kepercayaan Anda!</strong></p>
        <p>Simpan nota ini sebagai bukti service yang telah dilakukan</p>
        <p style="margin-top: 15px; font-size: 11px;">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} | 
            Sistem Bengkel Inventory v1.0
        </p>
    </div>

    <script>
        // Optional: Auto print when page loads (uncomment if needed)
        // window.addEventListener('load', function() {
        //     setTimeout(() => window.print(), 500);
        // });

        // Print function
        function printNota() {
            window.print();
        }

        // Keyboard shortcut for print
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                printNota();
            }
        });
    </script>
</body>
</html>