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
        try {
            $request = RestockRequest::with([
                'userGudang',
                'userApproved',
                'userOrdered',
                'userTerminated',
                'details.barang.stok'
            ])->findOrFail($id);

            // Check if user owns this request (security)
            if ($request->id_user_gudang !== Auth::id()) {
                abort(403, 'Unauthorized access to this request.');
            }

            // Calculate additional stats
            $stats = [
                'total_items' => $request->details->count(),
                'total_cost' => $request->details->sum('estimasi_harga'),
                'approved_items' => $request->details->where('qty_approved', '>', 0)->count(),
                'additional_items' => $request->details->where('alasan_request', 'Additional item added by Owner during approval')->count(),
                'original_items' => $request->details->where('alasan_request', '!=', 'Additional item added by Owner during approval')->count()
            ];

            // Get workflow status
            $workflow = $this->getWorkflowStatus($request);

            return view('gudang.restock-request-detail', compact('request', 'stats', 'workflow'));

        } catch (\Exception $e) {
            return redirect()->route('gudang.restock-requests')
                            ->with('error', 'Request not found or access denied.');
        }
    }
    
    private function getWorkflowStatus($request)
    {
        $steps = [
            [
                'name' => 'Request Created',
                'status' => 'completed',
                'date' => $request->tanggal_request,
                'user' => $request->userGudang->name,
                'icon' => '📋'
            ],
            [
                'name' => 'Pending Approval',
                'status' => $request->status_request === 'Pending' ? 'current' : 'completed',
                'date' => null,
                'user' => null,
                'icon' => '⏳'
            ]
        ];

        if (in_array($request->status_request, ['Approved', 'Ordered', 'Completed'])) {
            $steps[] = [
                'name' => 'Approved by Owner',
                'status' => 'completed',
                'date' => $request->tanggal_approved,
                'user' => $request->userApproved ? $request->userApproved->name : 'Owner',
                'icon' => '✅'
            ];
        }

        if (in_array($request->status_request, ['Ordered', 'Completed'])) {
            $steps[] = [
                'name' => 'Ordered',
                'status' => 'completed',
                'date' => $request->tanggal_ordered,
                'user' => $request->userOrdered ? $request->userOrdered->name : 'Owner',
                'icon' => '📦'
            ];
        }

        if ($request->status_request === 'Completed') {
            $steps[] = [
                'name' => 'Completed',
                'status' => 'completed',
                'date' => $request->updated_at, // You might want to add a specific completion date field
                'user' => 'Warehouse Team',
                'icon' => '🎉'
            ];
        }

        if ($request->status_request === 'Rejected') {
            $steps[] = [
                'name' => 'Rejected',
                'status' => 'rejected',
                'date' => $request->tanggal_approved,
                'user' => $request->userApproved ? $request->userApproved->name : 'Owner',
                'icon' => '❌'
            ];
        }

        if ($request->status_request === 'Terminated') {
            $steps[] = [
                'name' => 'Force Terminated',
                'status' => 'terminated',
                'date' => $request->tanggal_terminated,
                'user' => $request->userTerminated ? $request->userTerminated->name : 'Owner',
                'icon' => '🛑'
            ];
        }

        return $steps;
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