<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\RestockRequest;
use App\Models\RestockRequestDetail;
use App\Models\Barang;
use App\Services\UrgencyCalculationService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestockApprovalController extends Controller
{

    protected $urgencyService;

    public function __construct(UrgencyCalculationService $urgencyService)
    {
        $this->urgencyService = $urgencyService;
    }
    /**
     * Display pending restock requests for approval
     */
    public function index()
    {
        // Get pending requests
        $pendingRequests = RestockRequest::with(['userGudang', 'details.barang.stok', 'details.barang.kategori'])
                            ->where('status_request', 'Pending')
                            ->orderBy('tanggal_request', 'desc')
                            ->get()
                            ->map(function ($request) {
                                // Calculate urgency for this request
                                $barangItems = $request->details->pluck('barang');
                                $bulkUrgency = $this->urgencyService->calculateBulkUrgency($barangItems);
                                
                                $request->urgency_data = $bulkUrgency;
                                $request->priority_score = $bulkUrgency['avg_urgency_score'];
                                $request->primary_urgency = $bulkUrgency['primary_urgency'];
                                $request->urgent_items_count = $bulkUrgency['urgent_items_count'];
                                
                                return $request;
                            })
                            ->sortByDesc('priority_score')
                            ->values();

        

        // Get approved requests for "Orders to Process" section
        $approvedRequests = RestockRequest::with(['userGudang', 'userApproved', 'details.barang'])
                                        ->where('status_request', 'Approved')
                                        ->orderBy('tanggal_approved', 'desc')
                                        ->get();

        // NEW: Get ordered requests (waiting for warehouse completion)
        $orderedRequests = RestockRequest::with(['userGudang', 'userApproved', 'userOrdered', 'details.barang'])
                                        ->where('status_request', 'Ordered')
                                        ->orderBy('tanggal_ordered', 'desc')
                                        ->get();

        // Get all requests for comprehensive history
        $allRequests = RestockRequest::with(['userGudang', 'userApproved', 'details.barang'])
                                    ->orderBy('tanggal_request', 'desc')
                                    ->paginate(10);

        // Enhanced statistics
        $stats = [
            'total_pending' => $pendingRequests->count(),
            'total_approved' => $approvedRequests->count(),  // Ready to order
            'total_ordered' => $orderedRequests->count(),    // Waiting for warehouse
            'total_this_month' => RestockRequest::whereMonth('tanggal_request', now()->month)
                                            ->whereYear('tanggal_request', now()->year)
                                            ->count(),
            'approved_this_month' => RestockRequest::where('status_request', 'Approved')
                                                ->whereMonth('tanggal_approved', now()->month)
                                                ->whereYear('tanggal_approved', now()->year)
                                                ->count(),
            'total_estimated_cost' => $pendingRequests->sum(function($request) {
                return $request->details->sum('estimasi_harga');
            }),
            'urgent_items' => $pendingRequests->sum(function($request) {
                return $request->details->filter(function($detail) {
                    return $detail->barang->stok && $detail->barang->stok->jumlah_stok <= 0;
                })->count();
            }),

            // NEW URGENCY STATS
            'urgent_requests' => $pendingRequests->where('primary_urgency', 'URGENT')->count(),
            'high_priority_requests' => $pendingRequests->where('primary_urgency', 'HIGH')->count(),
            'medium_priority_requests' => $pendingRequests->where('primary_urgency', 'MEDIUM')->count(),
            'total_urgent_items' => $pendingRequests->sum('urgent_items_count'),

            // URGENCY BREAKDOWN
            'urgency_breakdown' => [
                'URGENT' => $pendingRequests->where('primary_urgency', 'URGENT')->count(),
                'HIGH' => $pendingRequests->where('primary_urgency', 'HIGH')->count(),
                'MEDIUM' => $pendingRequests->where('primary_urgency', 'MEDIUM')->count(),
                'LOW' => $pendingRequests->where('primary_urgency', 'LOW')->count(),
                'NORMAL' => $pendingRequests->where('primary_urgency', 'NORMAL')->count(),
            ]

        ];

        


        return view('owner.restock-approval', compact('pendingRequests', 'approvedRequests', 'orderedRequests', 'allRequests', 'stats'));
    }

    /**
     * Get request details for AJAX modal
     */
    public function getRequestDetails($id)
    {
        try {
            $request = RestockRequest::with([
                'userGudang',
                'details.barang.stok'
            ])->findOrFail($id);

            $details = $request->details->map(function($detail) {
            $urgencyData = $this->urgencyService->calculateUrgencyLevel($detail->barang);
                return [
                    'id' => $detail->id_request_detail,
                    'barang' => [
                        'id' => $detail->barang->id_barang,
                        'nama' => $detail->barang->nama_barang,
                        'kode' => $detail->barang->kode_barang,
                        'current_stock' => $detail->barang->stok ? $detail->barang->stok->jumlah_stok : 0,
                        'harga_beli' => $detail->barang->harga_beli,
                        'satuan' => $detail->barang->satuan
                    ],
                    'qty_request' => $detail->qty_request,
                    'qty_approved' => $detail->qty_approved ?? $detail->qty_request,
                    'estimasi_harga' => $detail->estimasi_harga,
                    'alasan_request' => $detail->alasan_request,
                    'item_type' => 'requested',

                    'urgency_data' => $urgencyData,
                    'urgency_level' => $urgencyData['final_urgency'],
                    'urgency_score' => $urgencyData['urgency_score'],
                    'auto_reason' => $urgencyData['auto_reason'],
                    'demand_level' => $urgencyData['demand_level'],
                    'days_until_stockout' => $urgencyData['days_until_stockout']
                ];
            });

            return response()->json([
                'success' => true,
                'request' => [
                    'id' => $request->id_request,
                    'nomor_request' => $request->nomor_request,
                    'tanggal_request' => $request->tanggal_request->format('Y-m-d H:i:s'),
                    'status' => $request->status_request,
                    'catatan_request' => $request->catatan_request,
                    'user_gudang' => $request->userGudang->name
                ],
                'details' => $details,
                'totals' => [
                    'total_items' => $details->count(),
                    'total_estimated' => $details->sum('estimasi_harga')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get request details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load request details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search items for additional items feature - Enhanced version
     */
    public function searchItems(Request $request)
    {
        try {
            $query = $request->get('q');
            $requestId = $request->get('request_id');
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'message' => 'Please enter at least 2 characters'
                ]);
            }

            $itemsQuery = Barang::with('stok')
                            ->where(function($q) use ($query) {
                                $q->where('nama_barang', 'like', "%{$query}%")
                                    ->orWhere('kode_barang', 'like', "%{$query}%");
                            });

            // Exclude items already in the request
            if ($requestId) {
                $existingItemIds = RestockRequestDetail::where('id_request', $requestId)
                                                    ->pluck('id_barang')
                                                    ->toArray();
                if (!empty($existingItemIds)) {
                    $itemsQuery->whereNotIn('id_barang', $existingItemIds);
                }
            }

            $items = $itemsQuery->orderBy('nama_barang')
                            ->limit(20)
                            ->get()
                            ->map(function($item) {
                                return [
                                    'id' => $item->id_barang,
                                    'nama' => $item->nama_barang,
                                    'kode' => $item->kode_barang,
                                    'satuan' => $item->satuan,
                                    'harga_beli' => $item->harga_beli,
                                    'current_stock' => $item->stok ? $item->stok->jumlah_stok : 0,
                                    'reorder_point' => $item->reorder_point ?? 0,
                                    'status_stok' => $item->status_stok ?? 'Normal'
                                ];
                            });
                            
            return response()->json([
                'success' => true,
                'items' => $items,
                'count' => $items->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Search items failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve restock request with modifications and additional items - FIXED VERSION
     */
    public function approve(Request $request, $id)
    {
        // Enhanced validation with more detailed rules
        $request->validate([
            'details' => 'required|array',
            'details.*.qty_approved' => 'required|integer|min:0',
            'additional_items' => 'nullable|array',
            'additional_items.*.id_barang' => 'nullable|exists:barang,id_barang',
            'additional_items.*.qty_approved' => 'nullable|integer|min:1',
            'catatan_approval' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $restockRequest = RestockRequest::findOrFail($id);

            if ($restockRequest->status_request !== 'Pending') {
                throw new \Exception('Request is no longer pending approval');
            }

            // DEBUG: Log incoming request data
            Log::info("APPROVAL DEBUG - Incoming data", [
                'request_id' => $id,
                'details_count' => count($request->details ?? []),
                'additional_items_raw' => $request->additional_items,
                'additional_items_count' => count($request->additional_items ?? []),
                'full_request_data' => $request->all()
            ]);

            // Update existing details
            foreach ($request->details as $detailId => $data) {
                $detail = RestockRequestDetail::find($detailId);
                if ($detail && $detail->id_request == $restockRequest->id_request) {
                    $detail->qty_approved = $data['qty_approved'];
                    $detail->estimasi_harga = $detail->barang->harga_beli * $data['qty_approved'];
                    $detail->save();
                    
                    Log::info("Updated existing detail", [
                        'detail_id' => $detail->id_request_detail,
                        'item' => $detail->barang->nama_barang,
                        'qty_approved' => $data['qty_approved']
                    ]);
                }
            }

            // Add additional items - ENHANCED DEBUGGING
            $additionalItemsCount = 0;
            if ($request->additional_items && is_array($request->additional_items)) {
                Log::info("Processing additional items", [
                    'items_array' => $request->additional_items,
                    'array_count' => count($request->additional_items)
                ]);

                foreach ($request->additional_items as $index => $item) {
                    Log::info("Processing additional item #{$index}", [
                        'item_data' => $item,
                        'has_id_barang' => isset($item['id_barang']),
                        'has_qty' => isset($item['qty_approved']),
                        'id_barang_value' => $item['id_barang'] ?? 'NOT_SET',
                        'qty_value' => $item['qty_approved'] ?? 'NOT_SET'
                    ]);

                    // More robust validation
                    if (isset($item['id_barang']) && isset($item['qty_approved']) && 
                        !empty($item['id_barang']) && is_numeric($item['qty_approved']) && $item['qty_approved'] > 0) {
                        
                        $barang = Barang::find($item['id_barang']);
                        if ($barang) {
                            $newDetail = RestockRequestDetail::create([
                                'id_request' => $restockRequest->id_request,
                                'id_barang' => $item['id_barang'],
                                'qty_request' => $item['qty_approved'],
                                'qty_approved' => $item['qty_approved'],
                                'estimasi_harga' => $barang->harga_beli * $item['qty_approved'],
                                'alasan_request' => 'Additional item added by Owner during approval'
                            ]);
                            
                            $additionalItemsCount++;
                            
                            Log::info("✅ SUCCESSFULLY created additional item", [
                                'detail_id' => $newDetail->id_request_detail,
                                'barang_id' => $item['id_barang'],
                                'barang_name' => $barang->nama_barang,
                                'qty' => $item['qty_approved'],
                                'cost' => $newDetail->estimasi_harga
                            ]);
                        } else {
                            Log::error("❌ Barang not found", ['id_barang' => $item['id_barang']]);
                        }
                    } else {
                        Log::warning("❌ Invalid additional item data", [
                            'index' => $index,
                            'item' => $item,
                            'validation_fails' => [
                                'missing_id_barang' => !isset($item['id_barang']),
                                'empty_id_barang' => empty($item['id_barang'] ?? null),
                                'missing_qty' => !isset($item['qty_approved']),
                                'invalid_qty' => !is_numeric($item['qty_approved'] ?? null) || ($item['qty_approved'] ?? 0) <= 0
                            ]
                        ]);
                    }
                }
            } else {
                Log::info("No additional items to process", [
                    'additional_items_exists' => isset($request->additional_items),
                    'is_array' => is_array($request->additional_items ?? null)
                ]);
            }

            // Prepare approval message
            $approvalMessage = $request->catatan_approval ?: '';
            if ($additionalItemsCount > 0) {
                $additionalMessage = "Added {$additionalItemsCount} strategic items by Owner.";
                $approvalMessage = $approvalMessage ? $approvalMessage . ' | ' . $additionalMessage : $additionalMessage;
            }
            
            // Approve the request
            $restockRequest->approve(Auth::id(), $approvalMessage);

            // Get fresh data for response
            $approvedItems = $restockRequest->fresh(['details.barang'])->details()
                ->with('barang')
                ->where('qty_approved', '>', 0)
                ->get()
                ->map(function($detail) {
                    return [
                        'nama_barang' => $detail->barang->nama_barang,
                        'kode_barang' => $detail->barang->kode_barang,
                        'qty_approved' => $detail->qty_approved,
                        'satuan' => $detail->barang->satuan,
                        'harga_beli' => $detail->barang->harga_beli,
                        'total_cost' => $detail->estimasi_harga,
                        'is_additional' => $detail->alasan_request == 'Additional item added by Owner during approval'
                    ];
                });

            $totalCost = $approvedItems->sum('total_cost');

            DB::commit();

            Log::info("✅ APPROVAL COMPLETED", [
                'request_number' => $restockRequest->nomor_request,
                'total_items' => $approvedItems->count(),
                'additional_items_added' => $additionalItemsCount,
                'total_cost' => $totalCost
            ]);

            return response()->json([
                'success' => true,
                'message' => "Request {$restockRequest->nomor_request} approved successfully!" . 
                        ($additionalItemsCount > 0 ? " ({$additionalItemsCount} additional items added)" : ''),
                'debug_info' => [
                    'additional_items_processed' => $additionalItemsCount,
                    'total_items_final' => $approvedItems->count()
                ],
                'order_summary' => [
                    'request_number' => $restockRequest->nomor_request,
                    'items' => $approvedItems,
                    'total_items' => $approvedItems->count(),
                    'total_cost' => $totalCost,
                    'additional_items_count' => $additionalItemsCount,
                    'approved_by' => Auth::user()->name,
                    'approved_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('❌ APPROVAL FAILED', [
                'request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Failed to approve request: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Reject restock request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_approval' => 'required|string|max:500'
        ]);

        try {
            $restockRequest = RestockRequest::findOrFail($id);

            if ($restockRequest->status_request !== 'Pending') {
                throw new \Exception('Request is no longer pending approval');
            }

            $restockRequest->reject(Auth::id(), $request->catatan_approval);

            return response()->json([
                'success' => true,
                'message' => "Restock request {$restockRequest->nomor_request} has been rejected"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick approve with default quantities (no additional items)
     */
    public function quickApprove($id)
    {
        try {
            DB::beginTransaction();

            $restockRequest = RestockRequest::with('details.barang')->findOrFail($id);

            if ($restockRequest->status_request !== 'Pending') {
                throw new \Exception('Request is no longer pending approval');
            }

            // Auto-approve all items with requested quantities
            foreach ($restockRequest->details as $detail) {
                $detail->update([
                    'qty_approved' => $detail->qty_request,
                    'estimasi_harga' => $detail->barang->harga_beli * $detail->qty_request
                ]);
            }

            // Approve the request
            $restockRequest->approve(Auth::id(), 'Quick approval - all items approved as requested');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Request {$restockRequest->nomor_request} quickly approved with {$restockRequest->details->count()} items"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to quick approve: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark request as ordered (update status to completed) - UPDATED
     */
    public function markAsOrdered($id)
    {
        try {
            $restockRequest = RestockRequest::findOrFail($id);
            
            if ($restockRequest->status_request !== 'Approved') {
                throw new \Exception('Only approved requests can be marked as ordered');
            }
            
            // FIXED: Set to 'Ordered' instead of 'Completed'
            $restockRequest->update([
                'status_request' => 'Ordered',  // 👈 CHANGE: Was 'Completed', now 'Ordered'
                'tanggal_ordered' => now(),
                'id_user_ordered' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Request marked as ordered successfully. Items will be received in warehouse.",
                'request_number' => $restockRequest->nomor_request,
                'status' => 'Ordered'  // 👈 Add status info
            ]);
            
        } catch (\Exception $e) {
            Log::error('Mark as ordered failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export order list for supplier communication
     */
    public function exportOrderList(Request $request, $id)
    {
        try {
            $restockRequest = RestockRequest::with(['details.barang', 'userApproved'])
                ->findOrFail($id);
                
            if ($restockRequest->status_request !== 'Approved') {
                throw new \Exception('Only approved requests can be exported');
            }
            
            $approvedDetails = $restockRequest->details()
                ->with('barang')
                ->where('qty_approved', '>', 0)
                ->get();
                
            if ($request->get('format') === 'csv') {
                return $this->exportAsCSV($restockRequest, $approvedDetails);
            } else {
                return $this->exportAsPrintable($restockRequest, $approvedDetails);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportAsCSV($request, $details)
    {
        $filename = "order-list-{$request->nomor_request}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\""
        ];
        
        $callback = function() use ($request, $details) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Order List for Request: ' . $request->nomor_request,
                'Approved by: ' . $request->userApproved->name,
                'Date: ' . $request->tanggal_approved->format('Y-m-d H:i:s')
            ]);
            fputcsv($file, []);
            fputcsv($file, ['Item Code', 'Item Name', 'Quantity', 'Unit', 'Unit Price', 'Total Cost', 'Type']);
            
            foreach ($details as $detail) {
                $type = $detail->alasan_request == 'Additional item added by Owner during approval' 
                    ? 'Owner Added' : 'Original Request';
                    
                fputcsv($file, [
                    $detail->barang->kode_barang,
                    $detail->barang->nama_barang,
                    $detail->qty_approved,
                    $detail->barang->satuan,
                    $detail->barang->harga_beli,
                    $detail->estimasi_harga,
                    $type
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL:', $details->sum('estimasi_harga')]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportAsPrintable($request, $details)
    {
        $totalCost = $details->sum('estimasi_harga');
        $originalCount = $details->where('alasan_request', '!=', 'Additional item added by Owner during approval')->count();
        $additionalCount = $details->where('alasan_request', 'Additional item added by Owner during approval')->count();
        
        $html = view('owner.order-list-print', compact(
            'request', 'details', 'totalCost', 'originalCount', 'additionalCount'
        ))->render();
        
        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * Get approval statistics for dashboard
     */
    public function getApprovalStats()
    {
        $stats = [
            'pending_count' => RestockRequest::where('status_request', 'Pending')->count(),
            'today_requests' => RestockRequest::whereDate('tanggal_request', today())->count(),
            'this_week_approved' => RestockRequest::where('status_request', 'Approved')
                                                 ->whereBetween('tanggal_approved', [
                                                     now()->startOfWeek(),
                                                     now()->endOfWeek()
                                                 ])->count(),
            'urgent_requests' => RestockRequest::with('details.barang.stok')
                                              ->where('status_request', 'Pending')
                                              ->get()
                                              ->filter(function($request) {
                                                  return $request->details->filter(function($detail) {
                                                      return $detail->barang->stok && $detail->barang->stok->jumlah_stok <= 0;
                                                  })->count() > 0;
                                              })->count()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Force terminate an approved request (Emergency use) - FIXED
     */
    public function forceTerminate(Request $request, $id)
    {
        $request->validate([
            'termination_reason' => 'required|string|min:10|max:500',
            'confirm_termination' => 'required|boolean|accepted'
        ]);

        try {
            DB::beginTransaction();

            $restockRequest = RestockRequest::findOrFail($id);

            // Only allow termination of approved requests
            if ($restockRequest->status_request !== 'Approved') {
                throw new \Exception('Only approved requests can be force terminated');
            }

            // UPDATE: Use the terminate method from updated model
            $restockRequest->terminate(Auth::id(), $request->termination_reason);

            // Log the termination for audit
            Log::warning("Request force terminated", [
                'request_number' => $restockRequest->nomor_request,
                'terminated_by' => Auth::user()->name,
                'reason' => $request->termination_reason,
                'original_cost' => $restockRequest->total_estimasi_biaya  // 👈 CHANGE: use accessor
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Request {$restockRequest->nomor_request} has been force terminated",
                'redirect_needed' => true
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Force termination failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to terminate request: ' . $e->getMessage()
            ], 500);
        }
    }
    
}