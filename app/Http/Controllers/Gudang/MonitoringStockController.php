<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\LogStok;
use App\Models\RestockRequest;
use App\Models\RestockRequestDetail;
use App\Services\EOQCalculationService;
use App\Jobs\UpdateEOQCalculations;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonitoringStockController extends Controller
{
    protected $eoqService;

    public function __construct(EOQCalculationService $eoqService)
    {
        $this->eoqService = $eoqService;
    }

    /**
     * Display monitoring stock dashboard
     */
    public function index()
    {
        // Get all items with stock and EOQ data
        $items = Barang::with(['stok'])
                      ->whereHas('stok')
                      ->get()
                     ->map(function ($item) {
                        $recommendation = $item->getRestockRecommendation();
                        $currentStock = $item->stok->jumlah_stok;
                        $rop = $item->rop_calculated ?? $item->reorder_point;
                        
                        // Fix status logic
                        $status = 'Optimal Level';
                        if ($currentStock <= 0) {
                            $status = 'Critical - Out of Stock';
                        } elseif ($currentStock <= $rop) {
                            $status = 'Reorder Required';  
                        } elseif ($currentStock <= $rop * 1.5) {
                            $status = 'Monitor Closely';
                        }

                        return [
                            'item' => $item,
                            'current_stock' => $currentStock,
                            'status' => $item->stok->status_stok,
                            'eoq_status' => $status,  // Use calculated status
                            'eoq' => $item->eoq_calculated,
                            'rop' => $rop,
                            'safety_stock' => $item->safety_stock,
                            'recommendation' => $recommendation,
                            'last_calculated' => $item->last_eoq_calculation
                          ];
                      });

        // Categorize items
        $optimal = $items->where('recommendation.urgency', 'Normal')->count();
        $needRestock = $items->where('recommendation.need_restock', true)->count();
        $critical = $items->where('recommendation.urgency', 'Critical')->count();

        // Recent stock movements (today)
        $recentMovements = LogStok::with(['barang', 'user'])
                                 ->whereDate('tanggal_log', today())
                                 ->orderBy('tanggal_log', 'desc')
                                 ->limit(10)
                                 ->get();

        // EOQ calculation statistics
        $eoqStats = [
            'total_items' => $items->count(),
            'with_eoq' => $items->whereNotNull('eoq')->count(),
            'optimal_level' => $optimal,
            'need_restock' => $needRestock,
            'critical_stock' => $critical,
            'last_batch_update' => Barang::max('last_eoq_calculation')
        ];

        return view('gudang.monitoring-stock', compact(
            'items', 
            'eoqStats', 
            'recentMovements'
        ));
    }

    /**
     * Force update EOQ for specific item
     */
    public function updateEOQ($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            
            // Dispatch job for this specific item
            UpdateEOQCalculations::dispatch($id, true);
            
            return response()->json([
                'success' => true,
                'message' => "EOQ update initiated for {$barang->nama_barang}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force update all EOQ calculations
     */
    public function updateAllEOQ()
    {
        try {
            UpdateEOQCalculations::dispatch(null, true);
            
            return response()->json([
                'success' => true,
                'message' => 'EOQ batch update initiated for all items'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time stock data for AJAX updates
     */
    public function getRealTimeData()
    {
        $items = Barang::with('stok')
                      ->whereHas('stok')
                      ->get()
                      ->map(function ($item) {
                          $recommendation = $item->getRestockRecommendation();
                          
                          return [
                              'id' => $item->id_barang,
                              'current_stock' => $item->stok->jumlah_stok,
                              'eoq' => $item->eoq_calculated,
                              'rop' => $item->rop_calculated ?? $item->reorder_point,
                              'status' => $item->eoq_status,
                              'need_restock' => $recommendation['need_restock'],
                              'urgency' => $recommendation['urgency'],
                              'recommended_qty' => $recommendation['recommended_qty'],
                              'last_updated' => $item->last_eoq_calculation?->format('H:i')
                          ];
                      });

        return response()->json([
            'success' => true,
            'data' => $items,
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    /**
     * Get EOQ calculation details for modal
     */
    public function getEOQDetails($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            
            // Get fresh calculations
            $result = $this->eoqService->calculateAll($barang);
            
            return response()->json([
                'success' => true,
                'item' => [
                    'name' => $barang->nama_barang,
                    'code' => $barang->kode_barang
                ],
                'calculations' => $result,
                'parameters' => [
                    'annual_demand' => $barang->annual_demand,
                    'ordering_cost' => $barang->ordering_cost,
                    'holding_cost' => $barang->holding_cost,
                    'lead_time' => $barang->lead_time,
                    'demand_avg_daily' => $barang->demand_avg_daily,
                    'demand_max_daily' => $barang->demand_max_daily
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock movement trends for charts
     */
    public function getStockTrends($id, Request $request)
    {
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);
        
        $movements = LogStok::where('id_barang', $id)
                           ->where('tanggal_log', '>=', $startDate)
                           ->orderBy('tanggal_log')
                           ->get();

        $chartData = $movements->groupBy(function ($item) {
            return $item->tanggal_log->format('Y-m-d');
        })->map(function ($dayMovements) {
            return [
                'masuk' => $dayMovements->where('jenis_perubahan', 'Masuk')->sum('qty_perubahan'),
                'keluar' => abs($dayMovements->where('jenis_perubahan', 'Keluar')->sum('qty_perubahan')),
                'stock_end' => $dayMovements->last()->qty_sesudah ?? 0
            ];
        });

        return response()->json([
            'success' => true,
            'chart_data' => $chartData,
            'period' => $days . ' days'
        ]);
    }

    /**
     * Create restock request (ENHANCED)
     */
    public function createRestockRequest(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.qty_request' => 'required|integer|min:1',
            'items.*.alasan_request' => 'nullable|string|max:255',
            'catatan_request' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Create main restock request
            $restockRequest = RestockRequest::create([
                'id_user_gudang' => Auth::id(),
                'tanggal_request' => now(),
                'status_request' => 'Pending',
                'catatan_request' => $request->catatan_request
            ]);

            // Create request details
            foreach ($request->items as $itemData) {
                $barang = Barang::find($itemData['id_barang']);
                
                // Calculate estimated cost
                $estimasiHarga = $barang->harga_beli * $itemData['qty_request'];

                RestockRequestDetail::create([
                    'id_request' => $restockRequest->id_request,
                    'id_barang' => $itemData['id_barang'],
                    'qty_request' => $itemData['qty_request'],
                    'estimasi_harga' => $estimasiHarga,
                    'alasan_request' => $itemData['alasan_request'] ?? "Based on EOQ calculation"
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Restock request {$restockRequest->nomor_request} created successfully",
                'request_id' => $restockRequest->id_request,
                'request_number' => $restockRequest->nomor_request,
                'total_items' => count($request->items),
                'redirect_url' => route('gudang.restock-requests')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create restock request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get restock recommendations for selected items
     */
    public function getRestockRecommendations(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:barang,id_barang'
        ]);

        try {
            $items = Barang::with('stok')
                          ->whereIn('id_barang', $request->item_ids)
                          ->get()
                          ->map(function ($item) {
                              $recommendation = $item->getRestockRecommendation();
                              
                              return [
                                  'id_barang' => $item->id_barang,
                                  'nama_barang' => $item->nama_barang,
                                  'kode_barang' => $item->kode_barang,
                                  'current_stock' => $item->stok->jumlah_stok,
                                  'eoq' => $item->eoq_calculated,
                                  'recommended_qty' => $recommendation['recommended_qty'],
                                  'urgency' => $recommendation['urgency'],
                                  'harga_beli' => $item->harga_beli,
                                  'estimasi_total' => $item->harga_beli * $recommendation['recommended_qty'],
                                  'satuan' => $item->satuan,
                                  'alasan_default' => $this->generateDefaultReason($item, $recommendation)
                              ];
                          });

            return response()->json([
                'success' => true,
                'items' => $items,
                'total_estimasi' => $items->sum('estimasi_total')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate default reason for restock request
     */
    private function generateDefaultReason($item, $recommendation)
    {
        $currentStock = $item->stok->jumlah_stok;
        $rop = $item->rop_calculated ?? $item->reorder_point;

        if ($currentStock <= 0) {
            return "Critical: Out of stock - immediate restock needed";
        } elseif ($currentStock <= $rop) {
            return "Stock below ROP ({$rop}) - EOQ calculation suggests {$recommendation['recommended_qty']} units";
        } else {
            return "Preventive restock based on demand pattern analysis";
        }
    }

    /**
     * Quick restock for single item (ENHANCED)
     */
    public function quickRestockRequest(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'qty_request' => 'required|integer|min:1',
            'alasan_request' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::find($request->id_barang);
            $recommendation = $barang->getRestockRecommendation();

            // Create main restock request
            $restockRequest = RestockRequest::create([
                'id_user_gudang' => Auth::id(),
                'tanggal_request' => now(),
                'status_request' => 'Pending',
                'catatan_request' => "Quick restock for {$barang->nama_barang}"
            ]);

            // Create request detail
            RestockRequestDetail::create([
                'id_request' => $restockRequest->id_request,
                'id_barang' => $request->id_barang,
                'qty_request' => $request->qty_request,
                'estimasi_harga' => $barang->harga_beli * $request->qty_request,
                'alasan_request' => $request->alasan_request ?? $this->generateDefaultReason($barang, $recommendation)
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Quick restock request {$restockRequest->nomor_request} created for {$barang->nama_barang}",
                'request_id' => $restockRequest->id_request,
                'request_number' => $restockRequest->nomor_request
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quick restock request: ' . $e->getMessage()
            ], 500);
        }
    }
}