<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use App\Models\RestockRequest;
use App\Models\Barang;
use App\Models\Stok;
use App\Services\EOQCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarangMasukController extends Controller
{
    protected $eoqService;

    public function __construct(EOQCalculationService $eoqService)
    {
        $this->eoqService = $eoqService;
    }

    /**
     * Display barang masuk page
     */
    public function index()
    {
        return view('gudang.barang-masuk');
    }

    /**
     * Get pending restock requests
     */
    public function getPendingRequests()
    {
        try {
            $requests = RestockRequest::with(['details.barang'])
                ->whereIn('status_request', ['Ordered', 'Approved'])
                ->orderBy('tanggal_request', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id_request' => $request->id_request,
                        'nomor_request' => $request->nomor_request,
                        'tanggal_request' => $request->tanggal_request->format('Y-m-d'),
                        'total_items' => $request->details->count(),
                        'status_request' => $request->status_request,
                        'estimated_cost' => $request->details->sum('estimasi_harga')
                    ];
                });

            return response()->json([
                'success' => true,
                'requests' => $requests
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting pending requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'requests' => []
            ], 500);
        }
    }

    /**
     * Search specific restock request
     */
    public function searchRequest($requestNumber)
    {
        try {
            \Log::info('Searching for request: ' . $requestNumber);
            
            $request = RestockRequest::with([
                'details.barang.stok',
                'userGudang',
                'userApproved',
                'userOrdered'
            ])
            ->where('nomor_request', $requestNumber)
            ->whereIn('status_request', ['Ordered', 'Approved'])
            ->first();

            if (!$request) {
                \Log::warning('Request not found: ' . $requestNumber);
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found or not ready for processing. Only Approved/Ordered requests can be processed.'
                ]);
            }

            \Log::info('Request found: ' . $request->nomor_request . ' with status: ' . $request->status_request);

            // Format request data for frontend
            $requestData = [
                'id_request' => $request->id_request,
                'nomor_request' => $request->nomor_request,
                'tanggal_request' => $request->tanggal_request,
                'status_request' => $request->status_request,
                'supplier' => $request->supplier ?? '',
                'catatan_request' => $request->catatan_request ?? '',
                'total_estimated_cost' => $request->details->sum('estimasi_harga'),
                'details' => $request->details->map(function ($detail) {
                    return [
                        'id_request_detail' => $detail->id_request_detail,
                        'id_barang' => $detail->id_barang,
                        'qty_request' => $detail->qty_request,
                        'qty_approved' => $detail->qty_approved,
                        'estimasi_harga' => $detail->estimasi_harga,
                        'alasan_request' => $detail->alasan_request,
                        'barang' => [
                            'id_barang' => $detail->barang->id_barang,
                            'kode_barang' => $detail->barang->kode_barang,
                            'nama_barang' => $detail->barang->nama_barang,
                            'satuan' => $detail->barang->satuan,
                            'harga_beli' => $detail->barang->harga_beli,
                            'reorder_point' => $detail->barang->reorder_point,
                            'stok' => [
                                'jumlah_stok' => $detail->barang->stok ? $detail->barang->stok->jumlah_stok : 0
                            ]
                        ]
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'request' => $requestData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error searching request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error searching request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search items for direct entry - THIS WAS MISSING!
     */
    public function searchItems(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'items' => []
                ]);
            }

            $items = Barang::with('stok')
                ->where(function($q) use ($query) {
                    $q->where('nama_barang', 'like', "%{$query}%")
                      ->orWhere('kode_barang', 'like', "%{$query}%");
                })
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'id_barang' => $item->id_barang,
                        'kode_barang' => $item->kode_barang,
                        'nama_barang' => $item->nama_barang,
                        'satuan' => $item->satuan,
                        'harga_beli' => $item->harga_beli,
                        'reorder_point' => $item->reorder_point,
                        'stok' => [
                            'jumlah_stok' => $item->stok ? $item->stok->jumlah_stok : 0
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'items' => $items
            ]);

        } catch (\Exception $e) {
            \Log::error('Error searching items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'items' => []
            ], 500);
        }
    }

    /**
     * Process incoming stock from restock request
     */
    public function processRequest(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:restock_request,id_request',
            'supplier' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'delivery_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.qty_received' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        
        try {
            // Get restock request
            $restockRequest = RestockRequest::findOrFail($request->request_id);
            
            if (!in_array($restockRequest->status_request, ['Ordered', 'Approved'])) {
                throw new \Exception('Request is not ready for processing. Current status: ' . $restockRequest->status_request);
            }

            \Log::info('Processing request: ' . $restockRequest->nomor_request . ' with status: ' . $restockRequest->status_request);

            // Create barang masuk record
            $barangMasuk = BarangMasuk::create([
                'id_request' => $restockRequest->id_request,
                'tanggal_masuk' => Carbon::parse($request->delivery_date),
                'id_user_gudang' => Auth::id(),
                'supplier' => $request->supplier,
                'nomor_invoice' => $request->invoice_number,
                'jenis_masuk' => 'Restock Request',
                'keterangan' => $request->notes
            ]);

            $totalNilai = 0;

            // Process each item
            foreach ($request->items as $itemData) {
                if ($itemData['qty_received'] <= 0) continue;

                \Log::info('Processing item: ' . $itemData['id_barang'] . ' qty: ' . $itemData['qty_received']);

                // Create detail record
                $detail = BarangMasukDetail::create([
                    'id_masuk' => $barangMasuk->id_masuk,
                    'id_barang' => $itemData['id_barang'],
                    'qty_masuk' => $itemData['qty_received'],
                    'harga_beli_satuan' => $itemData['unit_price'],
                    'subtotal' => $itemData['subtotal']
                ]);

                // Update stock
                $stok = Stok::where('id_barang', $itemData['id_barang'])->first();
                if ($stok) {
                    $stok->tambahStok(
                        $itemData['qty_received'],
                        Auth::id(),
                        "Barang Masuk: {$barangMasuk->nomor_masuk}",
                        'barang_masuk',
                        $barangMasuk->id_masuk
                    );
                } else {
                    \Log::warning('Stock record not found for item: ' . $itemData['id_barang']);
                }

                $totalNilai += $itemData['subtotal'];

                // Update barang's harga_beli if different
                $barang = Barang::find($itemData['id_barang']);
                if ($barang && $barang->harga_beli != $itemData['unit_price']) {
                    $barang->update(['harga_beli' => $itemData['unit_price']]);
                }
            }

            // Update total nilai
            $barangMasuk->update(['total_nilai' => $totalNilai]);

            // Complete restock request
            $restockRequest->update(['status_request' => 'Completed']);

            // Trigger EOQ recalculation for affected items (if job exists)
            foreach ($request->items as $itemData) {
                if ($itemData['qty_received'] > 0) {
                    try {
                        if (class_exists(\App\Jobs\UpdateEOQCalculations::class)) {
                            dispatch(new \App\Jobs\UpdateEOQCalculations($itemData['id_barang'], false));
                        }
                    } catch (\Exception $e) {
                        \Log::warning('EOQ job dispatch failed: ' . $e->getMessage());
                    }
                }
            }

            DB::commit();

            \Log::info('Successfully processed request: ' . $restockRequest->nomor_request);

            return response()->json([
                'success' => true,
                'message' => 'Incoming stock processed successfully',
                'entry_number' => $barangMasuk->nomor_masuk,
                'total_value' => $totalNilai
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error processing request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process direct stock entry
     */
    public function processDirect(Request $request)
    {
        $request->validate([
            'supplier' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'entry_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.qty_received' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();
        
        try {
            // Create barang masuk record
            $barangMasuk = BarangMasuk::create([
                'tanggal_masuk' => Carbon::parse($request->entry_date),
                'id_user_gudang' => Auth::id(),
                'supplier' => $request->supplier,
                'nomor_invoice' => $request->invoice_number,
                'jenis_masuk' => 'Pembelian Manual',
                'keterangan' => $request->notes
            ]);

            $totalNilai = 0;

            // Process each item
            foreach ($request->items as $itemData) {
                // Create detail record
                $detail = BarangMasukDetail::create([
                    'id_masuk' => $barangMasuk->id_masuk,
                    'id_barang' => $itemData['id_barang'],
                    'qty_masuk' => $itemData['qty_received'],
                    'harga_beli_satuan' => $itemData['unit_price'],
                    'subtotal' => $itemData['subtotal']
                ]);

                // Update stock
                $stok = Stok::where('id_barang', $itemData['id_barang'])->first();
                if ($stok) {
                    $stok->tambahStok(
                        $itemData['qty_received'],
                        Auth::id(),
                        "Barang Masuk Manual: {$barangMasuk->nomor_masuk}",
                        'barang_masuk',
                        $barangMasuk->id_masuk
                    );
                } else {
                    // Create stock record if doesn't exist
                    $newStok = Stok::create([
                        'id_barang' => $itemData['id_barang'],
                        'jumlah_stok' => $itemData['qty_received'],
                        'status_stok' => 'Aman'
                    ]);
                    
                    // Create initial log
                    \App\Models\LogStok::create([
                        'id_barang' => $itemData['id_barang'],
                        'tanggal_log' => now(),
                        'jenis_perubahan' => 'Masuk',
                        'qty_sebelum' => 0,
                        'qty_perubahan' => $itemData['qty_received'],
                        'qty_sesudah' => $itemData['qty_received'],
                        'id_user' => Auth::id(),
                        'referensi_tipe' => 'barang_masuk',
                        'referensi_id' => $barangMasuk->id_masuk,
                        'keterangan' => "Initial Stock - Barang Masuk Manual: {$barangMasuk->nomor_masuk}"
                    ]);
                }

                $totalNilai += $itemData['subtotal'];

                // Update barang's harga_beli if different
                $barang = Barang::find($itemData['id_barang']);
                if ($barang && $barang->harga_beli != $itemData['unit_price']) {
                    $barang->update(['harga_beli' => $itemData['unit_price']]);
                }
            }

            // Update total nilai
            $barangMasuk->update(['total_nilai' => $totalNilai]);

            // Trigger EOQ recalculation for affected items
            foreach ($request->items as $itemData) {
                try {
                    if (class_exists(\App\Jobs\UpdateEOQCalculations::class)) {
                        dispatch(new \App\Jobs\UpdateEOQCalculations($itemData['id_barang'], false));
                    }
                } catch (\Exception $e) {
                    \Log::warning('EOQ job dispatch failed: ' . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Direct stock entry processed successfully',
                'entry_number' => $barangMasuk->nomor_masuk,
                'total_value' => $totalNilai
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent entries
     */
    public function getRecentEntries()
    {
        try {
            $entries = BarangMasuk::with(['userGudang', 'restockRequest'])
                ->withCount('details')
                ->orderBy('tanggal_masuk', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($entry) {
                    return [
                        'id_masuk' => $entry->id_masuk,
                        'nomor_masuk' => $entry->nomor_masuk,
                        'tanggal_masuk' => $entry->tanggal_masuk,
                        'jenis_masuk' => $entry->jenis_masuk,
                        'supplier' => $entry->supplier,
                        'nomor_invoice' => $entry->nomor_invoice,
                        'total_nilai' => $entry->total_nilai,
                        'details_count' => $entry->details_count,
                        'user_gudang' => $entry->userGudang->name ?? 'Unknown',
                        'restock_request' => $entry->restockRequest ? $entry->restockRequest->nomor_request : null
                    ];
                });

            return response()->json([
                'success' => true,
                'entries' => $entries
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific entry details
     */
    public function show($id)
    {
        try {
            $entry = BarangMasuk::with([
                'details.barang',
                'userGudang',
                'restockRequest'
            ])->findOrFail($id);

            return view('gudang.barang-masuk-detail', compact('entry'));

        } catch (\Exception $e) {
            return redirect()->route('gudang.barang-masuk')
                            ->with('error', 'Entry not found.');
        }
    }

    /**
     * Get entry statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            
            $stats = [
                'today_entries' => BarangMasuk::whereDate('tanggal_masuk', $today)->count(),
                'today_value' => BarangMasuk::whereDate('tanggal_masuk', $today)->sum('total_nilai'),
                'month_entries' => BarangMasuk::where('tanggal_masuk', '>=', $thisMonth)->count(),
                'month_value' => BarangMasuk::where('tanggal_masuk', '>=', $thisMonth)->sum('total_nilai'),
                'pending_requests' => RestockRequest::where('status_request', 'Ordered')->count(),
                'recent_by_type' => BarangMasuk::selectRaw('jenis_masuk, COUNT(*) as count')
                    ->where('tanggal_masuk', '>=', $thisMonth)
                    ->groupBy('jenis_masuk')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export entry to PDF
     */
    public function exportEntry($id)
    {
        try {
            $entry = BarangMasuk::with([
                'details.barang',
                'userGudang',
                'restockRequest'
            ])->findOrFail($id);

            // Implementation for PDF export
            // You can use libraries like TCPDF or DomPDF
            
            return response()->json([
                'success' => true,
                'message' => 'Export functionality to be implemented'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}