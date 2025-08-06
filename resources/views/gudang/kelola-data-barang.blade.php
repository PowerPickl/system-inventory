<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kelola Data Barang - Gudang Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
/* Urgency Badge Styles */
.urgency-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.urgency-urgent {
    background-color: #fecaca;
    color: #991b1b;
    border: 1px solid #f87171;
    animation: pulse 2s infinite;
}

.urgency-high {
    background-color: #fed7aa;
    color: #c2410c;
    border: 1px solid #fb923c;
}

.urgency-medium {
    background-color: #fef3c7;
    color: #d97706;
    border: 1px solid #fbbf24;
}

.urgency-low {
    background-color: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #60a5fa;
}

.urgency-normal {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #4ade80;
}
</style>


</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <x-gudang.sidebar active="data" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Kelola Data Barang</h1>
                            <p class="text-gray-600 text-sm">Manage inventory data dengan EOQ integration</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Quick Stats -->
                            <div class="text-sm text-gray-600 hidden md:block">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                        <span id="totalBarang">0</span> Items
                                    </span>
                                    <span class="flex items-center">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></div>
                                        <span id="needRestock">0</span> Need Restock
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Add New Button -->
                            <button onclick="showAddBarangModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 text-sm font-medium">
                                ➕ Tambah Barang
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Tabs Navigation -->
                <div class="mb-6">
                    <nav class="flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab('list')" id="tab-list" class="tab-button active border-b-2 border-emerald-500 text-emerald-600 py-2 px-1 text-sm font-medium">
                            📋 Data Barang
                        </button>
                        <button onclick="switchTab('activity')" id="tab-activity" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-2 px-1 text-sm font-medium">
                            📝 Activity Log
                        </button>
                    </nav>
                </div>

                <!-- Tab Content: Data Barang -->
                <div id="content-list" class="tab-content">
                    <!-- Filters & Search -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Search -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input type="text" id="searchInput" placeholder="Cari nama/kode barang..." 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                                
                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Stok</label>
                                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">Semua Status</option>
                                        <option value="Aman">Aman</option>
                                        <option value="Perlu Restock">Perlu Restock</option>
                                        <option value="Habis">Habis</option>
                                    </select>
                                </div>

                                <!-- EOQ Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">EOQ Status</label>
                                    <select id="eoqFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">Semua EOQ</option>
                                        <option value="calculated">Sudah Dihitung</option>
                                        <option value="not-calculated">Belum Dihitung</option>
                                    </select>
                                </div>

                                <!-- Urgency Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Urgency Level</label>
                                    <select id="urgencyFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">All Urgency</option>
                                        <option value="URGENT">🚨 Urgent</option>
                                        <option value="HIGH">⚠️ High</option>
                                        <option value="MEDIUM">📋 Medium</option>
                                        <option value="LOW">📅 Low</option>
                                        <option value="NORMAL">✅ Normal</option>
                                    </select>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-end space-x-2">
                                    <button onclick="applyFilters()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                        🔍 Filter
                                    </button>
                                    <button onclick="resetFilters()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">
                                        🔄 Reset
                                    </button>
                                    <button onclick="exportData()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                        📥 Export
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Data Barang</h3>
                                <div class="flex space-x-3">
                                    <button onclick="bulkUpdateEOQ()" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                        🔄 Bulk Update EOQ
                                    </button>
                                    <span class="text-sm text-gray-600" id="tableInfo">Loading...</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full" id="barangTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input type="checkbox" id="selectAll" class="rounded">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center space-x-1">
                                                <span>Barang</span>
                                                <button onclick="toggleSortOrder('urgency')" class="text-xs text-blue-600 hover:text-blue-800">
                                                    🔄 Sort by Urgency
                                                </button>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EOQ Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="barangTableBody">
                                    <!-- Data will be loaded here via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-gray-700">
                                    <span>Showing</span>
                                    <span class="font-medium mx-1" id="pageStart">1</span>
                                    <span>to</span>
                                    <span class="font-medium mx-1" id="pageEnd">10</span>
                                    <span>of</span>
                                    <span class="font-medium mx-1" id="totalItems">0</span>
                                    <span>results</span>
                                </div>
                                
                                <div class="flex items-center space-x-2" id="pagination">
                                    <!-- Pagination buttons will be generated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: Activity Log -->
                <div id="content-activity" class="tab-content hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">📝 Activity Log</h3>
                            <p class="text-sm text-gray-600 mt-1">Track all changes made to barang data</p>
                        </div>
                        
                        <!-- Activity Filters -->
                        <div class="p-4 border-b border-gray-200 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <input type="text" id="activitySearch" placeholder="Search activity..." 
                                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                
                                <select id="activityType" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <option value="">All Activities</option>
                                    <option value="create">Create</option>
                                    <option value="update">Update</option>
                                    <option value="stock_adjustment">Stock Adjustment</option>
                                    <option value="eoq_update">EOQ Update</option>
                                </select>
                                
                                <input type="date" id="activityDate" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                
                                <button onclick="loadActivityLog()" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
                                    🔍 Filter Log
                                </button>
                            </div>
                        </div>
                        
                        <!-- Activity Timeline -->
                        <div class="p-6">
                            <div id="activityTimeline" class="space-y-4">
                                <!-- Activity items will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit Barang Modal -->
    <div id="barangModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="inline-block align-top bg-white rounded-lg shadow-xl border w-full max-w-4xl my-8 overflow-hidden text-left transform transition-all">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Tambah Barang Baru</h3>
                        <p class="text-sm text-gray-600 mt-1">Fill in the details below</p>
                    </div>
                    <button onclick="closeBarangModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="max-h-[70vh] overflow-y-auto p-4">
                    <form id="barangForm" class="space-y-4">
                        <input type="hidden" id="barangId" name="id_barang">

                         <!-- Kategori & Auto Code Section -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <h4 class="font-medium text-purple-900 mb-3">🏷️ Kategori & Kode</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Barang *</label>
                                    <select id="kategoriBarang" name="id_kategori" required onchange="onKategoriChange()"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500">
                                        <option value="">Pilih Kategori</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Pilih kategori untuk auto-generate kode</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Internal</label>
                                    <div class="flex space-x-2">
                                        <input type="text" id="kodeBarangAuto" name="kode_barang_auto" readonly
                                            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                        <button type="button" onclick="generateKodePreview()" 
                                                class="px-3 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 text-sm">
                                            🔄
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Auto-generated: FLD-0001</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Basic Information -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <h4 class="font-medium text-blue-900 mb-3">📋 Basic Information</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang *</label>
                                    <input type="text" id="namaBarang" name="nama_barang" required
                                        placeholder="e.g., Oli Mesin, Filter Udara"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Merk/Brand</label>
                                    <input type="text" id="merkBarang" name="merk"
                                        placeholder="e.g., Castrol, Toyota, NGK"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Model/Tipe</label>
                                    <input type="text" id="modelTipe" name="model_tipe"
                                        placeholder="e.g., GTX 10W-40, Avanza"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan *</label>
                                    <select id="satuan" name="satuan" required
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih Satuan</option>
                                        <option value="pcs">Pieces (pcs)</option>
                                        <option value="liter">Liter</option>
                                        <option value="kg">Kilogram (kg)</option>
                                        <option value="box">Box</option>
                                        <option value="set">Set</option>
                                        <option value="unit">Unit</option>
                                        <option value="botol">Botol</option>
                                        <option value="kaleng">Kaleng</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Manual (Optional)</label>
                                    <input type="text" id="kodeBarang" name="kode_barang"
                                        placeholder="Manual code (if needed)"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Opsional, untuk backward compatibility</p>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                                <input type="text" id="deskripsi" name="deskripsi"
                                    placeholder="e.g., Oli mesin synthetic"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Detail</label>
                                <textarea id="keteranganDetail" name="keterangan_detail" rows="2"
                                        placeholder="e.g., 4 Liter per botol, untuk mobil bensin, viscosity 10W-40"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                <p class="text-xs text-gray-500 mt-1">Detail spec, isi kemasan, kompatibilitas, dll</p>
                            </div>
                        </div>

                         <!-- Pricing Information -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <h4 class="font-medium text-green-900 mb-3">💰 Pricing Information</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                                        <input type="number" id="hargaBeli" name="harga_beli" step="0.01" min="0" required
                                            class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                                        <input type="number" id="hargaJual" name="harga_jual" step="0.01" min="0" required
                                            class="w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                            </div>

                        <!-- EOQ Parameters -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-purple-900">🧮 EOQ Parameters</h4>
                                <button type="button" onclick="autoCalculateEOQParams()" class="text-xs text-purple-600 hover:text-purple-800 px-2 py-1 bg-white rounded">
                                    🤖 Auto Calculate
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Annual Demand</label>
                                    <input type="number" id="annualDemand" name="annual_demand" min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-xs text-gray-500 mt-1">Total demand per year</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordering Cost</label>
                                    <input type="number" id="orderingCost" name="ordering_cost" step="0.01" min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-xs text-gray-500 mt-1">Cost per order</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Holding Cost (%)</label>
                                    <input type="number" id="holdingCost" name="holding_cost" step="0.01" min="0" max="100"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-xs text-gray-500 mt-1">% of item cost/year</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lead Time (days)</label>
                                    <input type="number" id="leadTime" name="lead_time" min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-xs text-gray-500 mt-1">Days from order to delivery</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Avg Daily Demand</label>
                                    <input type="number" id="demandAvgDaily" name="demand_avg_daily" step="0.01" min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-xs text-gray-500 mt-1">Average per day</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Daily Demand</label>
                                    <input type="number" id="demandMaxDaily" name="demand_max_daily" step="0.01" min="0"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-xs text-gray-500 mt-1">Maximum per day</p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Stock (for edit mode) -->
                        <div id="currentStockSection" class="bg-orange-50 border border-orange-200 rounded-lg p-3 hidden">
                            <h4 class="font-medium text-orange-900 mb-2">📦 Current Stock</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                                    <input type="number" id="currentStock" readonly
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-100">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Adjustment</label>
                                    <input type="number" id="stockAdjustment" name="stock_adjustment" 
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                    <p class="text-xs text-gray-500 mt-1">+ tambah, - kurang</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Adjustment</label>
                                    <input type="text" id="adjustmentReason" name="adjustment_reason"
                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                        </div>

                        <!-- EOQ Calculation Results (for edit mode) -->
                        <div id="eoqResultsSection" class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 hidden">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-indigo-900">📊 EOQ Results</h4>
                                <button type="button" onclick="recalculateEOQ()" class="text-xs text-indigo-600 hover:text-indigo-800 px-2 py-1 bg-white rounded">
                                    🔄 Recalculate
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3">
                                <div class="text-center p-2 bg-white rounded-lg border">
                                    <div class="text-xl font-bold text-indigo-600" id="eoqValue">-</div>
                                    <div class="text-xs text-gray-600">EOQ</div>
                                </div>
                                <div class="text-center p-2 bg-white rounded-lg border">
                                    <div class="text-xl font-bold text-green-600" id="safetyStockValue">-</div>
                                    <div class="text-xs text-gray-600">Safety Stock</div>
                                </div>
                                <div class="text-center p-2 bg-white rounded-lg border">
                                    <div class="text-xl font-bold text-orange-600" id="ropValue">-</div>
                                    <div class="text-xs text-gray-600">ROP</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" onclick="closeBarangModal()" 
                            class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium">
                        Cancel
                    </button>
                    <button type="button" onclick="saveBarang()" 
                            class="px-6 py-2 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 font-medium">
                        💾 Save Barang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentPage = 1;
        let itemsPerPage = 10;
        let totalItems = 0;
        let barangData = [];
        let filteredData = [];
        let kategoriOptions = [];
        let currentSortBy = 'urgency';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Enhanced DOMContentLoaded event
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Kelola Data Barang with Kategori initialized');
            
            // Initialize select all functionality first
            initializeSelectAll();
            
            // Load kategori options AND data sequentially
            initializePageData();
        });

        // FIXED: Proper initialization sequence
        async function initializePageData() {
            try {
                // Step 1: Load kategori options first
                console.log('📋 Loading kategori options...');
                await loadKategoriOptionsSync();
                console.log('✅ Kategori options loaded:', kategoriOptions.length, 'categories');
                
                // Step 2: Load barang data after kategori is loaded
                console.log('📊 Loading barang data...');
                loadBarangData();
                
            } catch (error) {
                console.error('🔥 Failed to initialize page:', error);
                showNotification('Failed to load page data: ' + error.message, 'error');
                // Show error state
                showEmptyState('error', 'Failed to load page data. Please refresh the page.');
            }
        }

        function initializeSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.item-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }
        }


        // FIXED: Proper async kategori loading
        async function loadKategoriOptionsSync() {
            try {
                const response = await fetch('/gudang/kelola-data-barang/kategori-options', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('📡 Kategori API Response:', data);
                
                if (data.success && data.data) {
                    kategoriOptions = data.data; // Use global variable - NO LET declaration here
                    populateKategoriDropdown();
                    return kategoriOptions;
                } else {
                    throw new Error(data.message || 'Failed to load kategori options');
                }
            } catch (error) {
                console.error('❌ Error loading kategori options:', error);
                // Set empty options to prevent further errors
                kategoriOptions = []; // Use global variable - NO LET declaration here
                throw error;
            }
        }

        // Tab switching
        function switchTab(tabName) {
           // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-emerald-500', 'text-emerald-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab
            document.getElementById(`content-${tabName}`).classList.remove('hidden');
            const activeTab = document.getElementById(`tab-${tabName}`);
            activeTab.classList.add('active', 'border-emerald-500', 'text-emerald-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');

            // Load data if needed
            if (tabName === 'activity') {
                loadActivityLog();
            }
        }

        // Load barang data
        function loadBarangData() {
        console.log('📊 Loading barang data...');
        showLoadingSpinner('Loading barang data...');
        
        // Get current filter values safely
        const searchText = document.getElementById('searchInput')?.value || '';
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const eoqFilter = document.getElementById('eoqFilter')?.value || '';
        const urgencyFilter = document.getElementById('urgencyFilter')?.value || '';
        
        const params = new URLSearchParams({
            page: currentPage,
            per_page: itemsPerPage,
            search: searchText,
            status_filter: statusFilter,
            eoq_filter: eoqFilter,
            urgency_filter: urgencyFilter // TAMBAH INI
        });

        // FIXED: Ensure correct URL format
        const apiUrl = `/gudang/kelola-data-barang/data?${params}`;
        console.log('🔗 API URL:', apiUrl);

        // Make API call with better error handling
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('📡 Data API Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            hideLoadingSpinner();
            console.log('✅ Data API Response:', data);
            
            if (data.success) {
                barangData = data.data || [];
                filteredData = [...barangData];
                totalItems = data.pagination?.total || 0;
                
                console.log('📦 Loaded', barangData.length, 'items, total:', totalItems);
                
                if (barangData.length === 0) {
                    showEmptyState('empty', 'No barang data found');
                } else {
                    updateTable();
                    updateStats();
                    
                    if (data.pagination) {
                        updatePaginationFromResponse(data.pagination);
                    }
                }
            } else {
                console.error('❌ API Error:', data.message);
                showNotification('Error loading data: ' + (data.message || 'Unknown error'), 'error');
                showEmptyState('error', data.message || 'Failed to load data');
            }
        })
        .catch(error => {
            hideLoadingSpinner();
            console.error('🔥 Network Error:', error);
            showNotification(`Network error: ${error.message}`, 'error');
            showEmptyState('error', 'Failed to load data. Check your connection and try again.');
        });
        }
            
        function showEmptyState(type = 'empty', message = '') {
            const tbody = document.getElementById('barangTableBody');
            if (!tbody) return;
            
            let icon, title, description, buttonHtml = '';
            
            if (type === 'error') {
                icon = `<svg class="w-12 h-12 text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
                title = 'Failed to load data';
                description = message || 'Check your connection and try again';
                buttonHtml = `<button onclick="loadBarangData()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    🔄 Retry
                </button>`;
            } else {
                icon = `<svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`;
                title = 'No barang data found';
                description = message || 'Start by adding your first barang';
                buttonHtml = `<button onclick="showAddBarangModal()" class="mt-2 px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">
                    ➕ Add First Item
                </button>`;
            }
            
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            ${icon}
                            <p class="text-lg font-medium">${title}</p>
                            <p class="text-sm mt-1">${description}</p>
                            ${buttonHtml}
                        </div>
                    </td>
                </tr>
            `;
        }

        // Tambahin di bagian helper functions
        function getDemandBadgeColor(demandLevel) {
            switch(demandLevel) {
                case 'High': return 'bg-red-100 text-red-800';
                case 'Medium': return 'bg-yellow-100 text-yellow-800';
                case 'Low': return 'bg-blue-100 text-blue-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        }

        function toggleSortOrder(type) {
            if (type === 'urgency') {
                // Reload data dengan urgency sorting
                currentSortBy = 'urgency';
                loadBarangData();
                showNotification('Sorted by urgency level', 'info');
            }
        }


        // Enhanced updateTable function with kategori display
        function updateTable() {
            const tbody = document.getElementById('barangTableBody');
            
            console.log('🔄 Updating table with data:', barangData);
            
            // Check if we have data
            if (!barangData || barangData.length === 0) {
                showEmptyState('empty');
                return;
            }

            tbody.innerHTML = barangData.map(item => {
                // Safe null checks
                const currentStock = item.current_stock || 0;
                const statusStok = item.status_stok || 'No Data';
                const namaBarang = item.nama_barang || 'Unknown Item';
                const kodeBarang = item.kode_barang || '';
                const satuan = item.satuan || '';
                const deskripsi = item.deskripsi || '';
                const merk = item.merk || '';
                const modelTipe = item.model_tipe || '';
                const hargaBeli = item.harga_beli || 0;
                const hargaJual = item.harga_jual || 0;
                const eoqCalculated = item.eoq_calculated || null;
                const ropCalculated = item.rop_calculated || null;
                const hasEoqData = item.has_eoq_data || false;
                const needsRestock = item.needs_restock || false;
                const lastUpdated = item.last_updated || new Date().toISOString();

                // Build display name with merk
                let displayName = namaBarang;
                if (merk) {
                    displayName = `${merk} ${namaBarang}`;
                }
                if (modelTipe) {
                    displayName += ` ${modelTipe}`;
                }

                // Kategori badge
                const kategoriBadge = item.kategori_badge ? 
                    `<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full" style="${item.kategori_badge.style}">
                        ${item.kategori_badge.icon} ${item.kategori_badge.kode}
                    </span>` : 
                    `<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                        ❓ UNC
                    </span>`;

                return `
                    <tr class="hover:bg-gray-50" data-item-id="${item.id_barang}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${needsRestock ? `<input type="checkbox" class="item-checkbox rounded" value="${item.id_barang}">` : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <!-- Urgency Badge & Kategori -->
                            <div class="flex items-center space-x-2 mb-1">
                                ${item.urgency_badge ? `
                                    <span class="urgency-badge urgency-${item.urgency_level.toLowerCase()}" title="${item.auto_reason}">
                                        ${item.urgency_badge.icon} ${item.urgency_level}
                                    </span>
                                ` : ''}
                                ${kategoriBadge}
                            </div>
                            
                            <div class="text-sm font-medium text-gray-900">${displayName}</div>
                            <div class="text-sm text-gray-500">${kodeBarang} • ${satuan}</div>
                            
                            <!-- Demand Level -->
                            ${item.demand_level ? `
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${getDemandBadgeColor(item.demand_level)}">
                                        ${item.demand_level} Demand
                                    </span>
                                </div>
                            ` : ''}
                            
                            ${deskripsi ? `<div class="text-xs text-gray-400 mt-1">${deskripsi}</div>` : ''}
                            
                            <!-- Days until stockout -->
                            ${item.days_until_stockout ? `
                                <div class="text-xs text-red-600 mt-1">
                                    ~${item.days_until_stockout} days left
                                </div>
                            ` : ''}
                        </div>
                    </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${currentStock}</div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStockStatusColor(statusStok)}">
                                ${statusStok}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div>Beli: Rp ${formatCurrency(hargaBeli)}</div>
                                <div>Jual: Rp ${formatCurrency(hargaJual)}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${hasEoqData ? `
                                <div class="text-sm text-gray-900">
                                    <div>EOQ: ${eoqCalculated || '-'}</div>
                                    <div>ROP: ${ropCalculated || '-'}</div>
                                    <div class="text-xs text-blue-600 cursor-pointer" onclick="showEOQDetails(${item.id_barang})">
                                        📊 View Details
                                    </div>
                                </div>
                            ` : `
                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">
                                    Not Calculated
                                </span>
                                <div class="text-xs text-blue-600 cursor-pointer mt-1" onclick="calculateEOQ(${item.id_barang})">
                                    🧮 Calculate
                                </div>
                            `}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStockStatusColor(statusStok)}">
                                    ${statusStok}
                                </span>
                                <div class="text-xs text-gray-500">
                                    Updated: ${formatDateTime(lastUpdated)}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="editBarang(${item.id_barang})" 
                                        class="text-blue-600 hover:text-blue-900" title="Edit">
                                    ✏️
                                </button>
                                <button onclick="viewBarangDetail(${item.id_barang})" 
                                        class="text-green-600 hover:text-green-900" title="View Details">
                                    👁️
                                </button>
                                <button onclick="adjustStock(${item.id_barang})" 
                                        class="text-orange-600 hover:text-orange-900" title="Adjust Stock">
                                    📦
                                </button>
                                <button onclick="deleteBarang(${item.id_barang})" 
                                        class="text-red-600 hover:text-red-900" title="Delete">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            
            console.log('✅ Table updated successfully');
        }
        

        // Update pagination from API response
        function updatePaginationFromResponse(pagination) {
            const paginationContainer = document.getElementById('pagination');
            if (!paginationContainer) return;
            
            const totalPages = pagination.total_pages;
            currentPage = pagination.current_page;
            
            let paginationHTML = '';
            
            // Previous button
            if (currentPage > 1) {
                paginationHTML += `<button onclick="changePage(${currentPage - 1})" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Previous</button>`;
            }
            
            // Page numbers
            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                const isActive = i === currentPage;
                paginationHTML += `
                    <button onclick="changePage(${i})" 
                            class="px-3 py-2 text-sm ${isActive ? 'bg-emerald-600 text-white' : 'text-gray-500 hover:text-gray-700'}">
                        ${i}
                    </button>
                `;
            }
            
            // Next button
            if (currentPage < totalPages) {
                paginationHTML += `<button onclick="changePage(${currentPage + 1})" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">Next</button>`;
            }
            
            paginationContainer.innerHTML = paginationHTML;
            
            // Update pagination info
            const pageStartElement = document.getElementById('pageStart');
            const pageEndElement = document.getElementById('pageEnd');
            const totalItemsElement = document.getElementById('totalItems');
            const tableInfoElement = document.getElementById('tableInfo');
            
            if (pageStartElement) pageStartElement.textContent = pagination.from;
            if (pageEndElement) pageEndElement.textContent = pagination.to;
            if (totalItemsElement) totalItemsElement.textContent = pagination.total;
            if (tableInfoElement) tableInfoElement.textContent = `${pagination.total} items found`;
        }

        // Update table info
        function updateTableInfo() {
            const start = (currentPage - 1) * itemsPerPage + 1;
            const end = Math.min(start + itemsPerPage - 1, filteredData.length);
            
            document.getElementById('pageStart').textContent = start;
            document.getElementById('pageEnd').textContent = end;
            document.getElementById('totalItems').textContent = filteredData.length;
            document.getElementById('tableInfo').textContent = `${filteredData.length} items found`;
        }

        // Update stats from actual data
        function updateStats() {
             try {
                const total = barangData.length;
                const needRestock = barangData.filter(item => item.needs_restock === true).length;
                
                const totalElement = document.getElementById('totalBarang');
                const needRestockElement = document.getElementById('needRestock');
                
                if (totalElement) totalElement.textContent = total;
                if (needRestockElement) needRestockElement.textContent = needRestock;
                
                console.log('📊 Stats updated:', { total, needRestock });
            } catch (error) {
                console.error('Error updating stats:', error);
            }
        }

        // View barang detail
        function viewBarangDetail(id) {
            showLoadingSpinner('Loading barang details...');
            
            fetch(`/gudang/kelola-data-barang/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingSpinner();
                
                if (data.success) {
                    showBarangDetailModal(data.data);
                } else {
                    showNotification(data.message || 'Error loading barang details', 'error');
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('Error loading barang details:', error);
                showNotification('Network error while loading details', 'error');
            });
        }

        // Show barang detail modal
        function showBarangDetailModal(data) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div class="inline-block align-top bg-white rounded-lg shadow-xl border w-full max-w-2xl my-8 overflow-hidden text-left transform transition-all">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">📋 Detail Barang - ${data.barang.nama_barang}</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="max-h-[70vh] overflow-y-auto p-4">
                            <!-- Basic Info -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <h4 class="font-medium text-blue-900 mb-2 text-sm">Basic Information</h4>
                                    <div class="space-y-2 text-sm">
                                        <div><span class="font-medium">Kode:</span> ${data.barang.kode_barang}</div>
                                        <div><span class="font-medium">Nama:</span> ${data.barang.nama_barang}</div>
                                        <div><span class="font-medium">Satuan:</span> ${data.barang.satuan}</div>
                                        <div><span class="font-medium">Deskripsi:</span> ${data.barang.deskripsi || '-'}</div>
                                    </div>
                                </div>
                                
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <h4 class="font-medium text-green-900 mb-2 text-sm">Stock & Pricing</h4>
                                    <div class="space-y-2 text-sm">
                                        <div><span class="font-medium">Current Stock:</span> ${data.current_stock} ${data.barang.satuan}</div>
                                        <div><span class="font-medium">Status:</span> <span class="px-2 py-1 text-xs rounded-full ${getStockStatusColor(data.status_stok)}">${data.status_stok}</span></div>
                                        <div><span class="font-medium">Harga Beli:</span> Rp ${formatCurrency(data.barang.harga_beli)}</div>
                                        <div><span class="font-medium">Harga Jual:</span> Rp ${formatCurrency(data.barang.harga_jual)}</div>
                                    </div>
                                </div>
                            </div>

                            ${data.eoq_details ? `
                            <!-- EOQ Details -->
                            <div class="bg-purple-50 p-3 rounded-lg mb-4">
                                <h4 class="font-medium text-purple-900 mb-2 text-sm">EOQ Calculation Results</h4>
                                <div class="grid grid-cols-3 gap-3 mb-3">
                                    <div class="text-center p-2 bg-white rounded-lg border">
                                        <div class="text-lg font-bold text-blue-600">${data.eoq_details.eoq.eoq}</div>
                                        <div class="text-xs text-gray-600">EOQ</div>
                                    </div>
                                    <div class="text-center p-2 bg-white rounded-lg border">
                                        <div class="text-lg font-bold text-green-600">${data.eoq_details.safety_stock.safety_stock}</div>
                                        <div class="text-xs text-gray-600">Safety Stock</div>
                                    </div>
                                    <div class="text-center p-2 bg-white rounded-lg border">
                                        <div class="text-lg font-bold text-orange-600">${data.eoq_details.rop.rop}</div>
                                        <div class="text-xs text-gray-600">ROP</div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div><span class="font-medium">Annual Demand:</span> ${data.barang.annual_demand || '-'}</div>
                                    <div><span class="font-medium">Ordering Cost:</span> Rp ${formatCurrency(data.barang.ordering_cost || 0)}</div>
                                    <div><span class="font-medium">Holding Cost:</span> ${data.barang.holding_cost || '-'}%</div>
                                    <div><span class="font-medium">Lead Time:</span> ${data.barang.lead_time || '-'} days</div>
                                </div>
                            </div>
                            ` : ''}

                            ${data.recent_movements && data.recent_movements.length > 0 ? `
                            <!-- Recent Movements -->
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-2 text-sm">Recent Stock Movements</h4>
                                <div class="space-y-2">
                                    ${data.recent_movements.slice(0, 5).map(movement => `
                                        <div class="flex justify-between items-center text-sm bg-white p-2 rounded">
                                            <div>
                                                <span class="font-medium">${movement.jenis_perubahan}</span>
                                                <span class="text-gray-600">- ${movement.qty_perubahan > 0 ? '+' : ''}${movement.qty_perubahan}</span>
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                ${formatDateTime(movement.tanggal_log)}
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                            <button onclick="editBarang(${data.barang.id_barang}); this.closest('.fixed').remove();" 
                                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                ✏️ Edit Barang
                            </button>
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }


        // =====================================
        // KATEGORI & AUTO-CODE FUNCTIONS
        // =====================================


        // Load kategori options
        function loadKategoriOptions() {
            fetch('/gudang/kelola-data-barang/kategori-options', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    kategoriOptions = data.data;
                    populateKategoriDropdown();
                } else {
                    console.error('Failed to load kategori options:', data.message);
                }
            })
            .catch(error => {
                console.error('Error loading kategori options:', error);
            });
        }

        // Populate kategori dropdown
        function populateKategoriDropdown() {
        const dropdown = document.getElementById('kategoriBarang');
        if (!dropdown) {
            console.warn('⚠️ Kategori dropdown not found');
            return;
        }
        
        console.log('🔄 Populating kategori dropdown with options:', kategoriOptions);

        try {
            // Clear existing options except first
            dropdown.innerHTML = '<option value="">Pilih Kategori</option>';
            
            if (kategoriOptions && kategoriOptions.length > 0) {
                kategoriOptions.forEach(option => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.value;
                    optionElement.textContent = option.text;
                    optionElement.setAttribute('data-kode', option.kode || '');
                    optionElement.setAttribute('data-color', option.color || '');
                    dropdown.appendChild(optionElement);
                });
                
                console.log('✅ Kategori dropdown populated with', kategoriOptions.length, 'options');
            } else {
                console.warn('⚠️ No kategori options available');
                dropdown.innerHTML = '<option value="">No categories available</option>';
            }
        } catch (error) {
            console.error('❌ Error populating dropdown:', error);
            dropdown.innerHTML = '<option value="">Error loading categories</option>';
        }
        }

        // Handle kategori change
        function onKategoriChange() {
            const kategoriSelect = document.getElementById('kategoriBarang');
            const kodeInternalInput = document.getElementById('kodeInternal');
            
            if (kategoriSelect && kategoriSelect.value) {
                generateKodePreview();
            } else if (kodeInternalInput) {
                kodeInternalInput.value = '';
            }
        }

        // Generate kode preview
        function generateKodePreview() {
            const kategoriId = document.getElementById('kategoriBarang')?.value;
            const kodeInternalInput = document.getElementById('kodeBarangAuto'); // ← UBAH INI
            
            if (!kategoriId) {
                showNotification('Pilih kategori terlebih dahulu', 'warning');
                return;
            }
            
            if (!kodeInternalInput) { // ← TAMBAH CHECK INI
                console.error('Kode input element not found!');
                showNotification('Form element error', 'error');
                return;
            }
            
            fetch('/gudang/kelola-data-barang/preview-kode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    kategori_id: kategoriId
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('API Response:', data); // ← TAMBAH DEBUG
                if (data.success && kodeInternalInput) {
                    console.log('Setting value:', data.data.kode_barang); // ← TAMBAH DEBUG
                    kodeInternalInput.value = data.data.kode_barang;
                    
                    // Update preview text
                    const previewText = kodeInternalInput.parentElement.querySelector('p');
                    if (previewText) {
                        previewText.textContent = data.data.preview;
                    }
                } else {
                    showNotification('Error generating code: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error generating code preview:', error);
                showNotification('Network error generating code', 'error');
            });
        }

        // Change page
        function changePage(page) {
            currentPage = page;
            loadBarangData();
        }

        // Apply filters
        function applyFilters() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const eoqFilter = document.getElementById('eoqFilter');
            const urgencyFilter = document.getElementById('urgencyFilter'); // TAMBAH INI
            
            currentPage = 1;
            loadBarangData();
        }

        // Reset filters
        function resetFilters() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const eoqFilter = document.getElementById('eoqFilter');
            const urgencyFilter = document.getElementById('urgencyFilter');
            
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = '';
            if (eoqFilter) eoqFilter.value = '';
             if (urgencyFilter) urgencyFilter.value = '';
            
            currentPage = 1;
            loadBarangData();
        }

        // Show add barang modal
        function showAddBarangModal() {
            const modal = document.getElementById('barangModal');
            const form = document.getElementById('barangForm');
            const modalTitle = document.getElementById('modalTitle');
            const barangId = document.getElementById('barangId');
            const currentStockSection = document.getElementById('currentStockSection');
            const eoqResultsSection = document.getElementById('eoqResultsSection');
            
            // Reset form and modal state
            if (modalTitle) modalTitle.textContent = 'Tambah Barang Baru';
            if (form) form.reset();
            if (barangId) barangId.value = '';
            
            // Show/hide appropriate sections
            if (currentStockSection) currentStockSection.classList.add('hidden');
            if (eoqResultsSection) eoqResultsSection.classList.add('hidden');
            
            // Ensure kategori options are loaded
            if (kategoriOptions.length === 0) {
                console.log('🔄 Loading kategori options for add modal...');
                loadKategoriOptionsSync().catch(error => {
                    console.error('❌ Failed to load kategori for add modal:', error);
                    showNotification('Failed to load categories', 'error');
                });
            } else {
                // Populate dropdown if it's empty
                populateKategoriDropdown();
            }
            
            // Clear auto-generated fields
            const kodeInternalInput = document.getElementById('kodeInternal');
            if (kodeInternalInput) kodeInternalInput.value = '';
            
            // Show modal
            if (modal) modal.classList.remove('hidden');
        }


        // Enhanced edit barang function
        function editBarang(id) {
            const item = barangData.find(b => b.id_barang === id);
            if (!item) {
                showNotification('Barang not found', 'error');
                return;
            }

            const modal = document.getElementById('barangModal');
            const modalTitle = document.getElementById('modalTitle');
            const barangIdInput = document.getElementById('barangId');
            const currentStockSection = document.getElementById('currentStockSection');
            const eoqResultsSection = document.getElementById('eoqResultsSection');

            // Set modal title and ID
            if (modalTitle) modalTitle.textContent = 'Edit Data Barang';
            if (barangIdInput) barangIdInput.value = item.id_barang;

            // Load kategori options if not loaded
            if (kategoriOptions.length === 0) {
                console.log('🔄 Kategori options not loaded, loading now...');
                loadKategoriOptionsSync().then(() => {
                    populateFormData(item);
                }).catch(error => {
                    console.error('❌ Failed to load kategori for edit:', error);
                    showNotification('Failed to load categories for editing', 'error');
                });
            } else {
                populateFormData(item);
            }

            // Show appropriate sections for edit mode
            if (currentStockSection) currentStockSection.classList.remove('hidden');
            
            if (item.has_eoq_data && eoqResultsSection) {
                eoqResultsSection.classList.remove('hidden');
                updateEOQDisplay(item);
            }

            // Show modal
            if (modal) modal.classList.remove('hidden');
        }


        // Populate form data for edit mode
        function populateFormData(item) {
            // Basic fields
            const fields = [
                { id: 'kategoriBarang', value: item.id_kategori || '' },
                { id: 'namaBarang', value: item.nama_barang || '' },
                { id: 'merkBarang', value: item.merk || '' },
                { id: 'modelTipe', value: item.model_tipe || '' },
                { id: 'satuan', value: item.satuan || '' },
                { id: 'kodeBarang', value: item.kode_barang || '' },
                { id: 'kodeBarangAuto', value: item.kode_barang || '' },
                { id: 'deskripsi', value: item.deskripsi || '' },
                { id: 'keteranganDetail', value: item.keterangan_detail || '' },
                { id: 'hargaBeli', value: item.harga_beli || '' },
                { id: 'hargaJual', value: item.harga_jual || '' },
                // EOQ fields
                { id: 'annualDemand', value: item.annual_demand || '' },
                { id: 'orderingCost', value: item.ordering_cost || '' },
                { id: 'holdingCost', value: item.holding_cost || '' },
                { id: 'leadTime', value: item.lead_time || '' },
                { id: 'demandAvgDaily', value: item.demand_avg_daily || '' },
                { id: 'demandMaxDaily', value: item.demand_max_daily || '' }
            ];

            fields.forEach(field => {
                const element = document.getElementById(field.id);
                if (element) {
                    element.value = field.value;
                }
            });

            // Current stock info
            const currentStockInput = document.getElementById('currentStock');
            if (currentStockInput) {
                currentStockInput.value = item.current_stock || 0;
            }
        }


        // Update EOQ display in edit mode
        function updateEOQDisplay(item) {
            const eoqValue = document.getElementById('eoqValue');
            const safetyStockValue = document.getElementById('safetyStockValue');
            const ropValue = document.getElementById('ropValue');

            if (eoqValue) eoqValue.textContent = item.eoq_calculated || '-';
            if (safetyStockValue) safetyStockValue.textContent = item.safety_stock || '-';
            if (ropValue) ropValue.textContent = item.rop_calculated || '-';
        }

        // Close modal
        function closeBarangModal() {
            const modal = document.getElementById('barangModal');
            if (modal) modal.classList.add('hidden');
        }

        // Enhanced save barang function
        function saveBarang() {
            const form = document.getElementById('barangForm');
            const isEdit = document.getElementById('barangId').value;
            
            // Validate required fields
            if (!validateForm()) {
                return;
            }

            // Prepare form data
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            const loadingMessage = isEdit ? 'Updating barang...' : 'Creating barang...';
            showLoadingSpinner(loadingMessage);

            const url = isEdit ? `/gudang/kelola-data-barang/${isEdit}` : '/gudang/kelola-data-barang';
            const method = isEdit ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingSpinner();
                
                if (data.success) {
                    closeBarangModal();
                    showNotification(data.message, 'success');
                    loadBarangData(); // Reload data
                } else {
                    if (data.errors) {
                        showValidationErrors(data.errors);
                    } else {
                        showNotification(data.message || 'An error occurred', 'error');
                    }
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('Error saving barang:', error);
                showNotification('Network error while saving barang', 'error');
            });
        }

        // Form validation
        function validateForm() {
            const requiredFields = [
                { id: 'kategoriBarang', name: 'Kategori' },
                { id: 'namaBarang', name: 'Nama Barang' },
                { id: 'satuan', name: 'Satuan' },
                { id: 'hargaBeli', name: 'Harga Beli' },
                { id: 'hargaJual', name: 'Harga Jual' }
            ];

            const errors = [];
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element || !element.value.trim()) {
                    errors.push(field.name + ' harus diisi');
                    
                    // Add visual feedback
                    if (element) {
                        element.classList.add('border-red-500');
                        setTimeout(() => {
                            element.classList.remove('border-red-500');
                        }, 3000);
                    }
                }
            });

            // Validate prices
            const hargaBeli = parseFloat(document.getElementById('hargaBeli')?.value) || 0;
            const hargaJual = parseFloat(document.getElementById('hargaJual')?.value) || 0;
            
            if (hargaBeli <= 0) {
                errors.push('Harga beli harus lebih dari 0');
            }
            
            if (hargaJual <= 0) {
                errors.push('Harga jual harus lebih dari 0');
            }
            
            if (hargaJual <= hargaBeli) {
                errors.push('Harga jual harus lebih tinggi dari harga beli');
            }

            if (errors.length > 0) {
                showValidationErrors({ general: errors });
                return false;
            }

            return true;
        }

        // Show validation errors
        function showValidationErrors(errors) {
            let errorMessage = 'Validation errors:\n';
            
            if (errors.general) {
                errors.general.forEach(error => {
                    errorMessage += '• ' + error + '\n';
                });
            } else {
                Object.values(errors).forEach(fieldErrors => {
                    if (Array.isArray(fieldErrors)) {
                        fieldErrors.forEach(error => {
                            errorMessage += '• ' + error + '\n';
                        });
                    }
                });
            }
            
            showNotification(errorMessage, 'error');
        }

        // Adjust stock
        function adjustStock(id) {
            const item = barangData.find(b => b.id_barang === id);
            if (!item) return;

            const adjustment = prompt(`Current stock: ${item.current_stock} ${item.satuan}\nEnter adjustment (+/-):`, '0');
            if (adjustment === null) return;

            const adjustmentValue = parseInt(adjustment);
            if (isNaN(adjustmentValue) || adjustmentValue === 0) {
                showNotification('Invalid adjustment value', 'error');
                return;
            }

            const reason = prompt('Reason for stock adjustment:', 'Manual adjustment');
            if (reason === null) return;

            showLoadingSpinner('Adjusting stock...');
            
            fetch(`/gudang/kelola-data-barang/${id}/adjust-stock`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    adjustment: adjustmentValue,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingSpinner();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    loadBarangData(); // Reload data
                } else {
                    showNotification(data.message || 'Error adjusting stock', 'error');
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('Error adjusting stock:', error);
                showNotification('Network error while adjusting stock', 'error');
            });
        }

        // Delete barang
        function deleteBarang(id) {
            const item = barangData.find(b => b.id_barang === id);
            if (!item) return;

            if (confirm(`Are you sure you want to delete "${item.nama_barang}"?\nThis action cannot be undone.`)) {
                showLoadingSpinner('Deleting barang...');
                
                fetch(`/gudang/kelola-data-barang/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingSpinner();
                    
                    if (data.success) {
                        showNotification(data.message, 'success');
                        loadBarangData(); // Reload data
                    } else {
                        showNotification(data.message || 'Error deleting barang', 'error');
                    }
                })
                .catch(error => {
                    hideLoadingSpinner();
                    console.error('Error deleting barang:', error);
                    showNotification('Network error while deleting barang', 'error');
                });
            }
        }

        // Show EOQ details
        function showEOQDetails(id) {
            const item = barangData.find(b => b.id_barang === id);
            if (!item || !item.eoq_calculated) return;

            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div class="inline-block align-top bg-white rounded-lg shadow-xl border w-full max-w-2xl my-8 overflow-hidden text-left transform transition-all">
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">📊 EOQ Analysis</h3>
                                <p class="text-sm text-gray-600 mt-1">${item.nama_barang}</p>
                            </div>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Modal Content -->
                        <div class="p-6">
                            <!-- Main EOQ Values -->
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="text-center p-4 bg-blue-50 rounded-xl border-2 border-blue-200 hover:shadow-md transition-shadow">
                                    <div class="text-3xl font-bold text-blue-600 mb-1">${item.eoq_calculated}</div>
                                    <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Economic Order Quantity</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-xl border-2 border-green-200 hover:shadow-md transition-shadow">
                                    <div class="text-3xl font-bold text-green-600 mb-1">${item.safety_stock || '-'}</div>
                                    <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Safety Stock</div>
                                </div>
                                <div class="text-center p-4 bg-orange-50 rounded-xl border-2 border-orange-200 hover:shadow-md transition-shadow">
                                    <div class="text-3xl font-bold text-orange-600 mb-1">${item.rop_calculated || '-'}</div>
                                    <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">Reorder Point</div>
                                </div>
                            </div>
                            
                            <!-- Calculation Parameters -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-5 border">
                                <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    Calculation Parameters
                                </h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <span class="text-sm font-medium text-gray-700">Annual Demand</span>
                                            <span class="text-sm font-bold text-gray-900">${item.annual_demand || '-'}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <span class="text-sm font-medium text-gray-700">Ordering Cost</span>
                                            <span class="text-sm font-bold text-gray-900">Rp ${formatCurrency(item.ordering_cost || 0)}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <span class="text-sm font-medium text-gray-700">Holding Cost</span>
                                            <span class="text-sm font-bold text-gray-900">${item.holding_cost || '-'}%</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <span class="text-sm font-medium text-gray-700">Lead Time</span>
                                            <span class="text-sm font-bold text-gray-900">${item.lead_time || '-'} days</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Current Stock Status -->
                            <div class="mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-indigo-900">Current Stock Level</span>
                                        <div class="text-2xl font-bold text-indigo-600">${item.current_stock || 0} ${item.satuan || ''}</div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-medium text-indigo-900">Status</span>
                                        <div class="text-lg font-semibold">
                                            <span class="px-3 py-1 rounded-full text-xs ${getStockStatusColor(item.status_stok || 'No Data')}">
                                                ${item.status_stok || 'No Data'}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="px-6 py-2 text-sm font-medium bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Show EOQ calculation results modal
        function showEOQResultsModal(eoqData) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div class="inline-block align-top bg-white rounded-lg shadow-xl border w-full max-w-2xl my-8 overflow-hidden text-left transform transition-all">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">🧮 EOQ Calculation Results</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <!-- Main Results -->
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="text-center p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                                    <div class="text-2xl font-bold text-blue-600">${eoqData.eoq.eoq}</div>
                                    <div class="text-sm text-gray-600">Economic Order Quantity</div>
                                    <div class="text-xs text-gray-500 mt-1">${eoqData.eoq.formula}</div>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg border-2 border-green-200">
                                    <div class="text-2xl font-bold text-green-600">${eoqData.safety_stock.safety_stock}</div>
                                    <div class="text-sm text-gray-600">Safety Stock</div>
                                    <div class="text-xs text-gray-500 mt-1">${eoqData.safety_stock.formula}</div>
                                </div>
                                <div class="text-center p-4 bg-orange-50 rounded-lg border-2 border-orange-200">
                                    <div class="text-2xl font-bold text-orange-600">${eoqData.rop.rop}</div>
                                    <div class="text-sm text-gray-600">Reorder Point</div>
                                    <div class="text-xs text-gray-500 mt-1">${eoqData.rop.formula}</div>
                                </div>
                            </div>

                            <!-- Calculation Parameters -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="font-medium text-gray-900 mb-3">📊 Calculation Parameters</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Annual Demand (D):</span>
                                            <span class="text-sm font-medium">${eoqData.eoq.D}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Ordering Cost (S):</span>
                                            <span class="text-sm font-medium">Rp ${formatCurrency(eoqData.eoq.S)}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Holding Cost (H):</span>
                                            <span class="text-sm font-medium">Rp ${formatCurrency(eoqData.eoq.H)}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Lead Time:</span>
                                            <span class="text-sm font-medium">${eoqData.safety_stock.lead_time} days</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Avg Daily Demand:</span>
                                            <span class="text-sm font-medium">${eoqData.safety_stock.davg}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Max Daily Demand:</span>
                                            <span class="text-sm font-medium">${eoqData.safety_stock.dmax}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Status -->
                            ${eoqData.summary ? `
                            <div class="bg-indigo-50 rounded-lg p-4">
                                <h4 class="font-medium text-indigo-900 mb-3">📈 Current Status</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-600">Current Stock:</div>
                                        <div class="text-lg font-semibold text-indigo-600">${eoqData.summary.current_stock}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Stock Status:</div>
                                        <div class="text-lg font-semibold ${getStockStatusColor(getStockStatus(eoqData.summary.current_stock, eoqData.rop.rop))}">${getStockStatus(eoqData.summary.current_stock, eoqData.rop.rop)}</div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="p-4 border-t border-gray-200 flex justify-end">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }


        // Calculate EOQ for item - FIXED VERSION
        function calculateEOQ(id) {
            const item = barangData.find(b => b.id_barang === id);
            if (!item) {
                showNotification('Item not found', 'error');
                return;
            }

            // Check if item has required EOQ parameters
            if (!item.annual_demand || !item.ordering_cost || !item.holding_cost || !item.lead_time) {
                if (confirm('Item missing EOQ parameters. Auto-calculate from historical data?')) {
                    autoCalculateEOQParamsForItem(id);
                    return;
                } else {
                    showNotification('Please set EOQ parameters first', 'warning');
                    return;
                }
            }

            showLoadingSpinner('Calculating EOQ...');
            
            fetch(`/gudang/kelola-data-barang/${id}/calculate-eoq`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingSpinner();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    
                    // Show EOQ results modal
                    showEOQResultsModal(data.data);
                    
                    // Reload data to show updated values
                    loadBarangData();
                } else {
                    showNotification(data.message || 'Error calculating EOQ', 'error');
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('Error calculating EOQ:', error);
                showNotification('Network error while calculating EOQ', 'error');
            });
        }

        // Auto calculate EOQ parameters for specific item
        function autoCalculateEOQParamsForItem(id) {
            showLoadingSpinner('Analyzing historical data...');
            
            fetch(`/gudang/kelola-data-barang/${id}/auto-calculate-eoq-params`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingSpinner();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    
                    // Show detailed results
                    showAutoCalculationResultsModal(data.data);
                    
                    // Reload data to show updated values
                    loadBarangData();
                } else {
                    showNotification(data.message || 'Error calculating parameters', 'error');
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('Error auto-calculating EOQ params:', error);
                showNotification('Network error while calculating parameters', 'error');
            });
        }
        
        // Show auto calculation results modal
        function showAutoCalculationResultsModal(data) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div class="inline-block align-top bg-white rounded-lg shadow-xl border w-full max-w-2xl my-8 overflow-hidden text-left transform transition-all">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">🤖 Auto-Calculated EOQ Parameters</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <!-- Historical Analysis -->
                            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                <h4 class="font-medium text-blue-900 mb-3">📈 Historical Demand Analysis</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-600">Analysis Period:</div>
                                        <div class="text-lg font-semibold text-blue-600">${data.data_period}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Total Usage:</div>
                                        <div class="text-lg font-semibold text-blue-600">${data.demand_analysis.total_usage}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Annual Demand:</div>
                                        <div class="text-lg font-semibold text-blue-600">${Math.round(data.demand_analysis.annual_demand)}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Daily Average:</div>
                                        <div class="text-lg font-semibold text-blue-600">${data.demand_analysis.avg_daily_demand.toFixed(2)}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimated Costs -->
                            <div class="bg-green-50 rounded-lg p-4 mb-4">
                                <h4 class="font-medium text-green-900 mb-3">💰 Estimated Costs</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <div class="text-sm text-gray-600">Ordering Cost:</div>
                                        <div class="text-lg font-semibold text-green-600">Rp ${formatCurrency(data.estimated_costs.ordering_cost)}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Holding Cost:</div>
                                        <div class="text-lg font-semibold text-green-600">${data.estimated_costs.holding_cost}% per year</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Lead Time:</div>
                                        <div class="text-lg font-semibold text-green-600">${data.estimated_costs.lead_time} days</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Max Daily Demand:</div>
                                        <div class="text-lg font-semibold text-green-600">${data.demand_analysis.max_daily_demand.toFixed(2)}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- EOQ Results -->
                            ${data.eoq_calculation && data.eoq_calculation.success ? `
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h4 class="font-medium text-purple-900 mb-3">🧮 Calculated EOQ Values</h4>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="text-center p-3 bg-white rounded-lg border">
                                        <div class="text-xl font-bold text-blue-600">${data.eoq_calculation.eoq.eoq}</div>
                                        <div class="text-xs text-gray-600">EOQ</div>
                                    </div>
                                    <div class="text-center p-3 bg-white rounded-lg border">
                                        <div class="text-xl font-bold text-green-600">${data.eoq_calculation.safety_stock.safety_stock}</div>
                                        <div class="text-xs text-gray-600">Safety Stock</div>
                                    </div>
                                    <div class="text-center p-3 bg-white rounded-lg border">
                                        <div class="text-xl font-bold text-orange-600">${data.eoq_calculation.rop.rop}</div>
                                        <div class="text-xs text-gray-600">ROP</div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="p-4 border-t border-gray-200 flex justify-end">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }


        // Auto calculate EOQ parameters
        function autoCalculateEOQParams() {
            const barangId = document.getElementById('barangId').value;
            if (!barangId) {
                showNotification('Please save the barang first before auto-calculating parameters', 'warning');
                return;
            }
            
            autoCalculateEOQParamsForItem(barangId);
        }
        

        // Recalculate EOQ
        function recalculateEOQ() {
            const barangId = document.getElementById('barangId').value;
            if (!barangId) {
                showNotification('No barang selected', 'error');
                return;
            }

            showLoadingSpinner('Recalculating EOQ...');
            
            fetch(`/gudang/kelola-data-barang/${barangId}/calculate-eoq`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoadingSpinner();
                
                if (data.success) {
                    // Update the EOQ display in the modal
                    if (data.data && data.data.eoq) {
                        document.getElementById('eoqValue').textContent = data.data.eoq.eoq;
                        document.getElementById('safetyStockValue').textContent = data.data.safety_stock.safety_stock;
                        document.getElementById('ropValue').textContent = data.data.rop.rop;
                    }
                    
                    showNotification('EOQ recalculated successfully!', 'success');
                } else {
                    showNotification(data.message || 'Error recalculating EOQ', 'error');
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('Error recalculating EOQ:', error);
                showNotification('Network error while recalculating EOQ', 'error');
            });
        }

        // Show bulk operation results
        function showBulkResultsModal(results) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="flex items-start justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                    <div class="inline-block align-top bg-white rounded-lg shadow-xl border w-full max-w-lg my-8 overflow-hidden text-left transform transition-all">
                        <div class="flex items-center justify-between p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">📊 Bulk EOQ Update Results</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="p-4">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">${results.success_count}</div>
                                    <div class="text-sm text-gray-600">Successful</div>
                                </div>
                                <div class="text-center p-3 bg-red-50 rounded-lg">
                                    <div class="text-2xl font-bold text-red-600">${results.failed_count}</div>
                                    <div class="text-sm text-gray-600">Failed</div>
                                </div>
                            </div>
                            
                            ${results.failed_items.length > 0 ? `
                            <div class="bg-yellow-50 rounded-lg p-3">
                                <h4 class="font-medium text-yellow-900 mb-2">⚠️ Failed Items</h4>
                                <ul class="text-sm text-yellow-800 space-y-1">
                                    ${results.failed_items.slice(0, 5).map(item => `<li>• ${item}</li>`).join('')}
                                    ${results.failed_items.length > 5 ? `<li>• ... and ${results.failed_items.length - 5} more</li>` : ''}
                                </ul>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="p-4 border-t border-gray-200 flex justify-end">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="px-4 py-2 text-sm bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        // Bulk update EOQ
        function bulkUpdateEOQ() {
            const selectedItems = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
    
            if (selectedItems.length === 0) {
                showNotification('Please select items to update EOQ', 'warning');
                return;
            }
            
            const useQueue = selectedItems.length > 5; // Use queue for large batches
            const message = useQueue ? 
                `Queue EOQ calculation for ${selectedItems.length} items? This will process in the background.` :
                `Update EOQ for ${selectedItems.length} selected items? This will process immediately.`;
            
            if (confirm(message)) {
                showLoadingSpinner(useQueue ? 'Queueing EOQ calculations...' : 'Updating EOQ for selected items...');
                
                fetch('/gudang/kelola-data-barang/bulk-calculate-eoq', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        item_ids: selectedItems,
                        use_queue: useQueue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoadingSpinner();
                    
                    if (data.success) {
                        showNotification(data.message, 'success');
                        
                        // Show detailed results if processed immediately
                        if (data.data.processing_method === 'immediate' && data.data.failed_items.length > 0) {
                            showBulkResultsModal(data.data);
                        }
                        
                        loadBarangData(); // Reload data
                        
                        // Clear selections
                        document.querySelectorAll('.item-checkbox:checked').forEach(cb => cb.checked = false);
                        document.getElementById('selectAll').checked = false;
                    } else {
                        showNotification(data.message || 'Error updating EOQ', 'error');
                    }
                })
                .catch(error => {
                    hideLoadingSpinner();
                    console.error('Error bulk updating EOQ:', error);
                    showNotification('Network error while updating EOQ', 'error');
                });
            }
        }

        // Load activity log
        function loadActivityLog() {
            const timeline = document.getElementById('activityTimeline');
            if (!timeline) return;
            
            // Get filter values safely
            const search = document.getElementById('activitySearch')?.value || '';
            const type = document.getElementById('activityType')?.value || '';
            const date = document.getElementById('activityDate')?.value || '';
            
            // Prepare request parameters
            const params = new URLSearchParams({
                search: search,
                activity_type: type,
                activity_date: date,
                per_page: 20
            });
            
            console.log('📝 Loading activity log...');
            showLoadingSpinner('Loading activity log...');
            
            fetch(`/gudang/kelola-data-barang/activity-log?${params}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                hideLoadingSpinner();
                console.log('✅ Activity log loaded:', data);
                
                if (data.success) {
                    if (data.data.length === 0) {
                        timeline.innerHTML = `
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500">No activity found</p>
                            </div>
                        `;
                        return;
                    }
                    
                    timeline.innerHTML = data.data.map(activity => `
                        <div class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-${activity.color}-100 text-${activity.color}-600 rounded-full flex items-center justify-center text-sm">
                                    ${activity.icon}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">${activity.action}</p>
                                    <p class="text-xs text-gray-500">${formatDateTime(activity.timestamp)}</p>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">${activity.item}</p>
                                ${activity.item_code !== '-' ? `<p class="text-xs text-gray-400">${activity.item_code}</p>` : ''}
                                <p class="text-xs text-gray-500 mt-1">${activity.details}</p>
                                <p class="text-xs text-gray-400 mt-1">by ${activity.user}</p>
                            </div>
                        </div>
                    `).join('');
                } else {
                    timeline.innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-red-500">Error loading activity log: ${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('❌ Activity log error:', error);
                timeline.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-red-500">Network error loading activity log: ${error.message}</p>
                        <button onclick="loadActivityLog()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            🔄 Retry
                        </button>
                    </div>
                `;
            });
        }
        
        

        // Export data
        function exportData() {
            showLoadingSpinner('Preparing export...');
    
        // Get current filter values safely
        const searchText = document.getElementById('searchInput')?.value || '';
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const eoqFilter = document.getElementById('eoqFilter')?.value || '';
        
        // Prepare request parameters
        const params = new URLSearchParams({
            search: searchText,
            status_filter: statusFilter,
            eoq_filter: eoqFilter
        });
        
        fetch(`/gudang/kelola-data-barang/export?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'text/csv',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            hideLoadingSpinner();
            
            if (response.ok) {
                return response.blob();
            } else {
                throw new Error(`Export failed: ${response.status} ${response.statusText}`);
            }
        })
        .then(blob => {
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `data-barang-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
            
            showNotification('Data exported successfully!', 'success');
        })
        .catch(error => {
            hideLoadingSpinner();
            console.error('Export error:', error);
            showNotification(`Export failed: ${error.message}`, 'error');
        });
        }

        // Utility functions
        function getStockStatusColor(status) {
            if (!status) return 'bg-gray-100 text-gray-800';
            
            const normalizedStatus = status.toString().toLowerCase();
            switch(normalizedStatus) {
                case 'aman':
                    return 'bg-green-100 text-green-800';
                case 'perlu restock':
                    return 'bg-yellow-100 text-yellow-800';
                case 'habis':
                    return 'bg-red-100 text-red-800';
                case 'no stock data':
                case 'no data':
                    return 'bg-gray-100 text-gray-600';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }
        // Determine stock status based on current stock and ROP
        function getStockStatus(currentStock, rop) {
            const stock = parseInt(currentStock) || 0;
            const reorderPoint = parseInt(rop) || 0;
            
            if (stock <= 0) {
                return 'Habis';
            } else if (stock <= reorderPoint) {
                return 'Perlu Restock';
            } else {
                return 'Aman';
            }
        }

        function formatCurrency(amount) {
            if (!amount || isNaN(amount)) return '0';
            return new Intl.NumberFormat('id-ID').format(amount);
        }
        
         function formatDateTime(dateString) {
              if (!dateString) return '-';
            try {
                return new Date(dateString).toLocaleString('id-ID');
            } catch (e) {
                return dateString;
            }
        }

        function showLoadingSpinner(message) {
            hideLoadingSpinner(); 
            
            const spinner = document.createElement('div');
            spinner.id = 'loadingSpinner';
            spinner.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
            spinner.innerHTML = `
                <div class="bg-white rounded-lg p-6 flex items-center space-x-3 shadow-xl">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-emerald-600"></div>
                    <span class="text-gray-700 font-medium">${message || 'Loading...'}</span>
                </div>
            `;
            document.body.appendChild(spinner);
        }

        function hideLoadingSpinner() {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) spinner.remove();
        }

       function showNotification(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 max-w-md`;
        notification.innerHTML = `
            <div class="flex items-center">
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                    ✕
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentElement) notification.remove();
                }, 300);
            }
        }, 5000);
        }

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</body>
</html>