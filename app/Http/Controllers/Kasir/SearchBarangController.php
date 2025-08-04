<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class SearchBarangController extends Controller
{
    /**
     * Tampilkan halaman search barang
     */
    public function index()
    {
        return view('kasir.search-barang');
    }

    /**
     * Search barang dengan filter (AJAX)
     */
    public function searchBarang(Request $request)
    {
       try {
            // HAPUS 'stok' dari with() karena service advisor gak perlu tau stok
            $query = Barang::with(['kategori']);

            // Search by nama, kode, merk, atau model
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('merk', 'like', "%{$search}%")
                    ->orWhere('model_tipe', 'like', "%{$search}%");
                });
            }

            // Filter by kategori
            if ($request->filled('kategori_id')) {
                $query->where('id_kategori', $request->kategori_id);
            }

            // Filter by harga range
            if ($request->filled('harga_min')) {
                $query->where('harga_jual', '>=', $request->harga_min);
            }
            if ($request->filled('harga_max')) {
                $query->where('harga_jual', '<=', $request->harga_max);
            }

            // HAPUS FILTER STOCK INI - Service advisor harus bisa lihat semua barang
            // $query->whereHas('stok', function($q) {
            //     $q->where('jumlah_stok', '>', 0);
            // });

            // Sorting
            $sortBy = $request->get('sort_by', 'nama_barang');
            $sortOrder = $request->get('sort_order', 'asc');
            
            if ($sortBy === 'harga') {
                $query->orderBy('harga_jual', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $barangs = $query->paginate($perPage);

            // Format data untuk response - REMOVE stock references
            $formattedData = $barangs->getCollection()->map(function($barang) {
                return [
                    'id_barang' => $barang->id_barang,
                    'kode_barang' => $barang->kode_barang,
                    'nama_lengkap' => $barang->nama_lengkap,
                    'nama_barang' => $barang->nama_barang,
                    'merk' => $barang->merk,
                    'model_tipe' => $barang->model_tipe,
                    'satuan' => $barang->satuan,
                    'harga_jual' => $barang->harga_jual,
                    'harga_jual_formatted' => 'Rp ' . number_format($barang->harga_jual, 0, ',', '.'),
                    'deskripsi' => $barang->deskripsi,
                    'kategori_badge' => $barang->kategori_badge,
                    'is_available' => true // Always true - service advisor gak perlu tau availability
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'pagination' => [
                    'current_page' => $barangs->currentPage(),
                    'per_page' => $barangs->perPage(),
                    'total' => $barangs->total(),
                    'last_page' => $barangs->lastPage(),
                    'from' => $barangs->firstItem(),
                    'to' => $barangs->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail barang by ID
     */
    public function getBarangDetail($id)
    {
        try {
            // HAPUS 'stok' dari with()
            $barang = Barang::with(['kategori'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id_barang' => $barang->id_barang,
                    'kode_barang' => $barang->kode_barang,
                    'nama_lengkap' => $barang->nama_lengkap,
                    'nama_barang' => $barang->nama_barang,
                    'merk' => $barang->merk,
                    'model_tipe' => $barang->model_tipe,
                    'satuan' => $barang->satuan,
                    'harga_jual' => $barang->harga_jual,
                    'harga_jual_formatted' => 'Rp ' . number_format($barang->harga_jual, 0, ',', '.'),
                    'deskripsi' => $barang->deskripsi,
                    'keterangan_detail' => $barang->keterangan_detail,
                    'kategori' => $barang->kategori ? [
                        'id' => $barang->kategori->id_kategori,
                        'nama' => $barang->kategori->nama_kategori,
                        'kode' => $barang->kategori->kode_kategori,
                        'badge' => $barang->kategori_badge
                    ] : null,
                    'is_available' => true // Always true - service advisor gak perlu tau availability
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get kategori options untuk filter
     */
    public function getKategoriOptions()
    {
        try {
            $kategoris = KategoriBarang::aktif()
                ->withCount('barang')
                ->orderBy('nama_kategori')
                ->get()
                ->map(function($kategori) {
                    return [
                        'id' => $kategori->id_kategori,
                        'nama' => $kategori->nama_kategori,
                        'kode' => $kategori->kode_kategori,
                        'jumlah_barang' => $kategori->barang_count,
                        'badge' => $kategori->display_badge
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $kategoris
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori'
            ], 500);
        }
    }

    /**
     * Get quick suggestions untuk autocomplete
     */
    public function getQuickSuggestions(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $suggestions = Barang::where(function($q) use ($query) {
                    $q->where('nama_barang', 'like', "%{$query}%")
                    ->orWhere('kode_barang', 'like', "%{$query}%")
                    ->orWhere('merk', 'like', "%{$query}%");
                })
                // HAPUS FILTER STOCK INI
                // ->whereHas('stok', function($q) {
                //     $q->where('jumlah_stok', '>', 0);
                // })
                ->with(['kategori'])
                ->limit(10)
                ->get()
                ->map(function($barang) {
                    return [
                        'id' => $barang->id_barang,
                        'text' => $barang->nama_lengkap,
                        'kode' => $barang->kode_barang,
                        'harga' => $barang->harga_jual,
                        'available' => true // Always true
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil suggestions'
            ], 500);
        }
    }

    /**
     * Get stats untuk dashboard search
     */
    public function getSearchStats()
    {
        try {
            $stats = [
                // UBAH: Total semua barang yang ada di sistem
                'total_barang' => Barang::count(),
                // UBAH: Service advisor gak perlu tau yang tersedia, just show total
                'barang_tersedia' => Barang::count(), // Same as total
                'total_kategori' => KategoriBarang::aktif()->count()
            ];

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
     * Get CSS class untuk status badge
     */
    private function getStatusBadgeClass($status)
    {
        return match($status) {
            'Aman' => 'px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full',
            'Perlu Restock' => 'px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded-full',
            'Habis' => 'px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full',
            default => 'px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full'
        };
    }
}