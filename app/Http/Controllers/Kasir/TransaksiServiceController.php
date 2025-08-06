<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Barang;
use App\Models\Stok;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiServiceController extends Controller
{
    public function index()
    {
        // Get transaksi yang masih dalam progress untuk kasir ini
        $activeTransactions = Transaksi::where('id_user', Auth::id())
            ->where('status_transaksi', 'Progress')
            ->with(['detailTransaksi.barang'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get completed transactions hari ini
        $todayTransactions = Transaksi::where('id_user', Auth::id())
            ->where('status_transaksi', 'Selesai')
            ->whereDate('created_at', today())
            ->with(['detailTransaksi.barang'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kasir.transaksi-service', compact('activeTransactions', 'todayTransactions'));
    }

    public function searchBarang(Request $request)
    {
        $search = $request->get('search', '');
        
        $barang = Barang::where(function($query) use ($search) {
                $query->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            })
            ->orderBy('nama_barang')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id_barang,
                    'kode' => $item->kode_barang,
                    'nama' => $item->nama_barang,
                    'satuan' => $item->satuan,
                    'harga' => $item->harga_jual,
                    // Service Advisor tidak perlu lihat stok
                    'available' => true // Semua barang selalu available untuk service advisor
                ];
            });

        return response()->json($barang);
    }

    public function createTransaksi(Request $request)
    {
        $request->validate([
            'kategori_service' => 'required|string',
            'nama_customer' => 'required|string|max:255',
            'kendaraan' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.qty' => 'required|integer|min:1',
        ], [
            'kategori_service.required' => 'Kategori service harus dipilih',
            'nama_customer.required' => 'Nama customer harus diisi',
            'kendaraan.required' => 'Info kendaraan harus diisi',
            'items.required' => 'Minimal pilih 1 barang',
            'items.*.qty.min' => 'Quantity minimal 1'
        ]);

        try {
            DB::beginTransaction();

            // Create transaksi
            $transaksi = Transaksi::create([
                'tanggal_transaksi' => now(),
                'id_user' => Auth::id(),
                'nama_customer' => $request->nama_customer,
                'kendaraan' => $request->kendaraan,
                'jenis_transaksi' => $request->kategori_service,
                'status_transaksi' => 'Progress',
                'keterangan' => 'Menunggu validasi gudang',
                'total_harga' => 0
            ]);

            $totalHarga = 0;

            // Add detail transaksi untuk setiap item
            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['id_barang']);
                $subtotal = $barang->harga_jual * $item['qty'];
                $totalHarga += $subtotal;

                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang' => $item['id_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                    'status_permintaan' => 'Pending'
                ]);
            }

            // Update total harga
            $transaksi->update(['total_harga' => $totalHarga]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat! Request barang telah dikirim ke gudang untuk validasi.',
                'data' => [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'total_items' => count($request->items)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransaksiDetail($id)
    {
        $transaksi = Transaksi::with(['detailTransaksi.barang.stok', 'kasir'])
            ->where('id_transaksi', $id)
            ->where('id_user', Auth::id()) // Pastikan kasir hanya bisa akses transaksi sendiri
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id_transaksi' => $transaksi->id_transaksi,
                'nomor_transaksi' => $transaksi->nomor_transaksi,
                'tanggal' => $transaksi->tanggal_transaksi->format('d/m/Y H:i'),
                'customer' => $transaksi->nama_customer,
                'kendaraan' => $transaksi->kendaraan,
                'service' => $transaksi->jenis_transaksi,
                'status' => $transaksi->status_transaksi,
                'total_harga' => $transaksi->total_harga,
                'items' => $transaksi->detailTransaksi->map(function($detail) {
                    return [
                        'id_detail' => $detail->id_detail,
                        'kode_barang' => $detail->barang->kode_barang,
                        'nama_barang' => $detail->barang->nama_barang,
                        'qty' => $detail->qty,
                        'harga_satuan' => $detail->harga_satuan,
                        'subtotal' => $detail->subtotal,
                        'status_permintaan' => $detail->status_permintaan,
                        'stok_tersedia' => $detail->barang->stok ? $detail->barang->stok->jumlah_stok : 0
                    ];
                })
            ]
        ]);
    }

    public function addItemsToTransaksi(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $transaksi = Transaksi::where('id_transaksi', $id)
                ->where('id_user', Auth::id())
                ->where('status_transaksi', 'Selesai') // Hanya bisa add ke transaksi yang sudah selesai
                ->firstOrFail();

            $totalTambahan = 0;

            // Add new items
            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['id_barang']);
                $subtotal = $barang->harga_jual * $item['qty'];
                $totalTambahan += $subtotal;

                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang' => $item['id_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                    'status_permintaan' => 'Pending'
                ]);
            }

            // Update status transaksi kembali ke Progress
            $transaksi->update([
                'status_transaksi' => 'Progress',
                'keterangan' => 'Menunggu validasi tambahan barang dari gudang'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item tambahan berhasil ditambahkan! Request telah dikirim ke gudang untuk validasi.',
                'data' => [
                    'total_items_baru' => count($request->items),
                    'total_harga_tambahan' => $totalTambahan
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeTransaksi($id)
    {
        try {
            DB::beginTransaction();

            $transaksi = Transaksi::with('detailTransaksi.barang.stok')
                ->where('id_transaksi', $id)
                ->where('id_user', Auth::id())
                ->where('status_transaksi', 'Progress')
                ->firstOrFail();

            // Check if all items are validated
            $pendingItems = $transaksi->detailTransaksi->where('status_permintaan', 'Pending');
            if ($pendingItems->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada item yang belum divalidasi gudang. Tidak dapat menyelesaikan transaksi.'
                ], 400);
            }

            // Process approved items (kurangi stok)
            $approvedItems = $transaksi->detailTransaksi->where('status_permintaan', 'Approved');
            
            foreach ($approvedItems as $detail) {
                if ($detail->barang->stok) {
                    $detail->barang->stok->kurangiStok(
                        $detail->qty,
                        Auth::id(),
                        "Transaksi Service: {$transaksi->nomor_transaksi}",
                        'transaksi',
                        $transaksi->id_transaksi
                    );
                }
            }

            // Update total harga berdasarkan approved items saja
            $totalApproved = $approvedItems->sum('subtotal');
            
            $transaksi->update([
                'status_transaksi' => 'Selesai',
                'total_harga' => $totalApproved,
                'keterangan' => 'Transaksi selesai'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diselesaikan!',
                'data' => [
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'total_approved' => $approvedItems->count(),
                    'total_rejected' => $transaksi->detailTransaksi->where('status_permintaan', 'Rejected')->count(),
                    'total_harga' => $totalApproved
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getValidationStatus()
    {
        // Real-time check untuk status validasi
        $pendingTransactions = Transaksi::where('id_user', Auth::id())
            ->where('status_transaksi', 'Progress')
            ->with(['detailTransaksi' => function($query) {
                $query->whereIn('status_permintaan', ['Pending', 'Approved', 'Rejected']);
            }])
            ->get()
            ->map(function($transaksi) {
                $details = $transaksi->detailTransaksi;
                return [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'total_items' => $details->count(),
                    'pending_count' => $details->where('status_permintaan', 'Pending')->count(),
                    'approved_count' => $details->where('status_permintaan', 'Approved')->count(),
                    'rejected_count' => $details->where('status_permintaan', 'Rejected')->count(),
                    'can_complete' => $details->where('status_permintaan', 'Pending')->count() === 0
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $pendingTransactions
        ]);
    }


    /**
     * Search nota untuk reuse (dalam 2 jam terakhir)
     */
    public function searchNota(Request $request)
    {
        $request->validate([
            'nomor_transaksi' => 'required|string'
        ]);

        try {
            $transaksi = Transaksi::with(['detailTransaksi.barang'])
                ->where('nomor_transaksi', $request->nomor_transaksi)
                ->where('id_user', Auth::id())
                ->where(function($query) {
                    // Untuk transaksi Progress: tidak ada time limit
                    // Untuk transaksi Selesai: maksimal 2 jam
                    $query->where('status_transaksi', 'Progress')
                        ->orWhere(function($subQuery) {
                            $subQuery->where('status_transaksi', 'Selesai')
                                    ->where('created_at', '>=', now()->subHours(2));
                        });
                })
                ->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan. Untuk transaksi selesai, maksimal 2 jam yang lalu.'
                ], 404);
            }

            // Cek apakah masih bisa ditambah item
            $canAddItems = true;
            $reason = '';

            if ($transaksi->status_transaksi === 'Progress') {
                $pendingCount = $transaksi->detailTransaksi->where('status_permintaan', 'Pending')->count();
                if ($pendingCount > 0) {
                    $canAddItems = false;
                    $reason = "Masih ada {$pendingCount} item yang menunggu validasi. Tunggu sampai semua item divalidasi dulu.";
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'nama_customer' => $transaksi->nama_customer,
                    'kendaraan' => $transaksi->kendaraan,
                    'jenis_transaksi' => $transaksi->jenis_transaksi,
                    'status_transaksi' => $transaksi->status_transaksi,
                    'total_harga' => $transaksi->total_harga,
                    'created_at' => $transaksi->created_at->format('d/m/Y H:i'),
                    'can_add_items' => $canAddItems,
                    'reason' => $reason,
                    'existing_items' => $transaksi->detailTransaksi->map(function($detail) {
                        return [
                            'nama_barang' => $detail->barang->nama_barang,
                            'kode_barang' => $detail->barang->kode_barang,
                            'qty' => $detail->qty,
                            'harga_satuan' => $detail->harga_satuan,
                            'subtotal' => $detail->subtotal,
                            'status_permintaan' => $detail->status_permintaan,
                            'can_edit' => false // Semua existing item gak bisa diedit
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching nota: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tambah item ke nota existing (updated version)
     */
    public function addItemsToExistingNota(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id_barang',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $transaksi = Transaksi::with('detailTransaksi')
                ->where('id_transaksi', $request->id_transaksi)
                ->where('id_user', Auth::id())
                ->where(function($query) {
                    $query->where('status_transaksi', 'Progress')
                        ->orWhere(function($subQuery) {
                            $subQuery->where('status_transaksi', 'Selesai')
                                    ->where('created_at', '>=', now()->subHours(2));
                        });
                })
                ->firstOrFail();

            // Double check - pastikan gak ada pending items kalau status Progress
            if ($transaksi->status_transaksi === 'Progress') {
                $pendingCount = $transaksi->detailTransaksi->where('status_permintaan', 'Pending')->count();
                if ($pendingCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Tidak bisa menambah item. Masih ada {$pendingCount} item yang menunggu validasi."
                    ], 400);
                }
            }

            $totalTambahan = 0;
            $newItemsCount = 0;

            // Add new items
            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['id_barang']);
                $subtotal = $barang->harga_jual * $item['qty'];
                $totalTambahan += $subtotal;

                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang' => $item['id_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                    'status_permintaan' => 'Pending'
                ]);

                $newItemsCount++;
            }

            // Update status transaksi kembali ke Progress kalau sebelumnya Selesai
            if ($transaksi->status_transaksi === 'Selesai') {
                $transaksi->update([
                    'status_transaksi' => 'Progress',
                    'keterangan' => 'Menunggu validasi tambahan barang dari gudang'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menambah {$newItemsCount} item ke nota {$transaksi->nomor_transaksi}! Request telah dikirim ke gudang untuk validasi.",
                'data' => [
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'total_items_baru' => $newItemsCount,
                    'total_harga_tambahan' => $totalTambahan,
                    'status_baru' => 'Progress'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print nota transaksi (returns view)
     */
    public function printNota($id)
    {
        try {
            $transaksi = Transaksi::with(['detailTransaksi.barang', 'kasir'])
                ->where('id_transaksi', $id)
                ->where('id_user', Auth::id())
                ->firstOrFail();

            // Hanya bisa print kalau transaksi sudah selesai
            if ($transaksi->status_transaksi !== 'Selesai') {
                abort(400, 'Hanya bisa print nota yang sudah selesai');
            }

            $transaksiData = [
                'nomor_transaksi' => $transaksi->nomor_transaksi,
                'tanggal_transaksi' => $transaksi->tanggal_transaksi->format('d/m/Y H:i'),
                'nama_customer' => $transaksi->nama_customer,
                'kendaraan' => $transaksi->kendaraan,
                'jenis_transaksi' => $transaksi->jenis_transaksi,
                'total_harga' => $transaksi->total_harga,
                'kasir_name' => $transaksi->kasir->name
            ];

            $items = $transaksi->detailTransaksi
                ->where('status_permintaan', 'Approved') // Hanya item yang approved
                ->map(function($detail) {
                    return [
                        'nama_barang' => $detail->barang->nama_barang,
                        'kode_barang' => $detail->barang->kode_barang,
                        'qty' => $detail->qty,
                        'harga_satuan' => $detail->harga_satuan,
                        'subtotal' => $detail->subtotal
                    ];
                })->values();

            return view('kasir.print-nota', compact('transaksiData', 'items'));

        } catch (\Exception $e) {
            abort(500, 'Error loading nota: ' . $e->getMessage());
        }
    }

    /**
     * Helper method: Check if transaction can accept new items
     */
    private function canAddItemsToTransaction($transaksi)
    {
        // Cek waktu expired (2 jam)
        if ($transaksi->status_transaksi === 'Selesai' && $transaksi->created_at < now()->subHours(2)) {
            return [
                'can_add' => false,
                'reason' => 'Nota sudah expired (lebih dari 2 jam). Buat transaksi baru.'
            ];
        }

        // Cek status transaksi
        if ($transaksi->status_transaksi === 'Progress') {
            $pendingCount = $transaksi->detailTransaksi->where('status_permintaan', 'Pending')->count();
            if ($pendingCount > 0) {
                return [
                    'can_add' => false,
                    'reason' => "Masih ada {$pendingCount} item yang menunggu validasi. Tunggu sampai semua item divalidasi."
                ];
            }
        }

        // Cek apakah transaksi batal
        if ($transaksi->status_transaksi === 'Dibatalkan') {
            return [
                'can_add' => false,
                'reason' => 'Tidak bisa menambah item ke transaksi yang dibatalkan.'
            ];
        }

        return [
            'can_add' => true,
            'reason' => ''
        ];
    }

    /**
     * Get recent transactions for autocomplete
     */
    public function getRecentTransactions()
    {
        try {
            $recentTransactions = Transaksi::where('id_user', Auth::id())
                ->where(function($query) {
                    $query->where('status_transaksi', 'Progress')
                        ->orWhere(function($subQuery) {
                            $subQuery->where('status_transaksi', 'Selesai')
                                    ->where('created_at', '>=', now()->subHours(2));
                        });
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(['nomor_transaksi', 'nama_customer', 'kendaraan', 'status_transaksi', 'created_at'])
                ->map(function($transaksi) {
                    return [
                        'nomor_transaksi' => $transaksi->nomor_transaksi,
                        'label' => "{$transaksi->nomor_transaksi} - {$transaksi->nama_customer} - {$transaksi->kendaraan}",
                        'status' => $transaksi->status_transaksi,
                        'time_ago' => $transaksi->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $recentTransactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading recent transactions: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
 * Cancel transaksi yang masih dalam status Progress
 */
    /**
 * Cancel transaksi yang masih dalam status Progress
 */
    public function cancelTransaksi($id)
    {
        try {
            DB::beginTransaction();

            $transaksi = Transaksi::with('detailTransaksi')
                ->where('id_transaksi', $id)
                ->where('id_user', Auth::id())
                ->where('status_transaksi', 'Progress')
                ->firstOrFail();

            // Update status transaksi menjadi Dibatalkan
            $transaksi->update([
                'status_transaksi' => 'Dibatalkan',
                'keterangan' => 'Transaksi dibatalkan oleh Service Advisor pada ' . now()->format('d/m/Y H:i'),
                'total_harga' => 0
            ]);

            // Update semua detail transaksi yang masih pending menjadi cancelled
            $cancelledCount = $transaksi->detailTransaksi()
                ->where('status_permintaan', 'Pending')
                ->update([
                    'status_permintaan' => 'Cancelled',
                    'keterangan' => 'Dibatalkan oleh Service Advisor'
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Transaksi {$transaksi->nomor_transaksi} berhasil dibatalkan. {$cancelledCount} item pending dibatalkan.",
                'data' => [
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'items_cancelled' => $cancelledCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}