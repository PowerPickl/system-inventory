<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Stok;
use App\Models\LogStok;
use App\Models\KategoriBarang;
use App\Services\EOQCalculationService;
use App\Services\UrgencyCalculationService; 
use App\Services\BarangCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KelolaDataBarangController extends Controller
{
    protected $eoqService;
    protected $urgencyService;

    public function __construct(
        EOQCalculationService $eoqService,
        UrgencyCalculationService $urgencyService // TAMBAH INI
    ) {
        $this->eoqService = $eoqService;
        $this->urgencyService = $urgencyService; // TAMBAH INI
    }

    /**
     * Display the main Kelola Data Barang page
     */
    public function index()
    {
        try {
            // Get basic stats for dashboard
            $stats = [
                'total_items' => Barang::count(),
                'with_stock' => Barang::whereHas('stok')->count(),
                'need_restock' => Barang::whereHas('stok', function($q) {
                    $q->whereIn('status_stok', ['Perlu Restock', 'Habis']);
                })->count(),
                'with_eoq' => Barang::whereNotNull('eoq_calculated')->count(),
            ];

            return view('gudang.kelola-data-barang', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error loading Kelola Data Barang page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load page: ' . $e->getMessage());
        }
    }

    /**
     * Get paginated barang data with filters
     */
    public function getData(Request $request)
    {
        try {
            // Add debug logging
            \Log::info('getData called with params:', $request->all());
            
            $query = Barang::with(['stok', 'kategori'])
                        ->select([
                            'id_barang', 'id_kategori', 'kode_barang', 'nama_barang', 
                            'merk', 'model_tipe', 'satuan', 'harga_beli', 'harga_jual', 'deskripsi', 'keterangan_detail',
                            'annual_demand', 'ordering_cost', 'holding_cost', 'lead_time',
                            'demand_avg_daily', 'demand_max_daily',
                            'eoq_calculated', 'rop_calculated', 'safety_stock',
                            'last_eoq_calculation', 'updated_at'
                        ]);

            // Debug: Count total before filters
            $totalBeforeFilters = $query->count();
            \Log::info("Total barang before filters: {$totalBeforeFilters}");

            // Apply filters EXCEPT urgency (we'll filter that after transformation)
            if ($search = $request->get('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_barang', 'LIKE', "%{$search}%")
                    ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                    ->orWhere('merk', 'LIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'LIKE', "%{$search}%");
                });
            }

            if ($statusFilter = $request->get('status_filter')) {
                $query->whereHas('stok', function($q) use ($statusFilter) {
                    $q->where('status_stok', $statusFilter);
                });
            }

            if ($eoqFilter = $request->get('eoq_filter')) {
                if ($eoqFilter === 'calculated') {
                    $query->whereNotNull('eoq_calculated');
                } elseif ($eoqFilter === 'not-calculated') {
                    $query->whereNull('eoq_calculated');
                }
            }

            // Debug: Count after non-urgency filters
            $totalAfterFilters = $query->count();
            \Log::info("Total barang after non-urgency filters: {$totalAfterFilters}");

            // Get all data first (before pagination) for urgency calculation
            $barangs = $query->orderBy('updated_at', 'desc')->get();
            \Log::info("Retrieved barangs count: " . $barangs->count());

            // Transform data WITH URGENCY CALCULATION
            $transformedData = $barangs->map(function ($barang) {
                // TAMBAH URGENCY CALCULATION
                $urgencyData = $this->urgencyService->calculateUrgencyLevel($barang);
                
                $currentStock = $barang->stok ? $barang->stok->jumlah_stok : 0;
                $statusStok = $barang->stok ? $barang->stok->status_stok : 'No Stock Data';
                
                $ropValue = $barang->rop_calculated ?? $barang->reorder_point ?? 0;
                $needsRestock = $currentStock <= $ropValue && $currentStock >= 0;

                // Get kategori badge info
                $kategoriBadge = null;
                if ($barang->kategori) {
                    $kategoriBadge = [
                        'nama' => $barang->kategori->nama_kategori,
                        'kode' => $barang->kategori->kode_kategori,
                        'icon' => $barang->kategori->icon ?? '📦',
                        'warna' => $barang->kategori->warna ?? '#6B7280',
                        'style' => "background-color: " . ($barang->kategori->warna ?? '#6B7280') . "20; color: " . ($barang->kategori->warna ?? '#6B7280') . ";"
                    ];
                }

                return [
                    'id_barang' => $barang->id_barang,
                    'id_kategori' => $barang->id_kategori,
                    'kode_barang' => $barang->kode_barang ?: '',
                    'nama_barang' => $barang->nama_barang ?: 'Unknown Item',
                    'merk' => $barang->merk ?: '',
                    'model_tipe' => $barang->model_tipe ?: '',
                    'satuan' => $barang->satuan ?: '',
                    'deskripsi' => $barang->deskripsi ?: '',
                    'keterangan_detail' => $barang->keterangan_detail ?: '',
                    'harga_beli' => $barang->harga_beli ?: 0,
                    'harga_jual' => $barang->harga_jual ?: 0,
                    'current_stock' => $currentStock,
                    'status_stok' => $statusStok,
                    'eoq_calculated' => $barang->eoq_calculated,
                    'rop_calculated' => $barang->rop_calculated,
                    'safety_stock' => $barang->safety_stock,
                    'annual_demand' => $barang->annual_demand,
                    'ordering_cost' => $barang->ordering_cost,
                    'holding_cost' => $barang->holding_cost,
                    'lead_time' => $barang->lead_time,
                    'demand_avg_daily' => $barang->demand_avg_daily,
                    'demand_max_daily' => $barang->demand_max_daily,
                    'last_eoq_calculation' => $barang->last_eoq_calculation 
                        ? $barang->last_eoq_calculation->format('Y-m-d H:i:s') 
                        : null,
                    'last_updated' => $barang->updated_at->format('Y-m-d H:i:s'),
                    'has_eoq_data' => !is_null($barang->eoq_calculated),
                    'needs_restock' => $needsRestock,
                    'kategori_badge' => $kategoriBadge,
                    // TAMBAH URGENCY DATA
                    'urgency_data' => $urgencyData,
                    'urgency_level' => $urgencyData['final_urgency'],
                    'urgency_score' => $urgencyData['urgency_score'],
                    'urgency_badge' => $urgencyData['priority_badge'],
                    'demand_level' => $urgencyData['demand_level'],
                    'auto_reason' => $urgencyData['auto_reason'],
                    'days_until_stockout' => $urgencyData['days_until_stockout']
                ];
            })
            // SORT BY URGENCY SCORE (highest first)
            ->sortByDesc('urgency_score')
            ->values();

            \Log::info('Transformed data count before urgency filter: ' . $transformedData->count());

            // Apply urgency filter AFTER transformation
            $urgencyFilter = $request->get('urgency_filter');
            if ($urgencyFilter) {
                $transformedData = $transformedData->filter(function($item) use ($urgencyFilter) {
                    return $item['urgency_level'] === $urgencyFilter;
                });
                $transformedData = $transformedData->values(); // Re-index after filter
                \Log::info("Filtered by urgency '{$urgencyFilter}': " . $transformedData->count() . " items");
            }

            // Now apply pagination to transformed data
            $total = $transformedData->count();
            $perPage = min((int)$request->get('per_page', 10), 100);
            $page = max((int)$request->get('page', 1), 1);
            
            // Slice the collection for pagination
            $offset = ($page - 1) * $perPage;
            $paginatedData = $transformedData->slice($offset, $perPage)->values();

            \Log::info("Final pagination: page {$page}, showing " . $paginatedData->count() . " of {$total} items");

            $response = [
                'success' => true,
                'data' => $paginatedData,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage),
                    'from' => $total > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $total)
                ]
            ];

            \Log::info('Sending response with ' . $paginatedData->count() . ' items, total: ' . $total);
            
            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error fetching barang data: ' . $e->getMessage(), [
                'request_params' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new barang
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'id_kategori' => 'required|exists:kategori_barang,id_kategori',
        'nama_barang' => 'required|string|max:255',
        'merk' => 'nullable|string|max:100',
        'model_tipe' => 'nullable|string|max:100',
        'satuan' => 'required|string|max:50',
        'harga_beli' => 'required|numeric|min:0',
        'harga_jual' => 'required|numeric|min:0',
        'deskripsi' => 'nullable|string|max:500',
        'keterangan_detail' => 'nullable|string|max:1000',
        'kode_barang' => 'nullable|string|max:50|unique:barang,kode_barang',
        'annual_demand' => 'nullable|numeric|min:0',
        'ordering_cost' => 'nullable|numeric|min:0',
        'holding_cost' => 'nullable|numeric|min:0|max:100',
        'lead_time' => 'nullable|integer|min:0',
        'demand_avg_daily' => 'nullable|numeric|min:0',
        'demand_max_daily' => 'nullable|numeric|min:0'
    ]);

    // Custom validation: harga jual > harga beli
    $validator->after(function ($validator) use ($request) {
        if ($request->harga_jual <= $request->harga_beli) {
            $validator->errors()->add('harga_jual', 'Harga jual harus lebih tinggi dari harga beli');
        }
    });

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        // Generate kode internal automatically
        $kodeInternal = BarangCodeService::generateKodeBarang($request->id_kategori);
        $kategori = KategoriBarang::find($request->id_kategori);

        // Create barang
        $barang = Barang::create([
            'id_kategori' => $request->id_kategori,
            'kode_barang' => $kodeInternal,
            'sequence_number' => $kategori->getNextSequenceNumber(),
            'nama_barang' => $request->nama_barang,
            'merk' => $request->merk,
            'model_tipe' => $request->model_tipe,
            'satuan' => $request->satuan,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'deskripsi' => $request->deskripsi,
            'keterangan_detail' => $request->keterangan_detail,
            'annual_demand' => $request->annual_demand ?? null,
            'ordering_cost' => $request->ordering_cost ?? null,
            'holding_cost' => $request->holding_cost ?? null,
            'lead_time' => $request->lead_time ?? null,
            'demand_avg_daily' => $request->demand_avg_daily ?? null,
            'demand_max_daily' => $request->demand_max_daily ?? null,
            'reorder_point' => 0
        ]);

        // Create initial stock record (start with 0)
        $stok = Stok::create([
            'id_barang' => $barang->id_barang,
            'jumlah_stok' => 0,
            'status_stok' => 'Habis'
        ]);

        // Calculate EOQ if all parameters are provided
        if ($this->hasCompleteEOQParamsForBarang($barang)) {
            try {
                $eoqResult = $this->calculateEOQForBarang($barang);
                if ($eoqResult && $eoqResult['success']) {
                    $this->updateStockStatus($stok, $eoqResult['rop']['rop'] ?? 0);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to calculate EOQ for new barang {$barang->id_barang}: " . $e->getMessage());
            }
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => "Barang '{$barang->nama_barang}' berhasil ditambahkan dengan kode {$kodeInternal}",
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'kode_barang' => $kodeInternal,
                'kategori' => $kategori->nama_kategori
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error creating barang: ' . $e->getMessage());
        
        return response()->json([
                'success' => false,
                'message' => 'Failed to create barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific barang details
     */
    public function show($id)
    {
    try {
        $barang = Barang::with(['stok', 'kategori'])->findOrFail($id);
        
        // Get EOQ calculation details if available
        $eoqDetails = null;
        if ($barang->eoq_calculated) {
            $eoqDetails = $this->eoqService->calculateAll($barang);
        }

        // Get recent stock movements
        $recentMovements = LogStok::where('id_barang', $id)
                                ->with('user')
                                ->orderBy('tanggal_log', 'desc')
                                ->limit(10)
                                ->get();

        // Format kategori info
        $kategoriInfo = null;
        if ($barang->kategori) {
            $kategoriInfo = [
                'id' => $barang->kategori->id_kategori,
                'nama' => $barang->kategori->nama_kategori,
                'kode' => $barang->kategori->kode_kategori,
                'icon' => $barang->kategori->icon,
                'warna' => $barang->kategori->warna,
                'deskripsi' => $barang->kategori->deskripsi
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'barang' => $barang,
                'kategori_info' => $kategoriInfo,
                'current_stock' => $barang->stok ? $barang->stok->jumlah_stok : 0,
                'status_stok' => $barang->stok ? $barang->stok->status_stok : 'No Stock Data',
                'eoq_details' => $eoqDetails,
                'recent_movements' => $recentMovements,
                'display_name' => $barang->nama_lengkap // Use accessor from model
            ]
        ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barang not found or error occurred: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update barang data
     */
    public function update(Request $request, $id)
    {
    $validator = Validator::make($request->all(), [
        'id_kategori' => 'required|exists:kategori_barang,id_kategori',
        'nama_barang' => 'required|string|max:255',
        'merk' => 'nullable|string|max:100',
        'model_tipe' => 'nullable|string|max:100',
        'satuan' => 'required|string|max:50',
        'harga_beli' => 'required|numeric|min:0',
        'harga_jual' => 'required|numeric|min:0',
        'deskripsi' => 'nullable|string|max:500',
        'keterangan_detail' => 'nullable|string|max:1000',
        'kode_barang' => 'nullable|string|max:50|unique:barang,kode_barang,' . $id . ',id_barang',
        'annual_demand' => 'nullable|numeric|min:0',
        'ordering_cost' => 'nullable|numeric|min:0',
        'holding_cost' => 'nullable|numeric|min:0|max:100',
        'lead_time' => 'nullable|integer|min:0',
        'demand_avg_daily' => 'nullable|numeric|min:0',
        'demand_max_daily' => 'nullable|numeric|min:0',
        'stock_adjustment' => 'nullable|integer',
        'adjustment_reason' => 'nullable|string|max:255'
    ]);

    // Custom validation
    $validator->after(function ($validator) use ($request) {
        if ($request->harga_jual <= $request->harga_beli) {
            $validator->errors()->add('harga_jual', 'Harga jual harus lebih tinggi dari harga beli');
        }
    });

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        DB::beginTransaction();

        $barang = Barang::findOrFail($id);
        $oldData = $barang->toArray();

        // Check if kategori changed - regenerate kode if needed
        $needNewKode = false;
        if ($barang->id_kategori != $request->id_kategori) {
            $newKategori = KategoriBarang::find($request->id_kategori);
            $newKodeInternal = $newKategori->generateKodeBarang();
            $needNewKode = true;
        }

        // Update barang data
        $updateData = [
            'id_kategori' => $request->id_kategori,
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'merk' => $request->merk,
            'model_tipe' => $request->model_tipe,
            'satuan' => $request->satuan,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'deskripsi' => $request->deskripsi,
            'keterangan_detail' => $request->keterangan_detail,
            'annual_demand' => $request->annual_demand,
            'ordering_cost' => $request->ordering_cost,
            'holding_cost' => $request->holding_cost,
            'lead_time' => $request->lead_time,
            'demand_avg_daily' => $request->demand_avg_daily,
            'demand_max_daily' => $request->demand_max_daily,
        ];

        // Add new kode if kategori changed
        if ($needNewKode) {
            $updateData['kode_barang'] = $newKodeInternal;
            $updateData['sequence_number'] = $newKategori->getNextSequenceNumber();
        }

        $barang->update($updateData);

        // Handle stock adjustment if provided
        if ($request->has('stock_adjustment') && $request->stock_adjustment != 0) {
            $this->processStockAdjustment(
                $barang, 
                $request->stock_adjustment, 
                $request->adjustment_reason ?? 'Manual adjustment from barang update'
            );
        }

        // Recalculate EOQ if parameters changed
        if ($this->hasCompleteEOQParamsForBarang($barang)) {
            $this->calculateEOQForBarang($barang);
        }

        // Log the changes
        $this->logBarangChanges($barang, $oldData, $barang->fresh()->toArray());

        DB::commit();

        $message = "Barang '{$barang->nama_barang}' berhasil diupdate";
        if ($needNewKode) {
            $message .= " dengan kode baru {$newKodeInternal}";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'kode_barang' => $barang->kode_barang,
                'kategori_changed' => $needNewKode
            ]
        ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating barang: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete barang (soft delete)
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $barang = Barang::findOrFail($id);
            
            // Check if barang has stock
            if ($barang->stok && $barang->stok->jumlah_stok > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete barang with existing stock. Please adjust stock to 0 first.'
                ], 400);
            }

            // Check if barang is used in transactions
            $hasTransactions = $barang->detailTransaksi()->exists();
            if ($hasTransactions) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete barang that has transaction history. Consider archiving instead.'
                ], 400);
            }

            $namaBarang = $barang->nama_barang;
            
            // Delete related records
            if ($barang->stok) {
                $barang->stok->delete();
            }
            
            // Delete barang
            $barang->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Barang '{$namaBarang}' berhasil dihapus"
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting barang: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adjust stock for a specific barang
     */
    public function adjustStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'adjustment' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $barang = Barang::with('stok')->findOrFail($id);
            
            if (!$barang->stok) {
                // Create stock record if doesn't exist
                $barang->stok = Stok::create([
                    'id_barang' => $barang->id_barang,
                    'jumlah_stok' => 0,
                    'status_stok' => 'Habis'
                ]);
            }

            $this->processStockAdjustment($barang, $request->adjustment, $request->reason);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Stock adjustment berhasil: {$request->adjustment} units",
                'data' => [
                    'new_stock' => $barang->stok->jumlah_stok,
                    'status_stok' => $barang->stok->status_stok
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error adjusting stock: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate EOQ for specific barang
     */
    public function calculateEOQ($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            
            // Validate that we have complete EOQ parameters
            if (!$this->hasCompleteEOQParamsForBarang($barang)) {
                return response()->json([
                    'success' => false,
                    'message' => 'EOQ parameters incomplete. Required: annual_demand, ordering_cost, holding_cost, lead_time, demand_avg_daily, demand_max_daily'
                ], 400);
            }

            // Use the EOQ service for calculation
            $result = $this->calculateEOQForBarang($barang);
            
            if ($result && $result['success']) {
                // Update stock status with new ROP
                if ($barang->stok && isset($result['rop']['rop'])) {
                    $this->updateStockStatus($barang->stok, $result['rop']['rop']);
                }

                return response()->json([
                    'success' => true,
                    'message' => "EOQ calculated successfully for {$barang->nama_barang}",
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'EOQ calculation failed'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error calculating EOQ: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate EOQ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update EOQ for selected items
     */
    public function bulkUpdateEOQ(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:barang,id_barang'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $successCount = 0;
            $failedItems = [];

            foreach ($request->item_ids as $itemId) {
                try {
                    $barang = Barang::find($itemId);
                    
                    if ($this->hasCompleteEOQParamsForBarang($barang)) {
                        $this->calculateEOQForBarang($barang);
                        $successCount++;
                    } else {
                        $failedItems[] = $barang->nama_barang . ' (incomplete parameters)';
                    }
                } catch (\Exception $e) {
                    $failedItems[] = "Item ID {$itemId} (error: {$e->getMessage()})";
                }
            }

            $message = "EOQ updated for {$successCount} items";
            if (!empty($failedItems)) {
                $message .= ". Failed: " . implode(', ', $failedItems);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'success_count' => $successCount,
                    'failed_count' => count($failedItems),
                    'failed_items' => $failedItems
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk EOQ update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk EOQ update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estimate ordering cost based on item characteristics
     */
    private function estimateOrderingCost(Barang $barang)
    {
        // Base ordering cost calculation
        $baseOrderingCost = 50000; // Base 50k IDR
        
        // Adjust based on item price (higher value items = higher ordering cost)
        $priceAdjustment = $barang->harga_beli * 0.05; // 5% of item cost
        
        // Add minimum threshold
        $estimatedCost = max($baseOrderingCost, $priceAdjustment);
        
        // Cap at reasonable maximum
        return min($estimatedCost, 500000); // Max 500k IDR
    }

    /**
     * Estimate holding cost percentage based on item characteristics
     */
    private function estimateHoldingCost(Barang $barang)
    {
        // Default holding cost percentage
        $baseHoldingCost = 20; // 20% per year
        
        // Adjust based on item value and characteristics
        if ($barang->harga_beli > 1000000) {
            // Expensive items - higher holding cost
            $baseHoldingCost = 25;
        } elseif ($barang->harga_beli < 100000) {
            // Cheap items - lower holding cost
            $baseHoldingCost = 15;
        }
        
        return $baseHoldingCost;
    }





    /**
     * Auto-calculate EOQ parameters from historical data
     */
    public function autoCalculateEOQParams($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            
            // Calculate demand from historical data using the service
            $demandData = $this->eoqService->calculateDemandFromHistory($barang, 365);
            
            if ($demandData['total_usage'] <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No historical transaction data found for this item. Please enter EOQ parameters manually.'
                ], 400);
            }
            
            // Auto-estimate costs based on item characteristics
            $estimatedOrderingCost = $this->estimateOrderingCost($barang);
            $estimatedHoldingCost = $this->estimateHoldingCost($barang);
            $estimatedLeadTime = $barang->lead_time ?? 7; // Default 7 days
            
            // Update barang with calculated parameters
            $barang->update([
                'annual_demand' => $demandData['annual_demand'],
                'demand_avg_daily' => $demandData['avg_daily_demand'],
                'demand_max_daily' => $demandData['max_daily_demand'],
                'ordering_cost' => $estimatedOrderingCost,
                'holding_cost' => $estimatedHoldingCost,
                'lead_time' => $estimatedLeadTime
            ]);

            // Now calculate EOQ with the new parameters
            $eoqResult = $this->calculateEOQForBarang($barang);

            return response()->json([
                'success' => true,
                'message' => 'EOQ parameters calculated from historical data',
                'data' => [
                    'demand_analysis' => $demandData,
                    'estimated_costs' => [
                        'ordering_cost' => $estimatedOrderingCost,
                        'holding_cost' => $estimatedHoldingCost,
                        'lead_time' => $estimatedLeadTime
                    ],
                    'eoq_calculation' => $eoqResult,
                    'data_period' => $demandData['period_days'] . ' days'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error auto-calculating EOQ params: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to auto-calculate parameters: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity log with filters
     */
    public function getActivityLog(Request $request)
    {
        try {
            $query = LogStok::with(['barang', 'user'])
                        ->select([
                            'id_log', 'id_barang', 'tanggal_log', 'jenis_perubahan',
                            'qty_sebelum', 'qty_perubahan', 'qty_sesudah',
                            'id_user', 'referensi_tipe', 'keterangan'
                        ]);

            // Apply filters
            if ($search = $request->get('search')) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('barang', function($subQ) use ($search) {
                        $subQ->where('nama_barang', 'LIKE', "%{$search}%")
                            ->orWhere('kode_barang', 'LIKE', "%{$search}%");
                    })->orWhere('keterangan', 'LIKE', "%{$search}%");
                });
            }

            if ($activityType = $request->get('activity_type')) {
                // Map frontend types to actual jenis_perubahan values
                $jenisPerubahan = match($activityType) {
                    'create' => 'Masuk',
                    'update' => 'Adjustment', 
                    'stock_adjustment' => 'Adjustment',
                    'eoq_update' => 'Adjustment',
                    default => $activityType
                };
                $query->where('jenis_perubahan', $jenisPerubahan);
            }

            if ($activityDate = $request->get('activity_date')) {
                $query->whereDate('tanggal_log', $activityDate);
            }

            // Pagination
            $perPage = min((int)$request->get('per_page', 20), 100);
            $page = max((int)$request->get('page', 1), 1);
            
            $total = $query->count();
            $activities = $query->orderBy('tanggal_log', 'desc')
                            ->offset(($page - 1) * $perPage)
                            ->limit($perPage)
                            ->get();

            // Transform data with NULL checks
            $transformedData = $activities->map(function ($log) {
                return [
                    'id' => $log->id_log,
                    'type' => strtolower($log->jenis_perubahan ?: 'unknown'),
                    'user' => $log->user ? $log->user->name : 'System',
                    'action' => $this->getActivityAction($log),
                    'item' => $log->barang ? $log->barang->nama_barang : 'Unknown Item',
                    'item_code' => $log->barang ? $log->barang->kode_barang : '-',
                    'details' => $this->getActivityDetails($log),
                    'timestamp' => $log->tanggal_log ? $log->tanggal_log->format('Y-m-d H:i:s') : '-',
                    'icon' => $this->getActivityIcon($log->jenis_perubahan),
                    'color' => $this->getActivityColor($log->jenis_perubahan)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedData,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage),
                    'from' => ($page - 1) * $perPage + 1,
                    'to' => min($page * $perPage, $total)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching activity log: ' . $e->getMessage(), [
                'request_params' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activity log: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export barang data to CSV
     */
    public function exportData(Request $request)
    {
    try {
        $query = Barang::with(['stok', 'kategori']);

        // Apply filters including kategori
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'LIKE', "%{$search}%")
                  ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                  ->orWhere('merk', 'LIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$search}%");
            });
        }

        if ($statusFilter = $request->get('status_filter')) {
            $query->whereHas('stok', function($q) use ($statusFilter) {
                $q->where('status_stok', $statusFilter);
            });
        }

        if ($eoqFilter = $request->get('eoq_filter')) {
            if ($eoqFilter === 'calculated') {
                $query->whereNotNull('eoq_calculated');
            } elseif ($eoqFilter === 'not-calculated') {
                $query->whereNull('eoq_calculated');
            }
        }

        if ($kategoriFilter = $request->get('kategori_filter')) {
            $query->where('id_kategori', $kategoriFilter);
        }

        $barangs = $query->get();
        $filename = 'data-barang-' . date('Y-m-d-H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function() use ($barangs) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers with kategori
            fputcsv($file, [
                'Kategori',
                'Kode Barang',
                'Kode Manual (Legacy)',
                'Nama Barang',
                'Merk',
                'Model/Tipe', 
                'Satuan', 
                'Harga Beli', 
                'Harga Jual',
                'Profit',
                'Stock Saat Ini', 
                'Status Stock', 
                'EOQ', 
                'ROP', 
                'Safety Stock',
                'Annual Demand', 
                'Ordering Cost', 
                'Holding Cost', 
                'Lead Time',
                'Avg Daily Demand',
                'Max Daily Demand',
                'Deskripsi',
                'Keterangan Detail',
                'Last EOQ Calculation',
                'Last Updated'
            ]);

            // Data rows with kategori info
            foreach ($barangs as $barang) {
                $profit = ($barang->harga_jual ?: 0) - ($barang->harga_beli ?: 0);
                
                fputcsv($file, [
                    $barang->kategori ? $barang->kategori->nama_kategori : 'Uncategorized',
                    $barang->kode_barang ?: '',
                    $barang->kode_barang ?: '',
                    $barang->nama_barang ?: '',
                    $barang->merk ?: '',
                    $barang->model_tipe ?: '',
                    $barang->satuan ?: '',
                    $barang->harga_beli ?: 0,
                    $barang->harga_jual ?: 0,
                    $profit,
                    $barang->stok ? $barang->stok->jumlah_stok : 0,
                    $barang->stok ? $barang->stok->status_stok : 'No Data',
                    $barang->eoq_calculated ?? '-',
                    $barang->rop_calculated ?? '-',
                    $barang->safety_stock ?? '-',
                    $barang->annual_demand ?? '-',
                    $barang->ordering_cost ?? '-',
                    $barang->holding_cost ?? '-',
                    $barang->lead_time ?? '-',
                    $barang->demand_avg_daily ?? '-',
                    $barang->demand_max_daily ?? '-',
                    $barang->deskripsi ?: '',
                    $barang->keterangan_detail ?: '',
                    $barang->last_eoq_calculation ? $barang->last_eoq_calculation->format('Y-m-d H:i:s') : '-',
                    $barang->updated_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export data: ' . $e->getMessage()
            ], 500);
        }
    }

    // =====================================
    // HELPER METHODS
    // =====================================

    /**
     * Check if request has complete EOQ parameters
     */
    private function hasCompleteEOQParams(Request $request)
    {
        return $request->filled(['annual_demand', 'ordering_cost', 'holding_cost', 'lead_time']);
    }

    /**
     * Check if barang has complete EOQ parameters
     */
    private function hasCompleteEOQParamsForBarang(Barang $barang)
    {
         $required = [
            'annual_demand' => $barang->annual_demand,
            'ordering_cost' => $barang->ordering_cost,
            'holding_cost' => $barang->holding_cost,
            'lead_time' => $barang->lead_time,
            'demand_avg_daily' => $barang->demand_avg_daily,
            'demand_max_daily' => $barang->demand_max_daily
        ];

        foreach ($required as $field => $value) {
            if (is_null($value) || $value <= 0) {
                Log::debug("Missing EOQ parameter: {$field} = {$value} for barang {$barang->id_barang}");
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate EOQ for a barang and update the record
     */
    private function calculateEOQForBarang(Barang $barang)
    {
        try {
            // Refresh the model to get latest data
            $barang->refresh();

            // Debug: Log current barang EOQ parameters
            Log::info("EOQ Calculation for barang {$barang->id_barang}:", [
                'annual_demand' => $barang->annual_demand,
                'ordering_cost' => $barang->ordering_cost,
                'holding_cost' => $barang->holding_cost,
                'lead_time' => $barang->lead_time,
                'demand_avg_daily' => $barang->demand_avg_daily,
                'demand_max_daily' => $barang->demand_max_daily,
                'harga_beli' => $barang->harga_beli
            ]);

            // Validate required parameters
            if (!$this->hasCompleteEOQParamsForBarang($barang)) {
                Log::warning("Incomplete EOQ parameters for barang {$barang->id_barang}");
                return [
                    'success' => false,
                    'error' => 'Missing required EOQ parameters'
                ];
            }

            // Use the EOQ service for proper calculation
            $calculations = $this->eoqService->calculateAll($barang);
            
            if ($calculations['success']) {
                // Update stock status if we have a stok record
                if ($barang->stok && isset($calculations['rop']['rop'])) {
                    $this->updateStockStatus($barang->stok, $calculations['rop']['rop']);
                }

                Log::info("EOQ calculation successful for barang {$barang->id_barang}", [
                    'eoq' => $calculations['eoq']['eoq'],
                    'safety_stock' => $calculations['safety_stock']['safety_stock'],
                    'rop' => $calculations['rop']['rop']
                ]);
                
                return $calculations;
            } else {
                Log::error("EOQ Service failed for barang {$barang->id_barang}: " . $calculations['error']);
                return $calculations;
            }
            
        } catch (\Exception $e) {
            Log::error("Error calculating EOQ for barang {$barang->id_barang}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process stock adjustment
     */
    private function processStockAdjustment(Barang $barang, $adjustment, $reason)
    {
        $stok = $barang->stok;
        $stokSebelum = $stok->jumlah_stok;

        if ($adjustment > 0) {
            // Add stock
            $stok->tambahStok(
                $adjustment, 
                Auth::id(), 
                $reason, 
                'manual_adjustment'
            );
        } else {
            // Reduce stock
            $stok->kurangiStok(
                abs($adjustment), 
                Auth::id(), 
                $reason, 
                'manual_adjustment'
            );
        }

        return $stok;
    }

    /**
     * Log changes made to barang data
     */
    private function logBarangChanges(Barang $barang, array $oldData, array $newData)
    {
        $changes = [];
        $importantFields = [
            'nama_barang', 'kode_barang', 'harga_beli', 'harga_jual', 
            'annual_demand', 'ordering_cost', 'holding_cost', 'lead_time'
        ];

        foreach ($importantFields as $field) {
            if (array_key_exists($field, $oldData) && array_key_exists($field, $newData)) {
                if ($oldData[$field] != $newData[$field]) {
                    $changes[] = "{$field}: {$oldData[$field]} → {$newData[$field]}";
                }
            }
        }

        if (!empty($changes)) {
            // Use 'Adjustment' which exists in your LogStok model
            LogStok::create([
                'id_barang' => $barang->id_barang,
                'tanggal_log' => now(),
                'jenis_perubahan' => 'Adjustment', // Changed to match your model's match cases
                'qty_sebelum' => 0,
                'qty_perubahan' => 0,
                'qty_sesudah' => 0,
                'id_user' => Auth::id(),
                'referensi_tipe' => 'barang_update',
                'keterangan' => 'Data barang diupdate: ' . implode(', ', $changes)
            ]);
        }
    }

    /**
     * Get activity action description
     */
    private function getActivityAction(LogStok $log)
    {
        switch ($log->jenis_perubahan) {
            case 'Masuk':
                return 'Stock added';
            case 'Keluar':
                return 'Stock reduced';
            case 'Adjustment':
                return 'Data updated'; // Handle barang updates and stock adjustments
            case 'Koreksi':
                return 'Stock correction';
            default:
                return 'Stock change';
        }
    }

    /**
     * Get activity details
     */
    private function getActivityDetails(LogStok $log)
    {
        // For barang updates (when referensi_tipe is 'barang_update')
        if ($log->referensi_tipe === 'barang_update') {
            return $log->keterangan;
        }

        // For stock changes
        $details = "Quantity: ";
        if ($log->qty_perubahan > 0) {
            $details .= "+{$log->qty_perubahan}";
        } else {
            $details .= $log->qty_perubahan;
        }
        
        $details .= " ({$log->qty_sebelum} → {$log->qty_sesudah})";
        
        if ($log->keterangan) {
            $details .= " - {$log->keterangan}";
        }

        return $details;
    }

    /**
     * Get activity icon
     */
    private function getActivityIcon($jenisPerubahan)
    {
        return match($jenisPerubahan) {
            'Masuk' => '📈',
            'Keluar' => '📉',
            'Adjustment' => '⚖️', // Can be used for both stock adjustments and data updates
            'Koreksi' => '✏️',
            default => '📦'
        };
    }

    /**
     * Get activity color
     */
    private function getActivityColor($jenisPerubahan)
    {
        return match($jenisPerubahan) {
            'Masuk' => 'green',
            'Keluar' => 'red',
            'Adjustment' => 'blue', // Blue for adjustments/updates
            'Koreksi' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Get dashboard statistics
     */
    public function getStats()
    {
        try {
            $stats = [
                'total_items' => Barang::count(),
                'with_stock' => Barang::whereHas('stok', function($q) {
                    $q->where('jumlah_stok', '>', 0);
                })->count(),
                'need_restock' => Barang::whereHas('stok', function($q) {
                    $q->whereIn('status_stok', ['Perlu Restock', 'Habis']);
                })->count(),
                'with_eoq' => Barang::whereNotNull('eoq_calculated')->count(),
                'last_activity' => LogStok::latest('tanggal_log')->first()?->tanggal_log?->format('Y-m-d H:i:s'),
                'low_stock_items' => Barang::whereHas('stok', function($q) {
                    $q->where('status_stok', 'Perlu Restock');
                })->count(),
                'out_of_stock_items' => Barang::whereHas('stok', function($q) {
                    $q->where('status_stok', 'Habis');
                })->count()
            ];

            // Add kategori breakdown
            $kategoriStats = KategoriBarang::withStats()->aktif()->get()->map(function($kategori) {
                return [
                    'nama' => $kategori->nama_kategori,
                    'kode' => $kategori->kode_kategori,
                    'icon' => $kategori->icon,
                    'warna' => $kategori->warna,
                    'total_barang' => $kategori->total_barang,
                    'barang_aman' => $kategori->barang_aman,
                    'barang_restock' => $kategori->barang_restock,
                    'barang_habis' => $kategori->barang_habis
                ];
            });

            $stats['kategori_breakdown'] = $kategoriStats;

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }
            

    /**
     * Search barang for autocomplete/select components
     */
    public function searchBarang(Request $request)
    {
        try {
            $search = $request->get('q', '');
            $limit = $request->get('limit', 10);

            $barangs = Barang::with('stok')
                            ->where('nama_barang', 'LIKE', "%{$search}%")
                            ->orWhere('kode_barang', 'LIKE', "%{$search}%")
                            ->limit($limit)
                            ->get()
                            ->map(function($barang) {
                                return [
                                    'id' => $barang->id_barang,
                                    'text' => "{$barang->nama_barang} ({$barang->kode_barang})",
                                    'nama_barang' => $barang->nama_barang,
                                    'kode_barang' => $barang->kode_barang,
                                    'current_stock' => $barang->stok ? $barang->stok->jumlah_stok : 0,
                                    'status_stok' => $barang->stok ? $barang->stok->status_stok : 'No Data',
                                    'harga_jual' => $barang->harga_jual
                                ];
                            });

            return response()->json([
                'success' => true,
                'data' => $barangs
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching barang: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Search failed'
            ], 500);
        }
    }

    /**
     * Validate barang code uniqueness (for real-time validation)
     */
    public function validateKodeBarang(Request $request)
    {
        $kodeBarang = $request->get('kode_barang');
        $excludeId = $request->get('exclude_id'); // For updates

        $query = Barang::where('kode_barang', $kodeBarang);
        
        if ($excludeId) {
            $query->where('id_barang', '!=', $excludeId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Kode barang sudah digunakan' : 'Kode barang tersedia'
        ]);
    }

    /**
     * Get recent activities for dashboard widget
     */
    public function getRecentActivities($limit = 5)
    {
        try {
            $activities = LogStok::with(['barang', 'user'])
                                ->orderBy('tanggal_log', 'desc')
                                ->limit($limit)
                                ->get()
                                ->map(function($log) {
                                    return [
                                        'id' => $log->id_log,
                                        'action' => $this->getActivityAction($log),
                                        'item' => $log->barang ? $log->barang->nama_barang : 'Unknown',
                                        'user' => $log->user ? $log->user->name : 'System',
                                        'timestamp' => $log->tanggal_log->diffForHumans(),
                                        'icon' => $this->getActivityIcon($log->jenis_perubahan),
                                        'color' => $this->getActivityColor($log->jenis_perubahan)
                                    ];
                                });

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching recent activities: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent activities'
            ], 500);
        }
    }

     /**
     * Bulk calculate EOQ with better job integration
     */
    public function bulkCalculateEOQ(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:barang,id_barang',
            'use_queue' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $useQueue = $request->get('use_queue', true);
        $itemIds = $request->item_ids;

        try {
            if ($useQueue && count($itemIds) > 5) {
                // Use queue for large batch operations
                foreach ($itemIds as $itemId) {
                    UpdateEOQCalculations::dispatch($itemId, true);
                }

                return response()->json([
                    'success' => true,
                    'message' => "EOQ calculation queued for " . count($itemIds) . " items. Check back in a few minutes.",
                    'data' => [
                        'queued_count' => count($itemIds),
                        'processing_method' => 'background_job'
                    ]
                ]);
            } else {
                // Process immediately for small batches
                $successCount = 0;
                $failedItems = [];

                foreach ($itemIds as $itemId) {
                    try {
                        $barang = Barang::find($itemId);
                        
                        if ($this->hasCompleteEOQParamsForBarang($barang)) {
                            $result = $this->calculateEOQForBarang($barang);
                            if ($result && $result['success']) {
                                $successCount++;
                            } else {
                                $failedItems[] = $barang->nama_barang . ' (calculation failed)';
                            }
                        } else {
                            $failedItems[] = $barang->nama_barang . ' (incomplete parameters)';
                        }
                    } catch (\Exception $e) {
                        $failedItems[] = "Item ID {$itemId} (error: {$e->getMessage()})";
                    }
                }

                $message = "EOQ updated for {$successCount} items";
                if (!empty($failedItems)) {
                    $message .= ". Failed: " . implode(', ', array_slice($failedItems, 0, 3));
                    if (count($failedItems) > 3) {
                        $message .= " and " . (count($failedItems) - 3) . " others";
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'success_count' => $successCount,
                        'failed_count' => count($failedItems),
                        'failed_items' => $failedItems,
                        'processing_method' => 'immediate'
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in bulk EOQ calculation: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk EOQ calculation failed: ' . $e->getMessage()
            ], 500);
        }
    }




     /**
     * Calculate stock status based on current stock and ROP
     */
    private function calculateStockStatus($currentStock, $rop)
    {
        if ($currentStock <= 0) {
            return 'Habis';
        } elseif ($currentStock <= $rop) {
            return 'Perlu Restock';
        } else {
            return 'Aman';
        }
    }

     /**
     * Update stock status based on ROP
     */
    private function updateStockStatus($stok, $rop)
    {
        $newStatus = $this->calculateStockStatus($stok->jumlah_stok, $rop);
        
        if ($stok->status_stok !== $newStatus) {
            $stok->update(['status_stok' => $newStatus]);
            Log::info("Stock status updated for item {$stok->id_barang}: {$stok->status_stok} -> {$newStatus}");
        }
    }



    /**
     * Get kategori options for form dropdown
     */
    public function getKategoriOptions()
    {
        try {
            $options = BarangCodeService::getKategoriOptions();
            
            return response()->json([
                'success' => true,
                'data' => $options
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading kategori options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate kode barang preview
     */
    public function previewKodeBarang(Request $request)
    {
        try {
            $kategoriId = $request->get('kategori_id');
            
            if (!$kategoriId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori ID required'
                ], 400);
            }
            
            $kode = BarangCodeService::generateKodeBarang($kategoriId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'kode_barang' => $kode,
                    'preview' => "Preview: {$kode}"
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
        
}