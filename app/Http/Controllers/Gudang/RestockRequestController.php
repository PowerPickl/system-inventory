<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\RestockRequest;
use App\Models\RestockRequestDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestockRequestController extends Controller
{
    /**
     * Display restock requests list
     */
    public function index()
    {
        $requests = RestockRequest::with(['userGudang', 'userApproved', 'details.barang'])
            ->where('id_user_gudang', Auth::id())
            ->orderBy('tanggal_request', 'desc')
            ->paginate(15);

        // Statistics
        $stats = [
            'total' => RestockRequest::where('id_user_gudang', Auth::id())->count(),
            'pending' => RestockRequest::where('id_user_gudang', Auth::id())->pending()->count(),
            'approved' => RestockRequest::where('id_user_gudang', Auth::id())->approved()->count(),
            'completed' => RestockRequest::where('id_user_gudang', Auth::id())->completed()->count(),
            'rejected' => RestockRequest::where('id_user_gudang', Auth::id())->where('status_request', 'Rejected')->count()
        ];

        return view('gudang.restock-requests', compact('requests', 'stats'));
    }

    /**
     * Show specific restock request details
     */
    public function show($id)
    {
        $request = RestockRequest::with([
            'userGudang', 
            'userApproved', 
            'details.barang.stok',
            'barangMasuk.details'
        ])
        ->where('id_user_gudang', Auth::id())
        ->findOrFail($id);

        // Calculate totals
        $totals = [
            'total_items' => $request->details->count(),
            'total_qty_requested' => $request->details->sum('qty_request'),
            'total_qty_approved' => $request->details->sum('qty_approved'),
            'total_estimated_cost' => $request->details->sum('estimasi_harga'),
            'items_received' => $request->barangMasuk->sum(function($barangMasuk) {
                return $barangMasuk->details->count();
            })
        ];

        return view('gudang.restock-request-detail', compact('request', 'totals'));
    }

    /**
     * Cancel pending request
     */
    public function cancel($id)
    {
        try {
            $request = RestockRequest::where('id_user_gudang', Auth::id())
                ->where('status_request', 'Pending')
                ->findOrFail($id);

            $request->update([
                'status_request' => 'Cancelled',
                'catatan_approval' => 'Cancelled by warehouse staff'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Request {$request->nomor_request} has been cancelled"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get request status for AJAX updates
     */
    public function getRequestStatus($id)
    {
        try {
            $request = RestockRequest::where('id_user_gudang', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'status' => $request->status_request,
                'updated_at' => $request->updated_at->format('Y-m-d H:i:s'),
                'approved_by' => $request->userApproved ? $request->userApproved->name : null,
                'approval_notes' => $request->catatan_approval
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get requests summary for dashboard
     */
    public function getSummary()
    {
        $userId = Auth::id();
        
        $summary = [
            'recent_requests' => RestockRequest::with('details.barang')
                ->where('id_user_gudang', $userId)
                ->orderBy('tanggal_request', 'desc')
                ->limit(5)
                ->get(),
            
            'pending_count' => RestockRequest::where('id_user_gudang', $userId)
                ->pending()
                ->count(),
                
            'this_month' => RestockRequest::where('id_user_gudang', $userId)
                ->whereMonth('tanggal_request', now()->month)
                ->whereYear('tanggal_request', now()->year)
                ->count(),
                
            'approved_waiting_delivery' => RestockRequest::where('id_user_gudang', $userId)
                ->where('status_request', 'Approved')
                ->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}