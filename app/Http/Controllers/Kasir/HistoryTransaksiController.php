<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HistoryTransaksiController extends Controller
{
    /**
     * Tampilkan halaman history transaksi
     */
    public function index()
    {
        return view('kasir.history-transaksi');
    }

    /**
     * Get history transaksi dengan filter (AJAX)
     */
    public function getHistory(Request $request)
    {
        try {
            $query = Transaksi::with(['kasir', 'detailTransaksi.barang']);

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('tanggal_transaksi', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('tanggal_transaksi', '<=', $request->date_to);
            }

            // Quick date filters
            if ($request->filled('quick_filter')) {
                switch ($request->quick_filter) {
                    case 'today':
                        $query->whereDate('tanggal_transaksi', today());
                        break;
                    case 'yesterday':
                        $query->whereDate('tanggal_transaksi', yesterday());
                        break;
                    case 'this_week':
                        $query->whereBetween('tanggal_transaksi', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ]);
                        break;
                    case 'last_week':
                        $query->whereBetween('tanggal_transaksi', [
                            now()->subWeek()->startOfWeek(),
                            now()->subWeek()->endOfWeek()
                        ]);
                        break;
                    case 'this_month':
                        $query->whereMonth('tanggal_transaksi', now()->month)
                              ->whereYear('tanggal_transaksi', now()->year);
                        break;
                    case 'last_month':
                        $query->whereMonth('tanggal_transaksi', now()->subMonth()->month)
                              ->whereYear('tanggal_transaksi', now()->subMonth()->year);
                        break;
                }
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status_transaksi', $request->status);
            }

            // Filter by jenis transaksi
            if ($request->filled('jenis_transaksi')) {
                $query->where('jenis_transaksi', $request->jenis_transaksi);
            }

            // Filter by total range
            if ($request->filled('total_min')) {
                $query->where('total_harga', '>=', $request->total_min);
            }
            if ($request->filled('total_max')) {
                $query->where('total_harga', '<=', $request->total_max);
            }

            // Search by nomor transaksi atau customer
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nomor_transaksi', 'like', "%{$search}%")
                      ->orWhere('nama_customer', 'like', "%{$search}%")
                      ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            // Filter by kasir (hanya jika bukan kasir sendiri)
            if ($request->filled('kasir_id') && auth()->user()->role !== 'Kasir') {
                $query->where('id_user', $request->kasir_id);
            } else {
                // Jika kasir, hanya tampilkan transaksi sendiri
                if (auth()->user()->role === 'Kasir') {
                    $query->where('id_user', auth()->id());
                }
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'tanggal_transaksi');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $transaksis = $query->paginate($perPage);

            // Format data untuk response
            $formattedData = $transaksis->getCollection()->map(function($transaksi) {
                return [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'nomor_transaksi' => $transaksi->nomor_transaksi,
                    'formatted_nomor' => $transaksi->formatted_nomor,
                    'tanggal_transaksi' => $transaksi->tanggal_transaksi->format('Y-m-d H:i:s'),
                    'tanggal_formatted' => $transaksi->tanggal_transaksi->format('d/m/Y H:i'),
                    'tanggal_readable' => $transaksi->tanggal_transaksi->diffForHumans(),
                    'nama_customer' => $transaksi->nama_customer,
                    'kendaraan' => $transaksi->kendaraan,
                    'total_harga' => $transaksi->total_harga,
                    'total_formatted' => 'Rp ' . number_format($transaksi->total_harga, 0, ',', '.'),
                    'jenis_transaksi' => $transaksi->jenis_transaksi,
                    'status_transaksi' => $transaksi->status_transaksi,
                    'status_badge_class' => $this->getStatusBadgeClass($transaksi->status_transaksi),
                    'kasir_nama' => $transaksi->kasir->name ?? 'Unknown',
                    'jumlah_item' => $transaksi->detailTransaksi->count(),
                    'total_qty' => $transaksi->detailTransaksi->sum('qty'),
                    'keterangan' => $transaksi->keterangan,
                    'can_view_detail' => true
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $transaksis->currentPage(),
                    'per_page' => $transaksis->perPage(),
                    'total' => $transaksis->total(),
                    'last_page' => $transaksis->lastPage(),
                    'from' => $transaksis->firstItem(),
                    'to' => $transaksis->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail transaksi by ID
     */
    public function getDetailTransaksi($id)
    {
        try {
            $transaksi = Transaksi::with([
                'kasir',
                'detailTransaksi.barang.kategori'
            ])->findOrFail($id);

            // Format detail items
            $detailItems = $transaksi->detailTransaksi->map(function($detail) {
                return [
                    'id_detail' => $detail->id_detail,
                    'nama_barang' => $detail->barang->nama_lengkap,
                    'kode_barang' => $detail->barang->kode_barang,
                    'kategori' => $detail->barang->kategori->nama_kategori ?? 'Uncategorized',
                    'qty' => $detail->qty,
                    'satuan' => $detail->barang->satuan,
                    'harga_satuan' => $detail->harga_satuan,
                    'harga_satuan_formatted' => 'Rp ' . number_format($detail->harga_satuan, 0, ',', '.'),
                    'subtotal' => $detail->subtotal,
                    'subtotal_formatted' => 'Rp ' . number_format($detail->subtotal, 0, ',', '.'),
                    'status_permintaan' => $detail->status_permintaan,
                    'status_badge_class' => $this->getDetailStatusBadgeClass($detail->status_permintaan)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'transaksi' => [
                        'id_transaksi' => $transaksi->id_transaksi,
                        'nomor_transaksi' => $transaksi->nomor_transaksi,
                        'formatted_nomor' => $transaksi->formatted_nomor,
                        'tanggal_transaksi' => $transaksi->tanggal_transaksi->format('Y-m-d H:i:s'),
                        'tanggal_formatted' => $transaksi->tanggal_transaksi->format('d/m/Y H:i'),
                        'nama_customer' => $transaksi->nama_customer,
                        'kendaraan' => $transaksi->kendaraan,
                        'total_harga' => $transaksi->total_harga,
                        'total_formatted' => 'Rp ' . number_format($transaksi->total_harga, 0, ',', '.'),
                        'jenis_transaksi' => $transaksi->jenis_transaksi,
                        'status_transaksi' => $transaksi->status_transaksi,
                        'kasir_nama' => $transaksi->kasir->name ?? 'Unknown',
                        'keterangan' => $transaksi->keterangan
                    ],
                    'detail_items' => $detailItems,
                    'summary' => [
                        'jumlah_item' => $detailItems->count(),
                        'total_qty' => $detailItems->sum('qty'),
                        'subtotal' => $transaksi->total_harga,
                        'subtotal_formatted' => 'Rp ' . number_format($transaksi->total_harga, 0, ',', '.')
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get statistik transaksi untuk dashboard
     */
    public function getTransaksiStats(Request $request)
    {
        try {
            $period = $request->get('period', 'today');
            $userId = auth()->user()->role === 'Kasir' ? auth()->id() : null;

            $query = Transaksi::query();
            if ($userId) {
                $query->where('id_user', $userId);
            }

            // Apply period filter
            switch ($period) {
                case 'today':
                    $query->whereDate('tanggal_transaksi', today());
                    break;
                case 'yesterday':
                    $query->whereDate('tanggal_transaksi', yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('tanggal_transaksi', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('tanggal_transaksi', now()->month)
                          ->whereYear('tanggal_transaksi', now()->year);
                    break;
            }

            $stats = [
                'total_transaksi' => $query->count(),
                'total_penjualan' => $query->sum('total_harga'),
                'transaksi_selesai' => $query->where('status_transaksi', 'Selesai')->count(),
                'transaksi_progress' => $query->where('status_transaksi', 'Progress')->count(),
                'rata_rata_transaksi' => $query->avg('total_harga') ?? 0,
                'total_item_terjual' => DetailTransaksi::whereHas('transaksi', function($q) use ($query) {
                    $q->whereIn('id_transaksi', $query->pluck('id_transaksi'));
                })->sum('qty')
            ];

            // Format currency
            $stats['total_penjualan_formatted'] = 'Rp ' . number_format($stats['total_penjualan'], 0, ',', '.');
            $stats['rata_rata_transaksi_formatted'] = 'Rp ' . number_format($stats['rata_rata_transaksi'], 0, ',', '.');

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik'
            ], 500);
        }
    }

    /**
     * Export history transaksi (placeholder)
     */
    public function exportHistory(Request $request)
    {
        // TODO: Implement export functionality
        return response()->json([
            'success' => false,
            'message' => 'Export feature belum diimplementasi'
        ]);
    }

    /**
     * Get CSS class untuk status transaksi badge
     */
    private function getStatusBadgeClass($status)
    {
        return match($status) {
            'Selesai' => 'px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full',
            'Progress' => 'px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded-full',
            'Cancelled' => 'px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full',
            default => 'px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full'
        };
    }

    /**
     * Get CSS class untuk status detail transaksi badge
     */
    private function getDetailStatusBadgeClass($status)
    {
        return match($status) {
            'Approved' => 'px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full',
            'Pending' => 'px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full',
            'Rejected' => 'px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full',
            default => 'px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full'
        };
    }
}