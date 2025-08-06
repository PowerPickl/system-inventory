<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\RestockRequest;
use App\Models\LogStok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Dashboard utama laporan
     */
    public function index()
    {
        return view('owner.reports.index');
    }

    // ========================================
    // 📈 SALES ANALYSIS REPORTS
    // ========================================

    /**
     * Daily/Weekly/Monthly Revenue
     */
    public function revenueAnalysis(Request $request)
    {
        try {
            $period = $request->get('period', 'monthly');
            $startDate = $request->get('start_date', now()->startOfMonth());
            $endDate = $request->get('end_date', now()->endOfMonth());

            // Simple summary only first
            $summary = [
                'total_revenue' => Transaksi::where('status_transaksi', 'Selesai')
                                           ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                           ->sum('total_harga'),
                'total_transactions' => Transaksi::where('status_transaksi', 'Selesai')
                                                 ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                                 ->count(),
                'average_transaction' => Transaksi::where('status_transaksi', 'Selesai')
                                                 ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                                 ->avg('total_harga'),
                'growth_rate' => 0
            ];

            // Simplified data - just return empty for now
            $data = [];

            return response()->json([
                'period' => $period,
                'data' => $data,
                'summary' => $summary,
                'debug' => 'Simplified version - working!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Top 10 Selling Items
     */
    public function topSellingItems(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $limit = $request->get('limit', 10);

        $topItems = DetailTransaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                    ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
                    ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
                    ->where('transaksi.status_transaksi', 'Selesai')
                    ->where('detail_transaksi.status_permintaan', 'Approved')
                    ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
                    ->select([
                        'barang.id_barang',
                        'barang.nama_barang',
                        'barang.kode_barang',
                        'kategori_barang.nama_kategori',
                        DB::raw('SUM(detail_transaksi.qty) as total_qty_sold'),
                        DB::raw('SUM(detail_transaksi.subtotal) as total_revenue'),
                        DB::raw('COUNT(DISTINCT transaksi.id_transaksi) as transaction_count'),
                        DB::raw('AVG(detail_transaksi.harga_satuan) as avg_selling_price')
                    ])
                    ->groupBy('barang.id_barang', 'barang.nama_barang', 'barang.kode_barang', 'kategori_barang.nama_kategori')
                    ->orderBy('total_qty_sold', 'desc')
                    ->limit($limit)
                    ->get();

        return response()->json([
            'top_items' => $topItems,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Transaction Summary
     */
    public function transactionSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $summary = [
            'total_transactions' => Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])->count(),
            'completed_transactions' => Transaksi::where('status_transaksi', 'Selesai')
                                               ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                               ->count(),
            'progress_transactions' => Transaksi::where('status_transaksi', 'Progress')
                                               ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                               ->count(),
            'total_revenue' => Transaksi::where('status_transaksi', 'Selesai')
                                       ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                       ->sum('total_harga'),
            'avg_transaction_value' => Transaksi::where('status_transaksi', 'Selesai')
                                               ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                               ->avg('total_harga')
        ];

        // Transaction by day
        $dailyTransactions = Transaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
                                     ->select([
                                         DB::raw('DATE(tanggal_transaksi) as date'),
                                         DB::raw('COUNT(*) as transaction_count'),
                                         DB::raw('SUM(CASE WHEN status_transaksi = "Selesai" THEN total_harga ELSE 0 END) as daily_revenue')
                                     ])
                                     ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
                                     ->orderBy('date')
                                     ->get();

        return response()->json([
            'summary' => $summary,
            'daily_breakdown' => $dailyTransactions
        ]);
    }

    // ========================================
    // 💰 PROFIT ANALYSIS REPORTS
    // ========================================

    /**
     * Gross Profit per Kategori
     */
    public function profitByCategory(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $profitData = DetailTransaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                      ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
                      ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
                      ->where('transaksi.status_transaksi', 'Selesai')
                      ->where('detail_transaksi.status_permintaan', 'Approved')
                      ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
                      ->select([
                          'kategori_barang.id_kategori',
                          'kategori_barang.nama_kategori',
                          'kategori_barang.kode_kategori',
                          DB::raw('SUM(detail_transaksi.qty * barang.harga_beli) as total_cost'),
                          DB::raw('SUM(detail_transaksi.subtotal) as total_revenue'),
                          DB::raw('SUM(detail_transaksi.subtotal) - SUM(detail_transaksi.qty * barang.harga_beli) as gross_profit'),
                          DB::raw('((SUM(detail_transaksi.subtotal) - SUM(detail_transaksi.qty * barang.harga_beli)) / SUM(detail_transaksi.subtotal) * 100) as profit_margin_percentage'),
                          DB::raw('SUM(detail_transaksi.qty) as total_qty_sold'),
                          DB::raw('COUNT(DISTINCT transaksi.id_transaksi) as transaction_count')
                      ])
                      ->groupBy('kategori_barang.id_kategori', 'kategori_barang.nama_kategori', 'kategori_barang.kode_kategori')
                      ->orderBy('gross_profit', 'desc')
                      ->get();

        $totalProfit = $profitData->sum('gross_profit');
        
        return response()->json([
            'category_profits' => $profitData,
            'total_gross_profit' => $totalProfit,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Profit Margin Trends
     */
    public function profitMarginTrends(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(6));
        $endDate = $request->get('end_date', now());
        $period = $request->get('period', 'monthly'); // daily, weekly, monthly

        $trends = [];
        
        if ($period === 'monthly') {
            $trends = DetailTransaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                      ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
                      ->where('transaksi.status_transaksi', 'Selesai')
                      ->where('detail_transaksi.status_permintaan', 'Approved')
                      ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
                      ->select([
                          DB::raw('YEAR(transaksi.tanggal_transaksi) as year'),
                          DB::raw('MONTH(transaksi.tanggal_transaksi) as month'),
                          DB::raw('SUM(detail_transaksi.qty * barang.harga_beli) as total_cost'),
                          DB::raw('SUM(detail_transaksi.subtotal) as total_revenue'),
                          DB::raw('SUM(detail_transaksi.subtotal) - SUM(detail_transaksi.qty * barang.harga_beli) as gross_profit'),
                          DB::raw('((SUM(detail_transaksi.subtotal) - SUM(detail_transaksi.qty * barang.harga_beli)) / SUM(detail_transaksi.subtotal) * 100) as profit_margin_percentage')
                      ])
                      ->groupBy('year', 'month')
                      ->orderBy('year')
                      ->orderBy('month')
                      ->get();
        }

        return response()->json([
            'trends' => $trends,
            'period' => $period
        ]);
    }

    // ========================================
    // 🔄 ADVANCED RESTOCK INTELLIGENCE
    // ========================================

    /**
     * Restock Pattern Analysis
     */
    public function restockPatternAnalysis(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(6));
        $endDate = $request->get('end_date', now());

        // Analisis frekuensi restock per barang
        $restockFrequency = RestockRequest::join('restock_request_detail', 'restock_request.id_request', '=', 'restock_request_detail.id_request')
                           ->join('barang', 'restock_request_detail.id_barang', '=', 'barang.id_barang')
                           ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
                           ->where('restock_request.status_request', 'Completed')
                           ->whereBetween('restock_request.tanggal_request', [$startDate, $endDate])
                           ->select([
                               'barang.id_barang',
                               'barang.nama_barang',
                               'barang.kode_barang',
                               'kategori_barang.nama_kategori',
                               DB::raw('COUNT(*) as restock_count'),
                               DB::raw('SUM(restock_request_detail.qty_approved) as total_qty_restocked'),
                               DB::raw('AVG(restock_request_detail.qty_approved) as avg_qty_per_restock'),
                               DB::raw('MIN(restock_request.tanggal_request) as first_restock'),
                               DB::raw('MAX(restock_request.tanggal_request) as last_restock')
                           ])
                           ->groupBy('barang.id_barang', 'barang.nama_barang', 'barang.kode_barang', 'kategori_barang.nama_kategori')
                           ->orderBy('restock_count', 'desc')
                           ->get();

        // Calculate average days between restocks
        foreach($restockFrequency as $item) {
            if($item->restock_count > 1) {
                $daysBetween = Carbon::parse($item->first_restock)->diffInDays(Carbon::parse($item->last_restock));
                $item->avg_days_between_restock = round($daysBetween / ($item->restock_count - 1), 1);
            } else {
                $item->avg_days_between_restock = null;
            }
        }

        // Seasonal analysis (by month)
        $seasonalPattern = RestockRequest::join('restock_request_detail', 'restock_request.id_request', '=', 'restock_request_detail.id_request')
                          ->where('restock_request.status_request', 'Completed')
                          ->whereBetween('restock_request.tanggal_request', [$startDate, $endDate])
                          ->select([
                              DB::raw('MONTH(restock_request.tanggal_request) as month'),
                              DB::raw('COUNT(*) as restock_count'),
                              DB::raw('SUM(restock_request_detail.qty_approved) as total_qty')
                          ])
                          ->groupBy(DB::raw('MONTH(restock_request.tanggal_request)'))
                          ->orderBy('month')
                          ->get();

        return response()->json([
            'frequency_analysis' => $restockFrequency,
            'seasonal_pattern' => $seasonalPattern
        ]);
    }

    /**
     * Lead Time Performance
     */
    public function leadTimePerformance(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(3));
        $endDate = $request->get('end_date', now());

        $leadTimeData = RestockRequest::where('status_request', 'Completed')
                        ->whereBetween('tanggal_request', [$startDate, $endDate])
                        ->whereNotNull('tanggal_approved')
                        ->whereNotNull('tanggal_ordered')
                        ->select([
                            'id_request',
                            'nomor_request',
                            'tanggal_request',
                            'tanggal_approved',
                            'tanggal_ordered',
                            'updated_at as tanggal_completed',
                            DB::raw('DATEDIFF(tanggal_approved, tanggal_request) as approval_time_days'),
                            DB::raw('DATEDIFF(tanggal_ordered, tanggal_approved) as ordering_time_days'),
                            DB::raw('DATEDIFF(updated_at, tanggal_ordered) as completion_time_days'),
                            DB::raw('DATEDIFF(updated_at, tanggal_request) as total_lead_time_days')
                        ])
                        ->orderBy('tanggal_request', 'desc')
                        ->get();

        $summary = [
            'avg_approval_time' => $leadTimeData->avg('approval_time_days'),
            'avg_ordering_time' => $leadTimeData->avg('ordering_time_days'),
            'avg_completion_time' => $leadTimeData->avg('completion_time_days'),
            'avg_total_lead_time' => $leadTimeData->avg('total_lead_time_days'),
            'total_requests' => $leadTimeData->count()
        ];

        return response()->json([
            'lead_time_data' => $leadTimeData,
            'summary' => $summary
        ]);
    }

    /**
     * Restock Cost Efficiency
     */
    public function restockCostEfficiency(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(6));
        $endDate = $request->get('end_date', now());

        $costData = RestockRequest::join('restock_request_detail', 'restock_request.id_request', '=', 'restock_request_detail.id_request')
                    ->join('barang', 'restock_request_detail.id_barang', '=', 'barang.id_barang')
                    ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
                    ->where('restock_request.status_request', 'Completed')
                    ->whereBetween('restock_request.tanggal_request', [$startDate, $endDate])
                    ->select([
                        'kategori_barang.nama_kategori',
                        DB::raw('COUNT(DISTINCT restock_request.id_request) as request_count'),
                        DB::raw('SUM(restock_request_detail.estimasi_harga) as total_estimated_cost'),
                        DB::raw('SUM(restock_request_detail.qty_approved) as total_qty_ordered'),
                        DB::raw('AVG(restock_request_detail.estimasi_harga / restock_request_detail.qty_approved) as avg_cost_per_unit')
                    ])
                    ->groupBy('kategori_barang.nama_kategori')
                    ->orderBy('total_estimated_cost', 'desc')
                    ->get();

        return response()->json([
            'cost_by_category' => $costData,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Demand Forecasting
     */
    public function demandForecasting(Request $request)
    {
        $lookbackMonths = $request->get('lookback_months', 6);
        $forecastMonths = $request->get('forecast_months', 3);
        
        $startDate = now()->subMonths($lookbackMonths);
        $endDate = now();

        // Historical demand data
        $historicalDemand = DetailTransaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                           ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
                           ->where('transaksi.status_transaksi', 'Selesai')
                           ->where('detail_transaksi.status_permintaan', 'Approved')
                           ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
                           ->select([
                               'barang.id_barang',
                               'barang.nama_barang',
                               'barang.kode_barang',
                               DB::raw('YEAR(transaksi.tanggal_transaksi) as year'),
                               DB::raw('MONTH(transaksi.tanggal_transaksi) as month'),
                               DB::raw('SUM(detail_transaksi.qty) as monthly_demand')
                           ])
                           ->groupBy('barang.id_barang', 'barang.nama_barang', 'barang.kode_barang', 'year', 'month')
                           ->orderBy('barang.nama_barang')
                           ->orderBy('year')
                           ->orderBy('month')
                           ->get();

        // Calculate forecasts (simple moving average)
        $forecasts = $this->calculateDemandForecast($historicalDemand, $forecastMonths);

        return response()->json([
            'historical_demand' => $historicalDemand,
            'forecasts' => $forecasts,
            'parameters' => [
                'lookback_months' => $lookbackMonths,
                'forecast_months' => $forecastMonths
            ]
        ]);
    }

    // ========================================
    // 📤 EXPORT FUNCTIONS
    // ========================================

    /**
     * Export Sales Data to Excel/CSV
     */
    public function exportSalesData(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel, csv
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Get comprehensive sales data
        $salesData = DetailTransaksi::join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                     ->join('barang', 'detail_transaksi.id_barang', '=', 'barang.id_barang')
                     ->join('kategori_barang', 'barang.id_kategori', '=', 'kategori_barang.id_kategori')
                     ->join('users', 'transaksi.id_user', '=', 'users.id')
                     ->where('transaksi.status_transaksi', 'Selesai')
                     ->where('detail_transaksi.status_permintaan', 'Approved')
                     ->whereBetween('transaksi.tanggal_transaksi', [$startDate, $endDate])
                     ->select([
                         'transaksi.nomor_transaksi',
                         'transaksi.tanggal_transaksi',
                         'transaksi.nama_customer',
                         'users.name as kasir_name',
                         'barang.kode_barang',
                         'barang.nama_barang',
                         'kategori_barang.nama_kategori',
                         'detail_transaksi.qty',
                         'detail_transaksi.harga_satuan',
                         'detail_transaksi.subtotal',
                         'barang.harga_beli',
                         DB::raw('(detail_transaksi.subtotal - (detail_transaksi.qty * barang.harga_beli)) as profit')
                     ])
                     ->orderBy('transaksi.tanggal_transaksi', 'desc')
                     ->get();

        // Implementation depends on your export library (Laravel Excel, etc.)
        // This would return either Excel file or CSV download
        
        return response()->json([
            'message' => 'Export functionality would be implemented here',
            'data_count' => $salesData->count(),
            'format' => $format
        ]);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function getDailyRevenue($query, $startDate, $endDate)
    {
        return $query->select([
                    DB::raw('DATE(tanggal_transaksi) as date'),
                    DB::raw('SUM(total_harga) as revenue'),
                    DB::raw('COUNT(*) as transaction_count')
                ])
                ->groupBy(DB::raw('DATE(tanggal_transaksi)'))
                ->orderBy('date', 'asc')
                ->get();
    }

    private function getWeeklyRevenue($query, $startDate, $endDate)
    {
        return $query->select([
                    DB::raw('YEARWEEK(tanggal_transaksi) as week'),
                    DB::raw('SUM(total_harga) as revenue'),
                    DB::raw('COUNT(*) as transaction_count')
                ])
                ->groupBy(DB::raw('YEARWEEK(tanggal_transaksi)'))
                ->orderBy('week', 'asc')
                ->get();
    }

    private function getMonthlyRevenue($query, $startDate, $endDate)
    {
        return $query->select([
                    DB::raw('YEAR(tanggal_transaksi) as year'),
                    DB::raw('MONTH(tanggal_transaksi) as month'),
                    DB::raw('SUM(total_harga) as revenue'),
                    DB::raw('COUNT(*) as transaction_count')
                ])
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
    }

    private function calculateGrowthRate($period, $startDate, $endDate)
    {
        // Simple growth rate calculation
        // This would compare current period with previous period
        return 0; // Placeholder
    }

    private function calculateDemandForecast($historicalData, $forecastMonths)
    {
        // Simple moving average forecast
        // Group by barang and calculate average demand
        $forecasts = [];
        
        $groupedData = $historicalData->groupBy('id_barang');
        
        foreach($groupedData as $barangId => $demands) {
            $avgDemand = $demands->avg('monthly_demand');
            $forecasts[] = [
                'id_barang' => $barangId,
                'nama_barang' => $demands->first()->nama_barang,
                'avg_monthly_demand' => round($avgDemand, 2),
                'forecast_next_3_months' => round($avgDemand * $forecastMonths, 0)
            ];
        }
        
        return collect($forecasts)->sortByDesc('avg_monthly_demand')->values();
    }
}