<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\LogStok;
use App\Models\RestockRequest;
use App\Models\RestockRequestDetail;
use App\Services\EOQCalculationService;
use App\Services\UrgencyCalculationService; 
use App\Jobs\UpdateEOQCalculations;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonitoringStockController extends Controller
{
    protected $eoqService;
    protected $urgencyService; 

    public function __construct(
        EOQCalculationService $eoqService,
        UrgencyCalculationService $urgencyService 
    ) {
        $this->eoqService = $eoqService;
        $this->urgencyService = $urgencyService; 
    }

    /**
     * Display monitoring stock dashboard
     */
    public function index()
    {
        // Get all items with stock and EOQ data + URGENCY
        $items = Barang::with(['stok', 'kategori'])
                      ->whereHas('stok')
                      ->get()
                      ->map(function ($item) {
                        $recommendation = $item->getRestockRecommendation();
                        
                        // ADD URGENCY CALCULATION
                        $urgencyData = $this->urgencyService->calculateUrgencyLevel($item);
                        
                        $currentStock = $item->jumlah_stok;
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
                            'eoq_status' => $status,
                            'eoq' => $item->eoq_calculated,
                            'rop' => $rop,
                            'safety_stock' => $item->safety_stock,
                            'recommendation' => $recommendation,
                            'last_calculated' => $item->last_eoq_calculation,
                            // ADD URGENCY DATA
                            'urgency_data' => $urgencyData,
                            'urgency_badge' => $urgencyData['priority_badge'],
                            'auto_reason' => $urgencyData['auto_reason']
                        ];
                      })
                      // SORT BY URGENCY SCORE (highest first)
                      ->sortByDesc('urgency_data.urgency_score')
                      ->values();

        // Enhanced categorization with urgency
        $optimal = $items->where('urgency_data.final_urgency', 'NORMAL')->count();
        $needRestock = $items->whereIn('urgency_data.final_urgency', ['HIGH', 'URGENT'])->count();
        $critical = $items->where('urgency_data.final_urgency', 'URGENT')->count();
        $medium = $items->where('urgency_data.final_urgency', 'MEDIUM')->count();
        $low = $items->where('urgency_data.final_urgency', 'LOW')->count();

        // Group items by urgency for display
        $itemsByUrgency = $items->groupBy('urgency_data.final_urgency')
                               ->sortBy(function ($group, $key) {
                                   $priorities = ['URGENT' => 1, 'HIGH' => 2, 'MEDIUM' => 3, 'LOW' => 4, 'NORMAL' => 5];
                                   return $priorities[$key] ?? 6;
                               });

        // Recent stock movements (today)
        $recentMovements = LogStok::with(['barang', 'user'])
                                 ->whereDate('tanggal_log', today())
                                 ->orderBy('tanggal_log', 'desc')
                                 ->limit(10)
                                 ->get();

        // Enhanced EOQ + Urgency statistics
        $eoqStats = [
            'total_items' => $items->count(),
            'with_eoq' => $items->whereNotNull('eoq')->count(),
            'optimal_level' => $optimal,
            'need_restock' => $needRestock,
            'critical_stock' => $critical,
            'medium_priority' => $medium,
            'low_priority' => $low,
            'last_batch_update' => Barang::max('last_eoq_calculation'),
            // ADD URGENCY STATS
            'urgency_breakdown' => [
                'urgent' => $critical,
                'high' => $items->where('urgency_data.final_urgency', 'HIGH')->count(),
                'medium' => $medium,
                'low' => $low,
                'normal' => $optimal
            ]
        ];

        // Get demand statistics for debugging
        $demandStats = $this->urgencyService->getDemandStatistics();

        return view('gudang.monitoring-stock', compact(
            'items', 
            'itemsByUrgency', // ADD THIS for grouped display
            'eoqStats', 
            'recentMovements',
            'demandStats' // ADD THIS for debugging
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
            
            // Clear urgency cache when EOQ updates
            $this->urgencyService->clearDemandCache();
            
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
            
            // Clear urgency cache when all EOQ updates
            $this->urgencyService->clearDemandCache();
            
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
                        $urgencyData = $this->urgencyService->calculateUrgencyLevel($item);
                        
                        return [
                            'id' => $item->id_barang,
                            'current_stock' => $item->jumlah_stok,
                            'eoq' => $item->eoq_calculated,
                            'rop' => $item->rop_calculated ?? $item->reorder_point,
                            'status' => $item->eoq_status,
                            'need_restock' => $recommendation['need_restock'],
                            'urgency' => $recommendation['urgency'],
                            'recommended_qty' => $recommendation['recommended_qty'],
                            'last_updated' => $item->last_eoq_calculation?->format('H:i'),
                            // ADD URGENCY DATA
                            'urgency_level' => $urgencyData['final_urgency'],
                            'urgency_score' => $urgencyData['urgency_score'],
                            'urgency_badge' => $urgencyData['priority_badge'],
                            'demand_level' => $urgencyData['demand_level'],
                            'days_until_stockout' => $urgencyData['days_until_stockout']
                        ];
                    })
                    ->sortByDesc('urgency_score')
                    ->values() // ← ADD THIS
                    ->toArray(); // ← ADD THIS

        return response()->json([
            'success' => true,
            'data' => $items, // Now this is a proper array
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
            $urgencyData = $this->urgencyService->calculateUrgencyLevel($barang);
            
            return response()->json([
                'success' => true,
                'item' => [
                    'name' => $barang->nama_barang,
                    'code' => $barang->kode_barang
                ],
                'calculations' => $result,
                'urgency_data' => $urgencyData, // ADD THIS
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

            // Calculate bulk urgency for all items
            $barangItems = Barang::whereIn('id_barang', collect($request->items)->pluck('id_barang'))->get();
            $bulkUrgency = $this->urgencyService->calculateBulkUrgency($barangItems);

            // Create main restock request
            $restockRequest = RestockRequest::create([
                'id_user_gudang' => Auth::id(),
                'tanggal_request' => now(),
                'status_request' => 'Pending',
                'catatan_request' => $request->catatan_request,
                // ADD URGENCY FIELDS (if you have them in your table)
                'urgency_level' => $bulkUrgency['primary_urgency'] ?? 'MEDIUM',
                'urgency_score' => $bulkUrgency['avg_urgency_score'] ?? 0
            ]);

            // Create request details with auto-generated reasons
            foreach ($request->items as $itemData) {
                $barang = Barang::find($itemData['id_barang']);
                $urgencyData = $this->urgencyService->calculateUrgencyLevel($barang);
                
                // Calculate estimated cost
                $estimasiHarga = $barang->harga_beli * $itemData['qty_request'];

                // Use auto-generated reason if not provided
                $alasanRequest = $itemData['alasan_request'] ?? $urgencyData['auto_reason'];

                RestockRequestDetail::create([
                    'id_request' => $restockRequest->id_request,
                    'id_barang' => $itemData['id_barang'],
                    'qty_request' => $itemData['qty_request'],
                    'estimasi_harga' => $estimasiHarga,
                    'alasan_request' => $alasanRequest,
                    // ADD URGENCY FIELDS (if you have them)
                    'urgency_level' => $urgencyData['final_urgency'],
                    'urgency_score' => $urgencyData['urgency_score']
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Restock request {$restockRequest->nomor_request} created successfully",
                'request_id' => $restockRequest->id_request,
                'request_number' => $restockRequest->nomor_request,
                'total_items' => count($request->items),
                'urgency_summary' => $bulkUrgency['summary_text'],
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
                            $urgencyData = $this->urgencyService->calculateUrgencyLevel($item);
                            
                            return [
                                'id_barang' => $item->id_barang,
                                'nama_barang' => $item->nama_barang,
                                'kode_barang' => $item->kode_barang,
                                'current_stock' => $item->jumlah_stok,
                                'eoq' => $item->eoq_calculated,
                                'recommended_qty' => $recommendation['recommended_qty'],
                                'urgency' => $recommendation['urgency'],
                                'harga_beli' => $item->harga_beli,
                                'estimasi_total' => $item->harga_beli * $recommendation['recommended_qty'],
                                'satuan' => $item->satuan,
                                // USE AUTO-GENERATED REASON
                                'alasan_default' => $urgencyData['auto_reason'],
                                // ADD URGENCY DATA
                                'urgency_level' => $urgencyData['final_urgency'],
                                'urgency_score' => $urgencyData['urgency_score'],
                                'urgency_badge' => $urgencyData['priority_badge'],
                                'demand_level' => $urgencyData['demand_level'],
                                'days_until_stockout' => $urgencyData['days_until_stockout']
                            ];
                        })
                        ->sortByDesc('urgency_score')
                        ->values() // ← ADD THIS to convert Collection to array with sequential keys
                        ->toArray(); // ← ADD THIS to convert to plain PHP array

            // Calculate bulk urgency
            $barangItems = Barang::whereIn('id_barang', $request->item_ids)->get();
            $bulkUrgency = $this->urgencyService->calculateBulkUrgency($barangItems);

            return response()->json([
                'success' => true,
                'items' => $items, // Now this is a proper array
                'total_estimasi' => collect($items)->sum('estimasi_total'), // Use collect() since $items is now array
                'bulk_urgency' => $bulkUrgency
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
        // Use urgency service for better reasons
        $urgencyData = $this->urgencyService->calculateUrgencyLevel($item);
        return $urgencyData['auto_reason'];
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
            $urgencyData = $this->urgencyService->calculateUrgencyLevel($barang);

            // Create main restock request
            $restockRequest = RestockRequest::create([
                'id_user_gudang' => Auth::id(),
                'tanggal_request' => now(),
                'status_request' => 'Pending',
                'catatan_request' => "Quick restock for {$barang->nama_barang}",
                'urgency_level' => $urgencyData['final_urgency'],
                'urgency_score' => $urgencyData['urgency_score']
            ]);

            // Create request detail with auto-generated reason
            RestockRequestDetail::create([
                'id_request' => $restockRequest->id_request,
                'id_barang' => $request->id_barang,
                'qty_request' => $request->qty_request,
                'estimasi_harga' => $barang->harga_beli * $request->qty_request,
                'alasan_request' => $request->alasan_request ?? $urgencyData['auto_reason'],
                'urgency_level' => $urgencyData['final_urgency'],
                'urgency_score' => $urgencyData['urgency_score']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Quick restock request {$restockRequest->nomor_request} created for {$barang->nama_barang}",
                'request_id' => $restockRequest->id_request,
                'request_number' => $restockRequest->nomor_request,
                'urgency_level' => $urgencyData['final_urgency']
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quick restock request: ' . $e->getMessage()
            ], 500);
        }
    }

     public function getUrgencyStats()
    {
        try {
            $items = Barang::with('stok')->whereHas('stok')->get();
            $urgencyBreakdown = [
                'URGENT' => 0,
                'HIGH' => 0, 
                'MEDIUM' => 0,
                'LOW' => 0,
                'NORMAL' => 0
            ];

            foreach ($items as $item) {
                $urgencyData = $this->urgencyService->calculateUrgencyLevel($item);
                $urgencyBreakdown[$urgencyData['final_urgency']]++;
            }

            $demandStats = $this->urgencyService->getDemandStatistics();

            return response()->json([
                'success' => true,
                'urgency_breakdown' => $urgencyBreakdown,
                'demand_statistics' => $demandStats,
                'total_items' => $items->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}