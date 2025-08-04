<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use App\Models\Stok;
use App\Models\LogStok;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VerifikasiPermintaanController extends Controller
{
    /**
     * Display the verification page
     */
    public function index()
    {
        return view('gudang.verifikasi-permintaan');
    }

    /**
     * Get verification data for AJAX requests
     */
    public function getData(Request $request)
    {
        try {
            $status = $request->get('status', '');
            $search = $request->get('search', '');

            // Base query untuk semua transaksi yang perlu validasi
            $baseQuery = Transaksi::with(['kasir', 'detailTransaksi.barang.stok'])
                ->where('status_transaksi', 'Progress'); // Hanya transaksi yang masih progress

            // Apply search filter
            if (!empty($search)) {
                $baseQuery->where(function($query) use ($search) {
                    $query->where('nomor_transaksi', 'like', "%{$search}%")
                          ->orWhere('nama_customer', 'like', "%{$search}%")
                          ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            // Get all transactions untuk stats dan table
            $allTransactions = $baseQuery->get()->map(function($transaksi) {
                $details = $transaksi->detailTransaksi;
                return [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'tanggal_transaksi' => $transaksi->tanggal_transaksi,
                    'nama_customer' => $transaksi->nama_customer,
                    'kendaraan' => $transaksi->kendaraan,
                    'jenis_transaksi' => $transaksi->jenis_transaksi,
                    'status_transaksi' => $transaksi->status_transaksi,
                    'total_harga' => $transaksi->total_harga,
                    'kasir_name' => $transaksi->kasir->name,
                    'pending_count' => $details->where('status_permintaan', 'Pending')->count(),
                    'approved_count' => $details->where('status_permintaan', 'Approved')->count(),
                    'rejected_count' => $details->where('status_permintaan', 'Rejected')->count(),
                    'total_items' => $details->count()
                ];
            });

            // Filter by status if specified
            if (!empty($status)) {
                $allTransactions = $allTransactions->filter(function($transaction) use ($status) {
                    switch($status) {
                        case 'Pending':
                            return $transaction['pending_count'] > 0;
                        case 'Approved':
                            return $transaction['approved_count'] > 0 && $transaction['pending_count'] == 0;
                        case 'Rejected':
                            return $transaction['rejected_count'] > 0;
                        default:
                            return true;
                    }
                });
            }

            // Get pending requests (priority section)
            $pendingRequests = $allTransactions->filter(function($transaction) {
                return $transaction['pending_count'] > 0;
            })->values();

            // Calculate stats
            $stats = [
                'pending' => DetailTransaksi::whereHas('transaksi', function($query) {
                    $query->where('status_transaksi', 'Progress');
                })->where('status_permintaan', 'Pending')->count(),
                
                'approved' => DetailTransaksi::whereHas('transaksi', function($query) {
                    $query->where('status_transaksi', 'Progress');
                })->where('status_permintaan', 'Approved')->count(),
                
                'rejected' => DetailTransaksi::whereHas('transaksi', function($query) {
                    $query->where('status_transaksi', 'Progress');
                })->where('status_permintaan', 'Rejected')->count(),
                
                'total' => $allTransactions->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'pending_requests' => $pendingRequests,
                'all_transactions' => $allTransactions->values()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading verification data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed transaction for validation modal
     */
    public function getTransactionDetail($id)
    {
        try {
            $transaksi = Transaksi::with(['kasir', 'detailTransaksi.barang.stok'])
                ->where('id_transaksi', $id)
                ->firstOrFail();

            $items = $transaksi->detailTransaksi->map(function($detail) {
                $barang = $detail->barang;
                $stok = $barang->stok;
                
                return [
                    'id_detail' => $detail->id_detail,
                    'kode_barang' => $barang->kode_barang,
                    'nama_barang' => $barang->nama_barang,
                    'satuan' => $barang->satuan,
                    'qty' => $detail->qty,
                    'harga_satuan' => $detail->harga_satuan,
                    'subtotal' => $detail->subtotal,
                    'status_permintaan' => $detail->status_permintaan,
                    'stok_tersedia' => $stok ? $stok->jumlah_stok : 0,
                    'reorder_point' => $barang->reorder_point,
                    'can_approve' => $detail->status_permintaan === 'Pending' && 
                                   $stok && $stok->jumlah_stok >= $detail->qty
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'tanggal_transaksi' => $transaksi->tanggal_transaksi,
                    'nama_customer' => $transaksi->nama_customer,
                    'kendaraan' => $transaksi->kendaraan,
                    'jenis_transaksi' => $transaksi->jenis_transaksi,
                    'status_transaksi' => $transaksi->status_transaksi,
                    'total_harga' => $transaksi->total_harga,
                    'kasir_name' => $transaksi->kasir->name,
                    'items' => $items
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate individual item (approve/reject)
     */
    public function validateItem(Request $request)
    {
        $request->validate([
            'detail_id' => 'required|exists:detail_transaksi,id_detail',
            'action' => 'required|in:approve,reject'
        ]);

        try {
            DB::beginTransaction();

            $detail = DetailTransaksi::with(['barang.stok', 'transaksi'])
                ->findOrFail($request->detail_id);

            // Check if item is still pending
            if ($detail->status_permintaan !== 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Item sudah divalidasi sebelumnya'
                ], 400);
            }

            if ($request->action === 'approve') {
                // Check stock availability
                $stok = $detail->barang->stok;
                if (!$stok || $stok->jumlah_stok < $detail->qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak mencukupi. Tersedia: " . ($stok ? $stok->jumlah_stok : 0) . ", Diminta: " . $detail->qty
                    ], 400);
                }

                // Approve the item
                $detail->approve();
                
                $message = "Item {$detail->barang->nama_barang} berhasil di-approve";
                
            } else {
                // Reject the item
                $detail->reject();
                
                $message = "Item {$detail->barang->nama_barang} berhasil di-reject";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_status' => $detail->status_permintaan
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error validating item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk approve items with sufficient stock
     */
    public function bulkApprove(Request $request)
    {
        try {
            DB::beginTransaction();

            $status = $request->get('status', '');
            $search = $request->get('search', '');

            // Get pending items yang bisa di-approve (stok cukup)
            $query = DetailTransaksi::with(['barang.stok', 'transaksi'])
                ->whereHas('transaksi', function($q) {
                    $q->where('status_transaksi', 'Progress');
                })
                ->where('status_permintaan', 'Pending');

            // Apply same filters as main view
            if (!empty($search)) {
                $query->whereHas('transaksi', function($q) use ($search) {
                    $q->where('nomor_transaksi', 'like', "%{$search}%")
                      ->orWhere('nama_customer', 'like', "%{$search}%")
                      ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            $pendingItems = $query->get();

            $approvedCount = 0;
            $insufficientStock = [];

            foreach ($pendingItems as $detail) {
                $stok = $detail->barang->stok;
                
                if ($stok && $stok->jumlah_stok >= $detail->qty) {
                    $detail->approve();
                    $approvedCount++;
                } else {
                    $insufficientStock[] = $detail->barang->nama_barang;
                }
            }

            // Log bulk activity
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'approved_count' => $approvedCount,
                    'insufficient_stock_items' => $insufficientStock,
                    'search_filter' => $search,
                    'status_filter' => $status
                ])
                ->log("Bulk approved {$approvedCount} items");

            DB::commit();

            $message = "Berhasil approve {$approvedCount} items";
            if (count($insufficientStock) > 0) {
                $message .= ". " . count($insufficientStock) . " items tidak bisa di-approve karena stok tidak cukup";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'approved_count' => $approvedCount,
                'insufficient_stock_count' => count($insufficientStock),
                'insufficient_stock_items' => $insufficientStock
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error bulk approving: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get validation statistics for dashboard
     */
    public function getValidationStats()
    {
        try {
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            $stats = [
                'today' => [
                    'validated' => DetailTransaksi::whereDate('updated_at', $today)
                        ->whereIn('status_permintaan', ['Approved', 'Rejected'])
                        ->count(),
                    'approved' => DetailTransaksi::whereDate('updated_at', $today)
                        ->where('status_permintaan', 'Approved')
                        ->count(),
                    'rejected' => DetailTransaksi::whereDate('updated_at', $today)
                        ->where('status_permintaan', 'Rejected')
                        ->count()
                ],
                'this_week' => [
                    'validated' => DetailTransaksi::where('updated_at', '>=', $thisWeek)
                        ->whereIn('status_permintaan', ['Approved', 'Rejected'])
                        ->count(),
                    'approved' => DetailTransaksi::where('updated_at', '>=', $thisWeek)
                        ->where('status_permintaan', 'Approved')
                        ->count(),
                    'rejected' => DetailTransaksi::where('updated_at', '>=', $thisWeek)
                        ->where('status_permintaan', 'Rejected')
                        ->count()
                ],
                'this_month' => [
                    'validated' => DetailTransaksi::where('updated_at', '>=', $thisMonth)
                        ->whereIn('status_permintaan', ['Approved', 'Rejected'])
                        ->count(),
                    'approved' => DetailTransaksi::where('updated_at', '>=', $thisMonth)
                        ->where('status_permintaan', 'Approved')
                        ->count(),
                    'rejected' => DetailTransaksi::where('updated_at', '>=', $thisMonth)
                        ->where('status_permintaan', 'Rejected')
                        ->count()
                ],
                'pending_urgent' => DetailTransaksi::with(['barang.stok'])
                    ->whereHas('transaksi', function($q) {
                        $q->where('status_transaksi', 'Progress')
                          ->where('created_at', '<=', Carbon::now()->subHours(2)); // Older than 2 hours
                    })
                    ->where('status_permintaan', 'Pending')
                    ->whereHas('barang.stok', function($q) {
                        $q->whereRaw('jumlah_stok <= reorder_point'); // Low stock items
                    })
                    ->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading validation stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent validation activities for monitoring
     */
    public function getRecentActivities()
    {
        try {
            $activities = DetailTransaksi::with(['transaksi.kasir', 'barang'])
                ->whereIn('status_permintaan', ['Approved', 'Rejected'])
                ->whereDate('updated_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function($detail) {
                    return [
                        'id_detail' => $detail->id_detail,
                        'nomor_transaksi' => $detail->transaksi->nomor_transaksi,
                        'nama_barang' => $detail->barang->nama_barang,
                        'qty' => $detail->qty,
                        'status_permintaan' => $detail->status_permintaan,
                        'kasir_name' => $detail->transaksi->kasir->name,
                        'customer' => $detail->transaksi->nama_customer,
                        'validated_at' => $detail->updated_at,
                        'time_ago' => $detail->updated_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading recent activities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export validation report
     */
    public function exportValidationReport(Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

            $data = DetailTransaksi::with(['transaksi.kasir', 'barang'])
                ->whereHas('transaksi', function($q) {
                    $q->where('status_transaksi', 'Progress');
                })
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->whereIn('status_permintaan', ['Approved', 'Rejected'])
                ->orderBy('updated_at', 'desc')
                ->get();

            // Generate CSV or PDF export logic here
            // This is a placeholder for export functionality

            return response()->json([
                'success' => true,
                'message' => 'Export functionality will be implemented',
                'record_count' => $data->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk approve items for specific transaction
     */
    public function bulkApproveTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transaksi,id_transaksi'
        ]);

        try {
            DB::beginTransaction();

            // Get pending items for this transaction that can be approved
            $pendingItems = DetailTransaksi::with(['barang.stok'])
                ->where('id_transaksi', $request->transaction_id)
                ->where('status_permintaan', 'Pending')
                ->get();

            $approvedCount = 0;
            $insufficientStock = [];

            foreach ($pendingItems as $detail) {
                $stok = $detail->barang->stok;
                
                if ($stok && $stok->jumlah_stok >= $detail->qty) {
                    $detail->approve();
                    $approvedCount++;
                } else {
                    $insufficientStock[] = $detail->barang->nama_barang;
                }
            }

            DB::commit();

            $message = "Berhasil approve {$approvedCount} items";
            if (count($insufficientStock) > 0) {
                $message .= ". " . count($insufficientStock) . " items tidak bisa di-approve karena stok tidak cukup";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'approved_count' => $approvedCount,
                'insufficient_stock_count' => count($insufficientStock)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error bulk approving transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk reject items for specific transaction
     */
    public function bulkRejectTransaction(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transaksi,id_transaksi'
        ]);

        try {
            DB::beginTransaction();

            // Get pending items for this transaction
            $pendingItems = DetailTransaksi::where('id_transaksi', $request->transaction_id)
                ->where('status_permintaan', 'Pending')
                ->get();

            $rejectedCount = 0;

            foreach ($pendingItems as $detail) {
                $detail->reject();
                $rejectedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil reject {$rejectedCount} items",
                'rejected_count' => $rejectedCount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error bulk rejecting transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}