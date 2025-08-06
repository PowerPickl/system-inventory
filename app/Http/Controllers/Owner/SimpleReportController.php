<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use App\Models\RestockRequest;
use App\Models\LogStok;
use App\Models\BarangMasuk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DailyReportExport;
use App\Exports\StockReportExport;
use App\Exports\RestockReportExport;
use App\Exports\MonthlyReportExport;

class SimpleReportController extends Controller
{
    /**
     * Dashboard laporan simple
     */
    public function index()
    {
        return view('owner.simple-reports.index');
    }

    // ========================================
    // 📅 LAPORAN HARIAN/MINGGUAN
    // ========================================

    /**
     * Laporan Harian - Data penting hari ini
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $targetDate = Carbon::parse($date);

        // 1. Transaksi hari ini
        $todayTransactions = Transaksi::whereDate('tanggal_transaksi', $targetDate)
            ->with(['kasir', 'detailTransaksi.barang'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Summary hari ini
        $todaySummary = [
            'total_transaksi' => $todayTransactions->count(),
            'transaksi_selesai' => $todayTransactions->where('status_transaksi', 'Selesai')->count(),
            'total_omzet' => $todayTransactions->where('status_transaksi', 'Selesai')->sum('total_harga'),
            'rata_rata_transaksi' => $todayTransactions->where('status_transaksi', 'Selesai')->avg('total_harga') ?? 0
        ];

        // 3. Barang yang keluar hari ini
        $itemsKeluarHariIni = LogStok::where('jenis_perubahan', 'Keluar')
            ->whereDate('tanggal_log', $targetDate)
            ->with('barang.kategori')
            ->select([
                'id_barang',
                DB::raw('SUM(ABS(qty_perubahan)) as total_keluar')
            ])
            ->groupBy('id_barang')
            ->orderBy('total_keluar', 'desc')
            ->limit(10)
            ->get();

        // 4. Stok yang perlu diperhatikan (hampir habis)
        $stokPerluPerhatian = Barang::whereHas('stok', function($q) {
                $q->whereRaw('jumlah_stok <= barang.reorder_point');
            })
            ->with(['stok', 'kategori'])
            ->orderBy('id_barang')
            ->get();

        return response()->json([
            'date' => $targetDate->format('Y-m-d'),
            'summary' => $todaySummary,
            'transactions' => $todayTransactions,
            'items_keluar' => $itemsKeluarHariIni,
            'stok_perlu_perhatian' => $stokPerluPerhatian
        ]);
    }

    /**
     * Laporan Mingguan
     */
    public function weeklyReport(Request $request)
    {
        $weekStart = $request->get('week_start', now()->startOfWeek()->format('Y-m-d'));
        $startDate = Carbon::parse($weekStart);
        $endDate = $startDate->copy()->endOfWeek();

        // Summary seminggu
        $weeklySummary = [
            'total_transaksi' => Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])->count(),
            'total_omzet' => Transaksi::where('status_transaksi', 'Selesai')
                                    ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                    ->sum('total_harga'),
            'transaksi_per_hari' => Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                            ->select([
                                                DB::raw('DATE(tanggal_transaksi) as date'),
                                                DB::raw('COUNT(*) as count'),
                                                DB::raw('SUM(CASE WHEN status_transaksi = "Selesai" THEN total_harga ELSE 0 END) as revenue')
                                            ])
                                            ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
                                            ->orderBy('date')
                                            ->get()
        ];

        // Top selling items minggu ini
        $topSellingWeek = DetailTransaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
            ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
            ->where('transaksi.status_transaksi', 'Selesai')
            ->where('detail_transaksi.status_permintaan', 'Approved')
            ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
            ->select([
                'barang.nama_barang',
                'barang.kode_barang',
                DB::raw('SUM(detail_transaksi.qty) as total_terjual'),
                DB::raw('SUM(detail_transaksi.subtotal) as total_revenue')
            ])
            ->groupBy('barang.id_barang', 'barang.nama_barang', 'barang.kode_barang')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'week_period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'summary' => $weeklySummary,
            'top_selling' => $topSellingWeek
        ]);
    }

    // ========================================
    // 🛒 LAPORAN BELANJA/RESTOCK
    // ========================================

    /**
     * Laporan Belanja - Daftar barang yang harus dibeli
     */
    public function restockReport(Request $request)
    {
        // 1. Barang yang harus dibeli (stok <= reorder point)
        $barangHarusBeli = Barang::whereHas('stok', function($q) {
                $q->whereRaw('jumlah_stok <= barang.reorder_point');
            })
            ->with(['stok', 'kategori'])
            ->select([
                'id_barang',
                'kode_barang', 
                'nama_barang',
                'satuan',
                'harga_beli',
                'reorder_point',
                'eoq_qty',
                'id_kategori'
            ])
            ->orderBy('id_kategori')
            ->get();

        // 2. Estimasi budget yang dibutuhkan
        $estimasiBudget = $barangHarusBeli->sum(function($item) {
            $qtyBeli = $item->eoq_qty ?? ($item->reorder_point * 2); // Default 2x reorder point
            return $qtyBeli * $item->harga_beli;
        });

        // 3. History pembelian bulan ini
        $bulanIni = now()->format('Y-m');
        $historyPembelianBulanIni = BarangMasuk::whereRaw('DATE_FORMAT(tanggal_masuk, "%Y-%m") = ?', [$bulanIni])
            ->with(['details.barang'])
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        $totalPembelianBulanIni = $historyPembelianBulanIni->sum('total_nilai');

        // 4. Request yang pending approval
        $pendingRequests = RestockRequest::where('status_request', 'Pending')
            ->with(['details.barang', 'userGudang'])
            ->orderBy('tanggal_request', 'desc')
            ->get();

        return response()->json([
            'barang_harus_beli' => $barangHarusBeli,
            'estimasi_budget' => $estimasiBudget,
            'history_pembelian_bulan_ini' => $historyPembelianBulanIni,
            'total_pembelian_bulan_ini' => $totalPembelianBulanIni,
            'pending_requests' => $pendingRequests
        ]);
    }

    // ========================================
    // 📦 LAPORAN STOK
    // ========================================

    /**
     * Laporan Stok - Inventory overview
     */
    public function stockReport(Request $request)
    {
        // 1. Summary inventory
        $inventorySummary = [
            'total_items' => Barang::count(),
            'total_categories' => Barang::distinct('id_kategori')->count(),
            'total_value' => Barang::join('stok', 'barang.id_barang', '=', 'stok.id_barang')
                                   ->select(DB::raw('SUM(stok.jumlah_stok * barang.harga_beli) as total'))
                                   ->value('total') ?? 0,
            'stok_aman' => Barang::whereHas('stok', function($q) {
                $q->where('status_stok', 'Aman');
            })->count(),
            'stok_perlu_restock' => Barang::whereHas('stok', function($q) {
                $q->where('status_stok', 'Perlu Restock');
            })->count(),
            'stok_habis' => Barang::whereHas('stok', function($q) {
                $q->where('status_stok', 'Habis');
            })->count()
        ];

        // 2. Breakdown per kategori
        $stokPerKategori = Barang::join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
            ->join('stok', 'barang.id_barang', '=', 'stok.id_barang')
            ->select([
                'kategori_barang.nama_kategori',
                'kategori_barang.kode_kategori',
                DB::raw('COUNT(barang.id_barang) as total_items'),
                DB::raw('SUM(stok.jumlah_stok) as total_qty'),
                DB::raw('SUM(stok.jumlah_stok * barang.harga_beli) as total_value'),
                DB::raw('SUM(CASE WHEN stok.status_stok = "Aman" THEN 1 ELSE 0 END) as stok_aman'),
                DB::raw('SUM(CASE WHEN stok.status_stok = "Perlu Restock" THEN 1 ELSE 0 END) as stok_restock'),
                DB::raw('SUM(CASE WHEN stok.status_stok = "Habis" THEN 1 ELSE 0 END) as stok_habis')
            ])
            ->groupBy('kategori_barang.id_kategori', 'kategori_barang.nama_kategori', 'kategori_barang.kode_kategori')
            ->orderBy('total_value', 'desc')
            ->get();

        // 3. Barang yang jarang keluar (slow moving) - 30 hari terakhir
        $slowMovingItems = Barang::leftJoin('log_stok', function($join) {
                $join->on('barang.id_barang', '=', 'log_stok.id_barang')
                     ->where('log_stok.jenis_perubahan', 'Keluar')
                     ->where('log_stok.tanggal_log', '>=', now()->subDays(30));
            })
            ->join('stok', 'barang.id_barang', '=', 'stok.id_barang')
            ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
            ->select([
                'barang.id_barang',
                'barang.kode_barang',
                'barang.nama_barang',
                'kategori_barang.nama_kategori',
                'stok.jumlah_stok',
                'barang.harga_beli',
                DB::raw('COALESCE(SUM(ABS(log_stok.qty_perubahan)), 0) as qty_keluar_30_hari'),
                DB::raw('(stok.jumlah_stok * barang.harga_beli) as nilai_stok')
            ])
            ->groupBy('barang.id_barang', 'barang.kode_barang', 'barang.nama_barang', 'kategori_barang.nama_kategori', 'stok.jumlah_stok', 'barang.harga_beli')
            ->having('qty_keluar_30_hari', '=', 0)
            ->where('stok.jumlah_stok', '>', 0)
            ->orderBy('nilai_stok', 'desc')
            ->limit(10)
            ->get();

        // 4. Top 10 barang berdasarkan value
        $topValueItems = Barang::join('stok', 'barang.id_barang', '=', 'stok.id_barang')
            ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
            ->select([
                'barang.kode_barang',
                'barang.nama_barang',
                'kategori_barang.nama_kategori',
                'stok.jumlah_stok',
                'barang.harga_beli',
                DB::raw('(stok.jumlah_stok * barang.harga_beli) as total_value')
            ])
            ->orderBy('total_value', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'summary' => $inventorySummary,
            'per_kategori' => $stokPerKategori,
            'slow_moving' => $slowMovingItems,
            'top_value_items' => $topValueItems
        ]);
    }

    // ========================================
    // 📊 LAPORAN BULANAN SIMPLE
    // ========================================

    /**
     * Laporan Bulanan - Omzet vs Pengeluaran
     */
    public function monthlyReport(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $targetMonth = Carbon::createFromFormat('Y-m', $month);
        $startDate = $targetMonth->copy()->startOfMonth();
        $endDate = $targetMonth->copy()->endOfMonth();

        // 1. Summary bulan ini
        $monthlySummary = [
            'omzet' => Transaksi::where('status_transaksi', 'Selesai')
                                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                ->sum('total_harga'),
            'total_transaksi' => Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])->count(),
            'pengeluaran_beli_barang' => BarangMasuk::whereBetween('tanggal_masuk', [$startDate, $endDate])
                                                   ->sum('total_nilai'),
            'rata_rata_transaksi' => Transaksi::where('status_transaksi', 'Selesai')
                                             ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                             ->avg('total_harga') ?? 0
        ];

        // Hitung profit estimation
        $monthlySummary['profit_estimation'] = $monthlySummary['omzet'] - $monthlySummary['pengeluaran_beli_barang'];

        // 2. Comparison dengan bulan lalu
        $lastMonth = $targetMonth->copy()->subMonth();
        $lastMonthStart = $lastMonth->copy()->startOfMonth();
        $lastMonthEnd = $lastMonth->copy()->endOfMonth();

        $lastMonthData = [
            'omzet' => Transaksi::where('status_transaksi', 'Selesai')
                                ->whereBetween('tanggal_transaksi', [$lastMonthStart, $lastMonthEnd])
                                ->sum('total_harga'),
            'pengeluaran' => BarangMasuk::whereBetween('tanggal_masuk', [$lastMonthStart, $lastMonthEnd])
                                      ->sum('total_nilai')
        ];

        // Growth calculation
        $omzetGrowth = $lastMonthData['omzet'] > 0 
            ? (($monthlySummary['omzet'] - $lastMonthData['omzet']) / $lastMonthData['omzet']) * 100 
            : 0;

        // 3. Daily breakdown untuk chart
        $dailyBreakdown = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->select([
                DB::raw('DAY(tanggal_transaksi) as day'),
                DB::raw('SUM(CASE WHEN status_transaksi = "Selesai" THEN total_harga ELSE 0 END) as daily_revenue'),
                DB::raw('COUNT(*) as daily_transactions')
            ])
            ->groupBy(DB::raw('DAY(tanggal_transaksi)'))
            ->orderBy('day')
            ->get();

        // 4. Top customers bulan ini
        $topCustomers = Transaksi::where('status_transaksi', 'Selesai')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNotNull('nama_customer')
            ->where('nama_customer', '!=', '')
            ->select([
                'nama_customer',
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(total_harga) as total_spending')
            ])
            ->groupBy('nama_customer')
            ->orderBy('total_spending', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'month' => $month,
            'summary' => $monthlySummary,
            'growth' => [
                'omzet_growth_percent' => round($omzetGrowth, 2),
                'last_month_omzet' => $lastMonthData['omzet']
            ],
            'daily_breakdown' => $dailyBreakdown,
            'top_customers' => $topCustomers
        ]);
    }

    // ========================================
    // 📄 EXPORT FUNCTIONS
    // ========================================

    /**
     * Export PDF Summary - 1 halaman ringkasan
     */
    public function exportPdfSummary(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        try {
            \Log::info('PDF Export Request:', ['type' => $type, 'date' => $date]);
            
            $data = [
                'date' => $date,
                'type' => $type,
                'generated_at' => now()->format('d/m/Y H:i'),
                'generated_by' => auth()->user()->name
            ];

            // 🔥 PENTING: Get data dan pilih template berdasarkan type
            switch($type) {
                case 'daily':
                    // Get daily data
                    $dailyRequest = new Request(['date' => $date]);
                    $dailyResponse = $this->dailyReport($dailyRequest);
                    $dailyData = json_decode($dailyResponse->getContent(), true);
                    
                    // Get stock data untuk summary
                    $stockRequest = new Request();
                    $stockResponse = $this->stockReport($stockRequest);
                    $stockData = json_decode($stockResponse->getContent(), true);
                    
                    $data['daily'] = $dailyData;
                    $data['stock'] = $stockData;
                    
                    // 🎯 GUNAKAN TEMPLATE YANG BENAR
                    $viewTemplate = 'owner.simple-reports.pdf-summary'; // atau pdf-daily jika ada
                    $filename = 'laporan-harian-' . $date . '.pdf';
                    break;
                    
                case 'restock':
                    // Get restock data
                    $restockRequest = new Request();
                    $restockResponse = $this->restockReport($restockRequest);
                    $restockData = json_decode($restockResponse->getContent(), true);
                    $data['restock'] = $restockData;
                    
                    // 🎯 GUNAKAN TEMPLATE RESTOCK
                    $viewTemplate = 'owner.simple-reports.pdf-restock'; // template khusus restock
                    $filename = 'laporan-belanja-' . $date . '.pdf';
                    break;
                    
                case 'stock':
                    // Get stock data
                    $stockRequest = new Request();
                    $stockResponse = $this->stockReport($stockRequest);
                    $stockData = json_decode($stockResponse->getContent(), true);
                    $data['stock'] = $stockData;
                    
                    // 🎯 GUNAKAN TEMPLATE STOCK
                    $viewTemplate = 'owner.simple-reports.pdf-stock'; // template khusus stock
                    $filename = 'laporan-stok-' . $date . '.pdf';
                    break;
                    
                case 'monthly':
                    $month = $request->get('month', now()->format('Y-m'));
                    $monthlyRequest = new Request(['month' => $month]);
                    $monthlyResponse = $this->monthlyReport($monthlyRequest);
                    $monthlyData = json_decode($monthlyResponse->getContent(), true);
                    $data['monthly'] = $monthlyData;
                    $data['month'] = $month;
                    
                    // 🎯 GUNAKAN TEMPLATE MONTHLY
                    $viewTemplate = 'owner.simple-reports.pdf-monthly'; // template khusus monthly
                    $filename = 'laporan-bulanan-' . $month . '.pdf';
                    break;
                    
                default:
                    // Fallback ke daily
                    $dailyRequest = new Request(['date' => $date]);
                    $dailyResponse = $this->dailyReport($dailyRequest);
                    $dailyData = json_decode($dailyResponse->getContent(), true);
                    
                    $stockRequest = new Request();
                    $stockResponse = $this->stockReport($stockRequest);
                    $stockData = json_decode($stockResponse->getContent(), true);
                    
                    $data['daily'] = $dailyData;
                    $data['stock'] = $stockData;
                    
                    $viewTemplate = 'owner.simple-reports.pdf-summary';
                    $filename = 'laporan-summary-' . $date . '.pdf';
            }

            \Log::info('PDF data prepared:', ['type' => $type, 'template' => $viewTemplate, 'filename' => $filename]);

            // Generate PDF dengan template yang benar
            $pdf = Pdf::loadView($viewTemplate, $data);
            $pdf->setPaper('A4', 'portrait');
            
            \Log::info('PDF generated successfully');
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage(), [
                'type' => $type,
                'date' => $date,
                'stack' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Excel Detail - Proper Excel format with multiple sheets
     */
    public function exportExcelDetail(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        $format = $request->get('format', 'xlsx'); // xlsx atau csv
        
        try {
            \Log::info('Excel Export Request:', ['type' => $type, 'date' => $date, 'format' => $format]);
            
            // Validate type
            if (!in_array($type, ['daily', 'stock', 'restock', 'monthly'])) {
                throw new \Exception('Invalid export type: ' . $type);
            }

            // Cek apakah Laravel Excel tersedia
            if (!class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                \Log::warning('Laravel Excel not available, using fallback');
                return $this->exportFallbackCsv($request);
            }

            switch($type) {
                case 'daily':
                    $filename = 'laporan-harian-' . $date . '.' . $format;
                    if (class_exists('App\Exports\DailyReportExport')) {
                        return Excel::download(new DailyReportExport($date), $filename);
                    } else {
                        return $this->generateNativeExcel($this->getDailyDataArray($date), $filename);
                    }
                    
                case 'stock':
                    $filename = 'laporan-stok-' . $date . '.' . $format;
                    if (class_exists('App\Exports\StockReportExport')) {
                        return Excel::download(new StockReportExport(), $filename);
                    } else {
                        return $this->generateNativeExcel($this->getStockDataArray(), $filename);
                    }
                    
                case 'restock':
                    $filename = 'laporan-belanja-' . $date . '.' . $format;
                    if (class_exists('App\Exports\RestockReportExport')) {
                        return Excel::download(new RestockReportExport(), $filename);
                    } else {
                        return $this->generateNativeExcel($this->getRestockDataArray(), $filename);
                    }
                    
                case 'monthly':
                    $month = $request->get('month', now()->format('Y-m'));
                    $filename = 'laporan-bulanan-' . $month . '.' . $format;
                    if (class_exists('App\Exports\MonthlyReportExport')) {
                        return Excel::download(new MonthlyReportExport($month), $filename);
                    } else {
                        return $this->generateNativeExcel($this->getMonthlyDataArray($month), $filename);
                    }
                    
                default:
                    throw new \Exception('Invalid export type: ' . $type);
            }
            
        } catch (\Exception $e) {
            \Log::error('Excel Export Error: ' . $e->getMessage(), [
                'type' => $type,
                'date' => $date,
                'stack' => $e->getTraceAsString()
            ]);
            
            // Return fallback response
            return $this->exportFallbackCsv($request);
        }
    }

    /**
     * Fallback CSV export if Excel package not available
     */
    private function exportExcelDetailFallback(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        // Generate CSV content (works without additional packages)
        $filename = '';
        $csvContent = '';
        
        switch($type) {
            case 'daily':
                $filename = 'laporan-harian-' . $date . '.csv';
                $csvContent = $this->generateDailyCsv($date);
                break;
                
            case 'stock':
                $filename = 'laporan-stok-' . $date . '.csv';
                $csvContent = $this->generateStockCsv();
                break;
                
            case 'restock':
                $filename = 'laporan-belanja-' . $date . '.csv';
                $csvContent = $this->generateRestockCsv();
                break;
                
            case 'monthly':
                $month = $request->get('month', now()->format('Y-m'));
                $filename = 'laporan-bulanan-' . $month . '.csv';
                $csvContent = $this->generateMonthlyCsv($month);
                break;
                
            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Generate Daily Report CSV
     */
    private function generateDailyCsv($date)
    {
        $targetDate = Carbon::parse($date);
        
        // Get transactions
        $transactions = Transaksi::whereDate('tanggal_transaksi', $targetDate)
            ->with(['kasir', 'detailTransaksi.barang.kategori'])
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = "LAPORAN HARIAN TRANSAKSI - " . $targetDate->format('d/m/Y') . "\n";
        $csv .= "Generated at: " . now()->format('d/m/Y H:i') . "\n\n";
        
        $csv .= "No Transaksi,Tanggal,Waktu,Customer,Kasir,Status,Total Harga,Kendaraan,Keterangan\n";
        
        foreach($transactions as $trx) {
            $csv .= '"' . $trx->nomor_transaksi . '",';
            $csv .= '"' . Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') . '",';
            $csv .= '"' . Carbon::parse($trx->tanggal_transaksi)->format('H:i') . '",';
            $csv .= '"' . ($trx->nama_customer ?? '-') . '",';
            $csv .= '"' . ($trx->kasir->name ?? '-') . '",';
            $csv .= '"' . $trx->status_transaksi . '",';
            $csv .= '"' . $trx->total_harga . '",';
            $csv .= '"' . ($trx->kendaraan ?? '-') . '",';
            $csv .= '"' . ($trx->keterangan ?? '-') . '"';
            $csv .= "\n";
        }
        
        // Add detail items
        $csv .= "\n\nDETAIL ITEMS TRANSAKSI\n";
        $csv .= "No Transaksi,Kode Barang,Nama Barang,Kategori,Qty,Harga Satuan,Subtotal,Status Permintaan\n";
        
        foreach($transactions as $trx) {
            foreach($trx->detailTransaksi as $detail) {
                $csv .= '"' . $trx->nomor_transaksi . '",';
                $csv .= '"' . ($detail->barang->kode_barang ?? '-') . '",';
                $csv .= '"' . ($detail->barang->nama_barang ?? '-') . '",';
                $csv .= '"' . ($detail->barang->kategori->nama_kategori ?? '-') . '",';
                $csv .= '"' . $detail->qty . '",';
                $csv .= '"' . $detail->harga_satuan . '",';
                $csv .= '"' . $detail->subtotal . '",';
                $csv .= '"' . $detail->status_permintaan . '"';
                $csv .= "\n";
            }
        }
        
        return $csv;
    }

    // ========================================
    // 📊 NATIVE EXCEL EXPORT METHODS
    // ========================================


    private function getDailyDataForExcel($date)
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
            'title' => 'Transaksi_' . $targetDate->format('d_m_Y'),
            'headers' => ['No Transaksi', 'Tanggal', 'Waktu', 'Customer', 'Kasir', 'Status', 'Total', 'Kendaraan'],
            'rows' => $rows
        ];
    }

    private function getStockDataForExcel()
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
            'title' => 'Stock_' . now()->format('d_m_Y'),
            'headers' => ['Kode', 'Nama', 'Kategori', 'Satuan', 'Stok', 'Status', 'Harga Beli', 'Harga Jual', 'ROP', 'Nilai'],
            'rows' => $rows
        ];
    }

    private function getRestockDataForExcel()
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
            'title' => 'Belanja_' . now()->format('d_m_Y'),
            'headers' => ['Kode', 'Nama', 'Kategori', 'Stok', 'ROP', 'Saran Beli', 'Harga', 'Estimasi'],
            'rows' => $rows
        ];
    }

    private function getMonthlyDataForExcel($month)
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
            'title' => 'Monthly_' . $targetMonth->format('M_Y'),
            'headers' => ['Tanggal', 'No Transaksi', 'Customer', 'Kasir', 'Status', 'Total'],
            'rows' => $rows
        ];
    }

    /**
     * Generate Stock Report CSV
     */
    private function generateStockCsv()
    {
        $barangList = Barang::with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->orderBy('nama_barang')
            ->get();

        $csv = "LAPORAN STOK INVENTORY - " . now()->format('d/m/Y H:i') . "\n\n";
        
        $csv .= "Kode,Nama Barang,Kategori,Satuan,Jumlah Stok,Status Stok,Harga Beli,Harga Jual,Reorder Point,Nilai Stok\n";
        
        foreach($barangList as $barang) {
            $stok = $barang->stok;
            $nilaiStok = ($stok ? $stok->jumlah_stok : 0) * $barang->harga_beli;
            
            $csv .= '"' . $barang->kode_barang . '",';
            $csv .= '"' . $barang->nama_barang . '",';
            $csv .= '"' . ($barang->kategori->nama_kategori ?? '-') . '",';
            $csv .= '"' . $barang->satuan . '",';
            $csv .= '"' . ($stok ? $stok->jumlah_stok : 0) . '",';
            $csv .= '"' . ($stok ? $stok->status_stok : 'No Stock') . '",';
            $csv .= '"' . $barang->harga_beli . '",';
            $csv .= '"' . $barang->harga_jual . '",';
            $csv .= '"' . $barang->reorder_point . '",';
            $csv .= '"' . $nilaiStok . '"';
            $csv .= "\n";
        }
        
        return $csv;
    }

    /**
     * Generate Restock Report CSV
     */
    private function generateRestockCsv()
    {
        $barangHarusBeli = Barang::whereHas('stok', function($q) {
                $q->whereRaw('jumlah_stok <= barang.reorder_point');
            })
            ->with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->get();

        $csv = "LAPORAN BELANJA - BARANG HARUS DIBELI - " . now()->format('d/m/Y H:i') . "\n\n";
        
        $csv .= "Kode,Nama Barang,Kategori,Stok Saat Ini,Reorder Point,Saran Qty Beli,Harga Beli Satuan,Estimasi Total Harga\n";
        
        foreach($barangHarusBeli as $barang) {
            $saranBeli = $barang->eoq_qty ?? ($barang->reorder_point * 2);
            $estimasiHarga = $saranBeli * $barang->harga_beli;
            
            $csv .= '"' . $barang->kode_barang . '",';
            $csv .= '"' . $barang->nama_barang . '",';
            $csv .= '"' . ($barang->kategori->nama_kategori ?? '-') . '",';
            $csv .= '"' . ($barang->stok ? $barang->stok->jumlah_stok : 0) . '",';
            $csv .= '"' . $barang->reorder_point . '",';
            $csv .= '"' . $saranBeli . '",';
            $csv .= '"' . $barang->harga_beli . '",';
            $csv .= '"' . $estimasiHarga . '"';
            $csv .= "\n";
        }
        
        return $csv;
    }

    /**
     * Generate Monthly Report CSV
     */
    private function generateMonthlyCsv($month)
    {
        $targetMonth = Carbon::createFromFormat('Y-m', $month);
        $startDate = $targetMonth->copy()->startOfMonth();
        $endDate = $targetMonth->copy()->endOfMonth();

        // Get monthly transactions
        $transactions = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->with(['kasir'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Get monthly purchases
        $purchases = BarangMasuk::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->orderBy('tanggal_masuk', 'desc')
            ->get();

        $csv = "LAPORAN BULANAN - " . $targetMonth->format('F Y') . "\n";
        $csv .= "Generated at: " . now()->format('d/m/Y H:i') . "\n\n";
        
        // Summary
        $totalOmzet = $transactions->where('status_transaksi', 'Selesai')->sum('total_harga');
        $totalPengeluaran = $purchases->sum('total_nilai');
        $profitEstimation = $totalOmzet - $totalPengeluaran;
        
        $csv .= "RINGKASAN BULAN " . $targetMonth->format('F Y') . "\n";
        $csv .= "Total Omzet," . $totalOmzet . "\n";
        $csv .= "Total Pengeluaran Beli Barang," . $totalPengeluaran . "\n";
        $csv .= "Profit Estimation," . $profitEstimation . "\n";
        $csv .= "Total Transaksi," . $transactions->count() . "\n\n";
        
        // Transactions detail
        $csv .= "DETAIL TRANSAKSI\n";
        $csv .= "Tanggal,No Transaksi,Customer,Kasir,Status,Total\n";
        
        foreach($transactions as $trx) {
            $csv .= '"' . Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') . '",';
            $csv .= '"' . $trx->nomor_transaksi . '",';
            $csv .= '"' . ($trx->nama_customer ?? '-') . '",';
            $csv .= '"' . ($trx->kasir->name ?? '-') . '",';
            $csv .= '"' . $trx->status_transaksi . '",';
            $csv .= '"' . $trx->total_harga . '"';
            $csv .= "\n";
        }
        
        // Purchases detail
        $csv .= "\nDETAIL PEMBELIAN BARANG\n";
        $csv .= "Tanggal,No Masuk,Supplier,Total Nilai,Keterangan\n";
        
        foreach($purchases as $purchase) {
            $csv .= '"' . Carbon::parse($purchase->tanggal_masuk)->format('d/m/Y') . '",';
            $csv .= '"' . $purchase->nomor_masuk . '",';
            $csv .= '"' . ($purchase->supplier ?? '-') . '",';
            $csv .= '"' . $purchase->total_nilai . '",';
            $csv .= '"' . ($purchase->keterangan ?? '-') . '"';
            $csv .= "\n";
        }
        
        return $csv;
    }

    public function exportSimpleExcel(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        try {
            \Log::info('Simple Excel Export:', ['type' => $type, 'date' => $date]);
            
            // Generate data berdasarkan type
            switch($type) {
                case 'daily':
                    $data = $this->getDailyDataArray($date);
                    $filename = 'laporan-harian-' . $date . '.xls';
                    break;
                    
                case 'stock':
                    $data = $this->getStockDataArray();
                    $filename = 'laporan-stok-' . $date . '.xls';
                    break;
                    
                case 'restock':
                    $data = $this->getRestockDataArray();
                    $filename = 'laporan-belanja-' . $date . '.xls';
                    break;
                    
                case 'monthly':
                    $month = $request->get('month', now()->format('Y-m'));
                    $data = $this->getMonthlyDataArray($month);
                    $filename = 'laporan-bulanan-' . $month . '.xls';
                    break;
                    
                default:
                    throw new \Exception('Invalid export type: ' . $type);
            }
            
            // Try Laravel Excel first
            if (class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                try {
                    return $this->exportExcelDetail($request);
                } catch (\Exception $e) {
                    \Log::warning('Laravel Excel failed, using native: ' . $e->getMessage());
                }
            }
            
            // Use native Excel XML generation
            return $this->generateNativeExcel($data, $filename);
            
        } catch (\Exception $e) {
            \Log::error('Simple Excel Export Error: ' . $e->getMessage());
            
            // Last resort: CSV
            return $this->exportFallbackCsv($request);
        }
    }

    // Helper methods yang dibutuhkan
    private function getDailyDataArray($date)
    {
        $targetDate = Carbon::parse($date);
        
        $transactions = Transaksi::whereDate('tanggal_transaksi', $targetDate)
            ->with(['kasir'])
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = ['No Transaksi', 'Tanggal', 'Waktu', 'Customer', 'Kasir', 'Status', 'Total', 'Kendaraan'];
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
            'title' => 'Transaksi ' . $targetDate->format('d-m-Y'),
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function getStockDataArray()
    {
        $barangList = Barang::with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->orderBy('nama_barang')
            ->get();

        $headers = ['Kode', 'Nama', 'Kategori', 'Satuan', 'Stok', 'Status', 'Harga Beli', 'Harga Jual', 'ROP', 'Nilai'];
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
            'title' => 'Stock ' . now()->format('d-m-Y'),
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function getRestockDataArray()
    {
        $barangHarusBeli = Barang::whereHas('stok', function($q) {
                $q->whereRaw('jumlah_stok <= barang.reorder_point');
            })
            ->with(['stok', 'kategori'])
            ->orderBy('id_kategori')
            ->get();

        $headers = ['Kode', 'Nama', 'Kategori', 'Stok', 'ROP', 'Saran Beli', 'Harga', 'Estimasi'];
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
            'title' => 'Belanja ' . now()->format('d-m-Y'),
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function getMonthlyDataArray($month)
    {
        $targetMonth = Carbon::createFromFormat('Y-m', $month);
        $startDate = $targetMonth->copy()->startOfMonth();
        $endDate = $targetMonth->copy()->endOfMonth();

        $transactions = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->with(['kasir'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $headers = ['Tanggal', 'No Transaksi', 'Customer', 'Kasir', 'Status', 'Total'];
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
            'title' => 'Monthly ' . $targetMonth->format('M-Y'),
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    private function generateNativeExcel($data, $filename)
    {
         try {
            \Log::info('Generating native Excel:', ['filename' => $filename]);
            
            // Generate Excel XML format (compatible dengan semua Excel versions)
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
            $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
            $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
            $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
            
            // Document properties
            $xml .= '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">' . "\n";
            $xml .= '<Title>' . htmlspecialchars($data['title']) . '</Title>' . "\n";
            $xml .= '<Author>Bengkel Inventory System</Author>' . "\n";
            $xml .= '<Created>' . now()->toISOString() . '</Created>' . "\n";
            $xml .= '</DocumentProperties>' . "\n";
            
            // Styles
            $xml .= '<Styles>' . "\n";
            $xml .= '<Style ss:ID="Header">' . "\n";
            $xml .= '<Font ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
            $xml .= '<Interior ss:Color="#366092" ss:Pattern="Solid"/>' . "\n";
            $xml .= '<Borders>' . "\n";
            $xml .= '<Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
            $xml .= '<Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
            $xml .= '<Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
            $xml .= '<Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>' . "\n";
            $xml .= '</Borders>' . "\n";
            $xml .= '</Style>' . "\n";
            $xml .= '<Style ss:ID="Currency">' . "\n";
            $xml .= '<NumberFormat ss:Format="#,##0"/>' . "\n";
            $xml .= '</Style>' . "\n";
            $xml .= '<Style ss:ID="Date">' . "\n";
            $xml .= '<NumberFormat ss:Format="dd/mm/yyyy"/>' . "\n";
            $xml .= '</Style>' . "\n";
            $xml .= '</Styles>' . "\n";
            
            // Worksheet
            $xml .= '<Worksheet ss:Name="' . htmlspecialchars($data['title']) . '">' . "\n";
            $xml .= '<Table>' . "\n";
            
            // Headers
            $xml .= '<Row>' . "\n";
            foreach($data['headers'] as $header) {
                $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
            }
            $xml .= '</Row>' . "\n";
            
            // Data rows
            foreach($data['rows'] as $row) {
                $xml .= '<Row>' . "\n";
                foreach($row as $cell) {
                    // Detect data type
                    if (is_numeric($cell) && !str_starts_with((string)$cell, '0') && strlen((string)$cell) < 15) {
                        $xml .= '<Cell ss:StyleID="Currency"><Data ss:Type="Number">' . $cell . '</Data></Cell>' . "\n";
                    } elseif ($this->isDate($cell)) {
                        $xml .= '<Cell ss:StyleID="Date"><Data ss:Type="String">' . htmlspecialchars($cell ?? '') . '</Data></Cell>' . "\n";
                    } else {
                        $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($cell ?? '') . '</Data></Cell>' . "\n";
                    }
                }
                $xml .= '</Row>' . "\n";
            }
            
            $xml .= '</Table>' . "\n";
            $xml .= '</Worksheet>' . "\n";
            $xml .= '</Workbook>';
            
            \Log::info('Native Excel generated successfully', ['size' => strlen($xml)]);
            
            // Return response dengan headers yang benar
            return response($xml)
                ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0')
                ->header('Content-Length', strlen($xml));
                
        } catch (\Exception $e) {
            \Log::error('Native Excel Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }


    private function isDate($value)
    {
        if (!is_string($value)) return false;
        
        return preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value) || 
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    }

    private function exportFallbackCsv($request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        try {
            \Log::info('Using CSV fallback for:', ['type' => $type]);
            
            $filename = '';
            $csvContent = '';
            
            switch($type) {
                case 'daily':
                    $filename = 'laporan-harian-' . $date . '.csv';
                    $csvContent = $this->generateDailyCsv($date);
                    break;
                    
                case 'stock':
                    $filename = 'laporan-stok-' . $date . '.csv';
                    $csvContent = $this->generateStockCsv();
                    break;
                    
                case 'restock':
                    $filename = 'laporan-belanja-' . $date . '.csv';
                    $csvContent = $this->generateRestockCsv();
                    break;
                    
                case 'monthly':
                    $month = $request->get('month', now()->format('Y-m'));
                    $filename = 'laporan-bulanan-' . $month . '.csv';
                    $csvContent = $this->generateMonthlyCsv($month);
                    break;
                    
                default:
                    throw new \Exception('Invalid type for CSV fallback');
            }
            
            // Add BOM for proper UTF-8 encoding
            $csvContent = "\xEF\xBB\xBF" . $csvContent;
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=UTF-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            \Log::error('CSV Fallback Error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'All export methods failed: ' . $e->getMessage()
            ], 500);
        }
    }
}