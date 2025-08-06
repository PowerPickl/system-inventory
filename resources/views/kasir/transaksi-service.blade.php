<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Service Request</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- jQuery for AJAX -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
    #search-results {
        scroll-behavior: smooth;
        overflow-y: auto !important;
        max-height: calc(80vh - 200px); /* Ensure scrollable area */
    }

    /* Better scrollbar */
    #search-results::-webkit-scrollbar {
        width: 8px;
    }

    #search-results::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    #search-results::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    #search-results::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    </style>



</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-purple-800 shadow-lg">
            <!-- Logo/Brand -->
            <div class="p-6 border-b border-purple-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white font-semibold">Bengkel Inventory</h3>
                        <p class="text-purple-400 text-sm">Service Advisor Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6">
                <div class="px-3">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard.kasir') }}" 
                       class="flex items-center px-3 py-2 text-purple-300 hover:text-white hover:bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v10H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Transaksi Service -->
                    <a href="{{ route('kasir.transaksi-service') }}" 
                       class="flex items-center px-3 py-2 text-white bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Service Request
                    </a>

                    <!-- Search Barang -->
                    <a href="{{ route('kasir.search-barang') }}" class="flex items-center px-3 py-2 text-purple-300 hover:text-white hover:bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Katalog Barang
                    </a>

                    <!-- History Transaksi -->
                    <a href="{{ route('kasir.history-transaksi') }}" class="flex items-center px-3 py-2 text-purple-300 hover:text-white hover:bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Service History
                    </a>
                </div>
            </nav>

            <!-- User Info & Logout -->
            <div class="absolute bottom-0 w-64 p-4 border-t border-purple-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-white text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-purple-400 text-xs">Service Advisor</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-purple-400 hover:text-white transition-colors duration-200" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            
                            <h1 class="text-2xl font-semibold text-gray-900">Service Request</h1>
                            <p class="text-gray-600 text-sm">Manage service kendaraan dan kebutuhan sparepart</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Quick Actions -->
                            <button id="btn-refresh" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh Status
                            </button>
                            
                            <!-- Date/Time -->
                            <div class="text-sm text-gray-600">
                                <div id="current-time-kasir"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        
          
        <!-- Form Transaksi Baru -->
        <form id="form-transaksi" class="space-y-4">
            @csrf
            
            <!-- Row 1: Service & Customer Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori Service *</label>
                    <select name="kategori_service" id="kategori_service" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Pilih kategori service...</option>
                        <option value="Service Berkala">Service Berkala</option>
                        <option value="Tune Up">Tune Up</option>
                        <option value="Ganti Oli">Ganti Oli</option>
                        <option value="Service CVT">Service CVT</option>
                        <option value="Service Rem">Service Rem</option>
                        <option value="Service Kopling">Service Kopling</option>
                        <option value="Overhaul Mesin">Overhaul Mesin</option>
                        <option value="Service Kelistrikan">Service Kelistrikan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Customer *</label>
                    <input type="text" name="nama_customer" id="nama_customer" required 
                           placeholder="Masukkan nama customer..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Info Kendaraan *</label>
                    <input type="text" name="kendaraan" id="kendaraan" required 
                           placeholder="Honda Beat 2020 - B1234XYZ"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <!-- Barang yang Dibutuhkan -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">Barang yang Dibutuhkan *</label>
                    <button type="button" id="btn-cari-barang" 
                            class="flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Barang
                    </button>
                </div>

                <!-- Selected Items Container -->
                <div id="selected-items" class="space-y-2 min-h-[100px] border-2 border-dashed border-gray-200 rounded-lg p-4">
                    <div id="empty-state" class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p>Belum ada barang yang dipilih</p>
                        <p class="text-sm">Klik "Cari Barang" untuk menambah item</p>
                    </div>
                </div>

                <!-- Total Estimasi -->
                <div id="total-section" class="hidden mt-4 p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Total Estimasi:</span>
                        <span id="total-amount" class="text-lg font-bold text-green-600">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-3">
                <button type="submit" id="btn-submit" 
                        class="flex-1 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:bg-gray-300 disabled:cursor-not-allowed font-medium transition-colors">
                    🚀 Kirim Request ke Gudang
                </button>
                <button type="button" id="btn-reset" 
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Tambah Item ke Nota Existing -->
    <!-- <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Tambah Item ke Nota Existing
        </h3>
        <div class="flex gap-3">
            <input type="text" id="search-nota" placeholder="Masukkan Nomor Transaksi (contoh: TRX20250101001)" 
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <button id="btn-cari-nota" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                Cari Nota
            </button>
        </div>
    </div> -->

    <!-- Status Transaksi Aktif -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Transaksi Menunggu Validasi
        </h3>
        
        <div id="active-transactions">
            @if($activeTransactions->count() > 0)
                @foreach($activeTransactions as $transaksi)
                <div class="border rounded-lg p-4 mb-3 bg-orange-50" data-transaction-id="{{ $transaksi->id_transaksi }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $transaksi->nomor_transaksi }}</h4>
                            <p class="text-sm text-gray-600">{{ $transaksi->nama_customer }} - {{ $transaksi->kendaraan }}</p>
                            <p class="text-sm text-gray-500">{{ $transaksi->jenis_transaksi }}</p>
                            <div class="mt-2">
                                <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">
                                    {{ $transaksi->detailTransaksi->where('status_permintaan', 'Pending')->count() }} pending, 
                                    {{ $transaksi->detailTransaksi->where('status_permintaan', 'Approved')->count() }} approved,
                                    {{ $transaksi->detailTransaksi->where('status_permintaan', 'Rejected')->count() }} rejected
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn-detail px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                                    data-id="{{ $transaksi->id_transaksi }}">
                                Detail
                            </button>

                            <!-- NEW: Cancel Button -->
                            <button class="btn-cancel px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                    data-id="{{ $transaksi->id_transaksi }}"
                                    title="Batalkan transaksi ini">
                                ❌ Cancel
                            </button>


                            @if($transaksi->detailTransaksi->where('status_permintaan', 'Pending')->count() === 0)
                            <button class="btn-complete px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm"
                                    data-id="{{ $transaksi->id_transaksi }}">
                                Selesaikan
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-gray-500 text-center py-8">Tidak ada transaksi yang menunggu validasi</p>
            @endif
        </div>
    </div>

    <!-- Transaksi Hari Ini -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Transaksi Selesai Hari Ini
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Transaksi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($todayTransactions as $transaksi)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $transaksi->nomor_transaksi }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $transaksi->nama_customer }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $transaksi->jenis_transaksi }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-green-600">{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $transaksi->created_at->format('H:i') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <button class="btn-detail text-blue-600 hover:text-blue-800 mr-2" data-id="{{ $transaksi->id_transaksi }}">Detail</button>
                            <button class="btn-add-items text-orange-600 hover:text-orange-800" data-id="{{ $transaksi->id_transaksi }}">+ Item</button>
                            <button class="btn-print text-green-600 hover:text-green-800" data-id="{{ $transaksi->id_transaksi }}">🖨️ Print</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada transaksi selesai hari ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Cari Barang -->
<div id="modal-search" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg w-full max-w-2xl h-[80vh] flex flex-col">
            <!-- Header - fixed height -->
            <div class="flex items-center justify-between p-6 pb-4 border-b flex-shrink-0">
                <h3 class="text-lg font-semibold">Cari Barang</h3>
                <button id="btn-close-search" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Search input - fixed height -->
            <div class="relative p-6 pb-4 border-b flex-shrink-0">
                <svg class="absolute left-9 top-7 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="search-input" placeholder="Cari nama barang atau kode..." 
                       class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <!-- Results container - scrollable content -->
            <div class="flex-1 overflow-hidden">
                <div id="search-results" class="h-full overflow-y-auto p-6 pt-4 space-y-2">
                </div>
            </div>
            
            <!-- Footer - fixed height -->
            <div class="flex justify-end p-6 pt-4 border-t flex-shrink-0">
                <button id="btn-cancel-search" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="modal-detail" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[80vh] overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Detail Transaksi</h3>
                <button id="btn-close-detail" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="detail-content" class="overflow-y-auto max-h-[60vh]">
                <!-- Detail content will be populated here -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Items to Existing Transaction -->
<div id="modal-add-items" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Tambah Item ke Transaksi</h3>
                <button id="btn-close-add-items" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="transaction-info" class="mb-4 p-3 bg-blue-50 rounded">
                <!-- Transaction info will be populated here -->
            </div>
            
            <div id="add-items-form">
                <div class="mb-4">
                    <button type="button" id="btn-add-search" 
                            class="w-full py-2 border-2 border-dashed border-blue-300 rounded-lg hover:border-blue-500 text-blue-600 font-medium">
                        + Pilih Barang Tambahan
                    </button>
                </div>
                
                <div id="additional-items" class="space-y-2 mb-4">
                    <!-- Additional items will be shown here -->
                </div>
                
                <div class="flex justify-end gap-2">
                    <button id="btn-cancel-add" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                    <button id="btn-submit-add" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:bg-gray-300" disabled>
                        Request Tambahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

                </div>
            </main>
        </div>
    </div>


   
   <script>
        // Update current time
        function updateTimeKasir() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('current-time-kasir').textContent = now.toLocaleDateString('id-ID', options);
        }
        
        updateTimeKasir();
        setInterval(updateTimeKasir, 60000); // Update every minute

        // Print nota function (global scope)
        function printNota(transactionId) {
            window.open(`/kasir/transaksi-service/${transactionId}/print`, '_blank');
        }
    
$(document).ready(function() {
    let selectedItems = [];
    let currentTransactionId = null;
    
    // ========== VARIABLES FOR EXISTING NOTA ==========
    let currentExistingTransaction = null;
    let additionalItems = [];
    
    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }
    
    // Also trigger validation when items change
    function updateTotal() {
        const total = selectedItems.reduce((sum, item) => sum + (item.harga * item.qty), 0);
        $('#total-amount').text(formatCurrency(total));
        
        if (selectedItems.length > 0) {
            $('#total-section').removeClass('hidden');
            $('#empty-state').hide();
        } else {
            $('#total-section').addClass('hidden');
            $('#empty-state').show();
        }
        
        updateSubmitButton(); // TAMBAH INI - trigger validation setiap total berubah
    }
    
    // Update submit button state
    function updateSubmitButton() {
        const kategoriValid = $('#kategori_service').val() && $('#kategori_service').val().trim() !== '';
        const customerValid = $('#nama_customer').val() && $('#nama_customer').val().trim() !== '';
        const kendaraanValid = $('#kendaraan').val() && $('#kendaraan').val().trim() !== '';
        const itemsValid = selectedItems.length > 0;
        
        const isValid = kategoriValid && customerValid && kendaraanValid && itemsValid;
        
        console.log('Form validation check:', {
            kategori: kategoriValid,
            customer: customerValid,
            kendaraan: kendaraanValid,
            items: itemsValid,
            itemCount: selectedItems.length,
            isValid: isValid
        });
        
        const submitBtn = $('#btn-submit');
        
        if (isValid) {
            submitBtn.prop('disabled', false)
                    .removeClass('bg-gray-300 cursor-not-allowed')
                    .addClass('bg-purple-600 hover:bg-purple-700')
                    .css('opacity', '1');
        } else {
            submitBtn.prop('disabled', true)
                    .removeClass('bg-purple-600 hover:bg-purple-700')
                    .addClass('bg-gray-300 cursor-not-allowed')
                    .css('opacity', '0.6');
        }
    }
    
    // Render selected items
    function renderSelectedItems() {
        const container = $('#selected-items');
        
        console.log('Rendering items, count:', selectedItems.length);
        
        // Clear container first
        container.empty();
        
        if (selectedItems.length === 0) {
            console.log('No items, showing empty state');
            container.html(`
                <div id="empty-state" class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p>Belum ada barang yang dipilih</p>
                    <p class="text-sm">Klik "Cari Barang" untuk menambah item</p>
                </div>
            `);
            $('#total-section').addClass('hidden');
            updateSubmitButton();
            return;
        }
        
        const itemsHtml = selectedItems.map((item, index) => `
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg" data-item-index="${index}">
                <div>
                    <p class="font-medium">${item.nama}</p>
                    <p class="text-sm text-gray-600">${item.kode}</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="number" min="1" value="${item.qty}" 
                        class="w-16 px-2 py-1 border rounded text-sm qty-input" data-index="${index}">
                    <span class="text-sm text-gray-600">x ${formatCurrency(item.harga)}</span>
                    <span class="font-medium text-green-600">${formatCurrency(item.harga * item.qty)}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-item" data-index="${index}" title="Hapus item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');
        
        container.html(itemsHtml);
        $('#total-section').removeClass('hidden');
        updateTotal();
    }
    
    // Search barang
    function searchBarang(query = '') {
        // Show loading state
        $('#search-results').html(`
        <div class="text-center py-8 text-gray-500">
            <svg class="animate-spin w-8 h-8 mx-auto mb-2 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p>Memuat barang...</p>
        </div>
    `);

        $.get('/kasir/transaksi-service/search-barang', { search: query })
            .done(function(data) {
                if (data.length === 0) {
                    $('#search-results').html(`
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Tidak ada barang ditemukan</p>
                            <p class="text-sm">Coba gunakan kata kunci lain</p>
                        </div>
                    `);
                    return;
                }

                const resultsHtml = data.map(item => `
                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors cursor-pointer group">
                        <div class="flex-1 min-w-0 mr-4">
                            <p class="font-medium text-gray-900 truncate group-hover:text-blue-600">${item.nama}</p>
                            <p class="text-sm text-gray-500 truncate">${item.kode} - ${item.satuan}</p>
                            <p class="text-sm text-green-600 font-medium">${formatCurrency(item.harga)}</p>
                        </div>
                        <button type="button" 
                                class="add-item-btn flex-shrink-0 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm transition-colors shadow-sm hover:shadow-md" 
                                data-item='${JSON.stringify(item)}'>
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Pilih
                        </button>
                    </div>
                `).join('');
                
                $('#search-results').html(resultsHtml);
            })
            .fail(function() {
                $('#search-results').html(`
                    <div class="text-center py-8 text-red-500">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>Error loading data</p>
                        <p class="text-sm">Silakan coba lagi</p>
                    </div>
                `);
            });
    }

    // ========== EXISTING NOTA FUNCTIONS ==========
    
    // Show existing nota modal
    function showExistingNotaModal(data) {
        const infoHtml = `
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p><strong>No. Transaksi:</strong> ${data.nomor_transaksi}</p>
                    <p><strong>Customer:</strong> ${data.nama_customer}</p>
                    <p><strong>Kendaraan:</strong> ${data.kendaraan}</p>
                </div>
                <div>
                    <p><strong>Service:</strong> ${data.jenis_transaksi}</p>
                    <p><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded-full ${data.status_transaksi === 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">${data.status_transaksi}</span></p>
                    <p><strong>Total Saat Ini:</strong> <span class="font-bold text-green-600">${formatCurrency(data.total_harga)}</span></p>
                </div>
            </div>
            
            <div class="mt-4 p-3 ${data.can_add_items ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'} rounded">
                <p class="text-sm ${data.can_add_items ? 'text-green-700' : 'text-red-700'}">
                    ${data.can_add_items ? '✅ Nota ini bisa ditambah item baru' : '❌ ' + data.reason}
                </p>
            </div>

            <div class="mt-4">
                <h4 class="font-semibold mb-2">Item yang Sudah Ada (${data.existing_items.length} items):</h4>
                <div class="max-h-32 overflow-y-auto border rounded">
                    ${data.existing_items.map(item => `
                        <div class="flex justify-between items-center p-2 border-b text-sm">
                            <div>
                                <span class="font-medium">${item.nama_barang}</span>
                                <span class="text-gray-500">x${item.qty}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-xs rounded-full ${
                                    item.status_permintaan === 'Approved' ? 'bg-green-100 text-green-800' :
                                    item.status_permintaan === 'Rejected' ? 'bg-red-100 text-red-800' :
                                    'bg-yellow-100 text-yellow-800'
                                }">${item.status_permintaan}</span>
                                <span class="font-medium">${formatCurrency(item.subtotal)}</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        $('#transaction-info').html(infoHtml);
        
        // Reset additional items
        additionalItems = [];
        renderAdditionalItems();
        
        // Enable/disable add button based on can_add_items
        $('#btn-add-search').prop('disabled', !data.can_add_items);
        $('#btn-submit-add').prop('disabled', true);
        
        $('#modal-add-items').removeClass('hidden');
    }

    // Render additional items
    function renderAdditionalItems() {
        const container = $('#additional-items');
        
        if (additionalItems.length === 0) {
            container.html('<p class="text-gray-500 text-center py-4">Belum ada item tambahan dipilih</p>');
            $('#btn-submit-add').prop('disabled', true);
            return;
        }

        const itemsHtml = additionalItems.map((item, index) => `
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div>
                    <p class="font-medium">${item.nama}</p>
                    <p class="text-sm text-gray-600">${item.kode} - Stok: ${item.stok}</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="number" min="1" max="${item.stok}" value="${item.qty}" 
                           class="w-16 px-2 py-1 border rounded text-sm additional-qty-input" data-index="${index}">
                    <span class="text-sm text-gray-600">x ${formatCurrency(item.harga)}</span>
                    <span class="font-medium text-green-600">${formatCurrency(item.harga * item.qty)}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 remove-additional-item" data-index="${index}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');
        
        container.html(itemsHtml);
        $('#btn-submit-add').prop('disabled', false);
    }

    // ========== EVENT HANDLERS ==========
    
    // Handle enter key di search nota
    $('#search-nota').keypress(function(e) {
        if (e.which === 13) { // Enter key
            $('#btn-cari-nota').click();
        }
    });

    // Search existing nota
    $('#btn-cari-nota').click(function() {
        const nomorTransaksi = $('#search-nota').val().trim();
        
        if (!nomorTransaksi) {
            alert('Masukkan nomor transaksi terlebih dahulu');
            return;
        }

        $(this).prop('disabled', true).text('Mencari...');

        $.post('/kasir/transaksi-service/search-nota', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            nomor_transaksi: nomorTransaksi
        })
        .done(function(response) {
            if (response.success) {
                currentExistingTransaction = response.data;
                showExistingNotaModal(response.data);
            } else {
                alert(response.message);
            }
        })
        .fail(function(xhr) {
            const response = xhr.responseJSON;
            alert(response?.message || 'Error searching nota');
        })
        .always(function() {
            $('#btn-cari-nota').prop('disabled', false).text('Cari Nota');
        });
    });

    // Handle add search for additional items
    $('#btn-add-search').click(function() {
        if ($(this).prop('disabled')) return;
        
        $('#modal-search').removeClass('hidden');
        $('#search-input').focus();
        searchBarang();
    });

    // Event Handlers - Original + New
    $('#btn-cari-barang').click(function() {
        $('#modal-search').removeClass('hidden');
        $('#search-input').focus();
        // LANGSUNG LOAD SEMUA BARANG TANPA PERLU KETIK
        searchBarang(); // Hapus parameter kosong
    });
    
    $('#btn-close-search, #btn-cancel-search').click(function() {
        $('#modal-search').addClass('hidden');
        $('#search-input').val('');
    });
    
    $('#search-input').on('input', function() {
        const query = $(this).val();
        searchBarang(query);
    });
    
    // SMART Add item to selection (handles both regular and additional items)
    $(document).on('click', '.add-item-btn', function() {
        const item = JSON.parse($(this).attr('data-item'));
    
        // Check if we're in additional items mode
        if (!$('#modal-add-items').hasClass('hidden')) {
            // Add to additional items
            const existingIndex = additionalItems.findIndex(ai => ai.id === item.id);
            
            if (existingIndex >= 0) {
                additionalItems[existingIndex].qty += 1;
            } else {
                additionalItems.push({
                    id: item.id,
                    kode: item.kode,
                    nama: item.nama,
                    harga: item.harga,
                    qty: 1
                });
            }
            
            renderAdditionalItems();
            $('#modal-search').addClass('hidden');
            $('#search-input').val('');
        } else {
            // Regular add item logic
            const existingIndex = selectedItems.findIndex(si => si.id === item.id);
            
            if (existingIndex >= 0) {
                selectedItems[existingIndex].qty += 1;
            } else {
                selectedItems.push({
                    id: item.id,
                    kode: item.kode,
                    nama: item.nama,
                    harga: item.harga,
                    qty: 1
                });
            }
            
            renderSelectedItems();
            $('#modal-search').addClass('hidden');
            $('#search-input').val('');
        }
    });

    // Handle additional item quantity change
    $(document).on('change', '.qty-input', function(e) {
        e.preventDefault();
        
        const index = parseInt($(this).data('index'));
        const newQty = parseInt($(this).val()) || 1;
        
        console.log('Updating qty for index:', index, 'to:', newQty); // Debug log
        
        if (index >= 0 && index < selectedItems.length) {
            if (newQty < 1) {
                // Remove item if qty < 1
                selectedItems.splice(index, 1);
            } else {
                selectedItems[index].qty = newQty;
            }
            renderSelectedItems();
        }
    });

    
    // Remove item - FIXED: Proper event handling and array cleanup
    $(document).on('click', '.remove-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const index = parseInt($(this).data('index'));
        console.log('Delete clicked - Index:', index, 'Array before:', selectedItems.length);
        
        if (index >= 0 && index < selectedItems.length) {
            const removedItem = selectedItems[index];
            selectedItems.splice(index, 1);
            console.log('Removed item:', removedItem.nama, 'Array after:', selectedItems.length);
            
            // Force re-render
            renderSelectedItems();
            
            // Double check if empty
            if (selectedItems.length === 0) {
                console.log('No items left, showing empty state');
                $('#empty-state').show();
                $('#total-section').addClass('hidden');
            }
        } else {
            console.error('Invalid index for delete:', index, 'Array length:', selectedItems.length);
        }
    });

    // Submit additional items
    $('#btn-submit-add').click(function() {
        if (additionalItems.length === 0) {
            alert('Pilih minimal 1 item tambahan!');
            return;
        }

        const requestData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id_transaksi: currentExistingTransaction.id_transaksi,
            items: additionalItems.map(item => ({
                id_barang: item.id,
                qty: item.qty
            }))
        };

        $(this).prop('disabled', true).text('Memproses...');

        $.post('/kasir/transaksi-service/add-to-existing', requestData)
            .done(function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#modal-add-items').addClass('hidden');
                    $('#search-nota').val('');
                    location.reload();
                } else {
                    alert(response.message);
                }
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Terjadi kesalahan saat menambah item');
            })
            .always(function() {
                $('#btn-submit-add').prop('disabled', false).text('Request Tambahan');
            });
    });

    // Close additional items modal
    $('#btn-close-add-items, #btn-cancel-add').click(function() {
        $('#modal-add-items').addClass('hidden');
        additionalItems = [];
        currentExistingTransaction = null;
        $('#search-nota').val('');
    });

    // Print nota handler
    $(document).on('click', '.btn-print', function() {
        const id = $(this).data('id');
        printNota(id);
    });

    // ========== ORIGINAL HANDLERS (UNCHANGED) ==========
    
    // Remove item
    $(document).on('click', '.remove-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const index = parseInt($(this).data('index'));
        console.log('Removing item at index:', index); // Debug log
        
        if (index >= 0 && index < selectedItems.length) {
            selectedItems.splice(index, 1);
            renderSelectedItems();
        }
    });

    // Cancel transaction
    $(document).on('click', '.btn-cancel', function() {
        const id = $(this).data('id');
        const transactionElement = $(this).closest('[data-transaction-id]');
        const nomorTransaksi = transactionElement.find('h4').text();
        
        if (confirm(`Yakin ingin MEMBATALKAN transaksi ${nomorTransaksi}?\n\nSemua item yang masih pending akan dibatalkan dan tidak bisa di-undo!`)) {
            $(this).prop('disabled', true).text('Membatalkan...');
            
            $.post(`/kasir/transaksi-service/${id}/cancel`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                if (response.success) {
                    alert(response.message);
                    // Remove dari active transactions
                    transactionElement.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if no more active transactions
                        if ($('#active-transactions [data-transaction-id]').length === 0) {
                            $('#active-transactions').html('<p class="text-gray-500 text-center py-8">Tidak ada transaksi yang menunggu validasi</p>');
                        }
                    });
                } else {
                    alert(response.message || 'Gagal membatalkan transaksi');
                }
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Error cancelling transaction');
            })
            .always(function() {
                // Reset button state jika masih ada
                $('.btn-cancel[data-id="' + id + '"]').prop('disabled', false).text('❌ Cancel');
            });
        }
    });
    
    // Handle quantity change for regular items
    $(document).on('change', '.qty-input', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const index = parseInt($(this).data('index'));
        const newQty = parseInt($(this).val()) || 1;
        
        console.log('Qty change - Index:', index, 'New Qty:', newQty, 'Array length:', selectedItems.length);
        
        if (index >= 0 && index < selectedItems.length) {
            if (newQty < 1) {
                // Remove item if qty < 1
                selectedItems.splice(index, 1);
                console.log('Item removed. New array length:', selectedItems.length);
            } else {
                selectedItems[index].qty = newQty;
                console.log('Qty updated for item:', selectedItems[index].nama);
            }
            renderSelectedItems();
        } else {
            console.error('Invalid index for qty change:', index);
        }
    });

    // Handle quantity change for additional items (separate handler)
    $(document).on('change', '.additional-qty-input', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const index = parseInt($(this).data('index'));
        const newQty = parseInt($(this).val()) || 1;
        
        if (index >= 0 && index < additionalItems.length) {
            if (newQty < 1) {
                additionalItems.splice(index, 1);
            } else {
                additionalItems[index].qty = newQty;
            }
            renderAdditionalItems();
        }
    });
    
    // Form validation
    $('#kategori_service, #nama_customer, #kendaraan').on('input change keyup blur', function() {
        console.log('Form field changed:', $(this).attr('id'), '=', $(this).val()); // Debug log
        updateSubmitButton();
    });
    
    // Submit form
    $('#form-transaksi').submit(function(e) {
        e.preventDefault();
        
        if (selectedItems.length === 0) {
            alert('Pilih minimal 1 barang!');
            return;
        }
        
        const formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            kategori_service: $('#kategori_service').val(),
            nama_customer: $('#nama_customer').val(),
            kendaraan: $('#kendaraan').val(),
            items: selectedItems.map(item => ({
                id_barang: item.id,
                qty: item.qty
            }))
        };
        
        $('#btn-submit').prop('disabled', true).text('Memproses...');
        
        $.post('/kasir/transaksi-service/create', formData)
            .done(function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#form-transaksi')[0].reset();
                    selectedItems = [];
                    renderSelectedItems();
                    location.reload();
                } else {
                    alert(response.message || 'Terjadi kesalahan');
                }
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.errors) {
                    const errors = Object.values(response.errors).flat().join('\n');
                    alert('Validation Error:\n' + errors);
                } else {
                    alert(response?.message || 'Terjadi kesalahan saat menyimpan transaksi');
                }
            })
            .always(function() {
                $('#btn-submit').prop('disabled', false).text('🚀 Kirim Request ke Gudang');
            });
    });
    
    // Reset form
    $('#btn-reset').click(function() {
        if (confirm('Reset semua data?')) {
            $('#form-transaksi')[0].reset();
            selectedItems = [];
            renderSelectedItems();
        }
    });
    
    // Detail transaction
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        
        $.get(`/kasir/transaksi-service/${id}/detail`)
            .done(function(response) {
                if (response.success) {
                    const data = response.data;
                    const itemsHtml = data.items.map(item => `
                        <tr>
                            <td class="px-4 py-2 border">${item.kode_barang}</td>
                            <td class="px-4 py-2 border">${item.nama_barang}</td>
                            <td class="px-4 py-2 border text-center">${item.qty}</td>
                            <td class="px-4 py-2 border text-right">${formatCurrency(item.harga_satuan)}</td>
                            <td class="px-4 py-2 border text-right">${formatCurrency(item.subtotal)}</td>
                            <td class="px-4 py-2 border text-center">
                                <span class="px-2 py-1 text-xs rounded-full ${
                                    item.status_permintaan === 'Approved' ? 'bg-green-100 text-green-800' :
                                    item.status_permintaan === 'Rejected' ? 'bg-red-100 text-red-800' :
                                    'bg-yellow-100 text-yellow-800'
                                }">${item.status_permintaan}</span>
                            </td>
                            <td class="px-4 py-2 border text-center">${item.stok_tersedia}</td>
                        </tr>
                    `).join('');
                    
                    const detailHtml = `
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p><strong>No. Transaksi:</strong> ${data.nomor_transaksi}</p>
                                <p><strong>Tanggal:</strong> ${data.tanggal}</p>
                                <p><strong>Customer:</strong> ${data.customer}</p>
                                <p><strong>Kendaraan:</strong> ${data.kendaraan}</p>
                            </div>
                            <div>
                                <p><strong>Service:</strong> ${data.service}</p>
                                <p><strong>Status:</strong> 
                                    <span class="px-2 py-1 text-xs rounded-full ${
                                        data.status === 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'
                                    }">${data.status}</span>
                                </p>
                                <p><strong>Total:</strong> <span class="text-green-600 font-bold">${formatCurrency(data.total_harga)}</span></p>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 border text-left">Kode</th>
                                        <th class="px-4 py-2 border text-left">Nama Barang</th>
                                        <th class="px-4 py-2 border text-center">Qty</th>
                                        <th class="px-4 py-2 border text-right">Harga</th>
                                        <th class="px-4 py-2 border text-right">Subtotal</th>
                                        <th class="px-4 py-2 border text-center">Status</th>
                                        <th class="px-4 py-2 border text-center">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHtml}
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    $('#detail-content').html(detailHtml);
                    $('#modal-detail').removeClass('hidden');
                } else {
                    alert(response.message || 'Gagal memuat detail');
                }
            })
            .fail(function() {
                alert('Error loading transaction detail');
            });
    });
    
    $('#btn-close-detail').click(function() {
        $('#modal-detail').addClass('hidden');
    });
    
    // Complete transaction
    $(document).on('click', '.btn-complete', function() {
        const id = $(this).data('id');
        
        if (confirm('Yakin ingin menyelesaikan transaksi ini?')) {
            $.post(`/kasir/transaksi-service/${id}/complete`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Gagal menyelesaikan transaksi');
                }
            })
            .fail(function(xhr) {
                const response = xhr.responseJSON;
                alert(response?.message || 'Error completing transaction');
            });
        }
    });
    
    // Refresh status
    $('#btn-refresh').click(function() {
        $(this).prop('disabled', true);
        
        $.get('/kasir/transaksi-service/validation-status')
            .done(function(response) {
                if (response.success) {
                    response.data.forEach(function(transaction) {
                        const container = $(`[data-transaction-id="${transaction.id_transaksi}"]`);
                        const statusHtml = `
                            <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">
                                ${transaction.pending_count} pending, 
                                ${transaction.approved_count} approved,
                                ${transaction.rejected_count} rejected
                            </span>
                        `;
                        container.find('.text-xs').replaceWith(statusHtml);
                        
                        const completeBtn = container.find('.btn-complete');
                        if (transaction.can_complete && completeBtn.length === 0) {
                            container.find('.flex.gap-2').append(`
                                <button class="btn-complete px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600 text-sm"
                                        data-id="${transaction.id_transaksi}">
                                    Selesaikan
                                </button>
                            `);
                        } else if (!transaction.can_complete) {
                            completeBtn.remove();
                        }
                    });
                }
            })
            .always(function() {
                $('#btn-refresh').prop('disabled', false);
            });
    });
    
    // Auto refresh every 30 seconds
    setInterval(function() {
        $('#btn-refresh').click();
    }, 30000);
    
    // Initialize
    updateSubmitButton();
    renderSelectedItems();

    // Test button state on page load
    console.log('Initial state:', {
        selectedItems: selectedItems.length,
        buttonDisabled: $('#btn-submit').prop('disabled')
    });
    
});


</script>
