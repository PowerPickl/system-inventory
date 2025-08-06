<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Service History</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                       class="flex items-center px-3 py-2 text-purple-300 hover:text-white hover:bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        Service Request
                    </a>

                    <!-- Search Barang -->
                    <a href="{{ route('kasir.search-barang') }}" 
                       class="flex items-center px-3 py-2 text-purple-300 hover:text-white hover:bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Katalog Barang
                    </a>

                    <!-- History Transaksi -->
                    <a href="{{ route('kasir.history-transaksi') }}" 
                       class="flex items-center px-3 py-2 text-white bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
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
                            <h1 class="text-2xl font-semibold text-gray-900">Service History</h1>
                            <p class="text-gray-600 text-sm">Histori service pelanggan dan penggunaan sparepart</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Stats Summary -->
                            <div id="stats-summary" class="flex space-x-4 text-sm">
                                <div class="text-center">
                                    <div class="font-semibold text-purple-600" id="total-transaksi">-</div>
                                    <div class="text-gray-500">Total</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-green-600" id="total-penjualan">-</div>
                                    <div class="text-gray-500">Penjualan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <!-- Quick Filters -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Cepat</label>
                            <div class="flex flex-wrap gap-2">
                                <button data-filter="today" class="quick-filter-btn px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200 text-sm">
                                    Hari Ini
                                </button>
                                <button data-filter="yesterday" class="quick-filter-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm">
                                    Kemarin
                                </button>
                                <button data-filter="this_week" class="quick-filter-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm">
                                    Minggu Ini
                                </button>
                                <button data-filter="this_month" class="quick-filter-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm">
                                    Bulan Ini
                                </button>
                                <button data-filter="all" class="quick-filter-btn px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm">
                                    Semua
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Transaksi</label>
                                <div class="relative">
                                    <input type="text" 
                                           id="search-input"
                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" 
                                           placeholder="No. transaksi, customer...">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Semua Status</option>
                                    <option value="Progress">Progress</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>

                            <!-- Date From -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                                <input type="date" id="date-from" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                                <input type="date" id="date-to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </div>
                        </div>

                        <!-- Advanced Filters (Collapsible) -->
                        <div class="mt-4">
                            <button id="toggle-advanced" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                <span id="advanced-text">Tampilkan Filter Lanjutan</span>
                                <svg id="advanced-icon" class="inline w-4 h-4 ml-1 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <div id="advanced-filters" class="hidden mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4 pt-4 border-t border-gray-200">
                                <!-- Total Range -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Range Total</label>
                                    <div class="flex space-x-2">
                                        <input type="number" id="total-min" placeholder="Min" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                        <input type="number" id="total-max" placeholder="Max" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    </div>
                                </div>

                                <!-- Sort By -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                                    <select id="sort-by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                        <option value="tanggal_transaksi">Tanggal</option>
                                        <option value="nomor_transaksi">No. Transaksi</option>
                                        <option value="total_harga">Total</option>
                                        <option value="nama_customer">Customer</option>
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                                    <select id="sort-order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                        <option value="desc">Terbaru</option>
                                        <option value="asc">Terlama</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2 mt-4">
                                <button id="search-btn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Cari History
                                </button>
                                <button id="reset-btn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Reset Filter
                                </button>
                                <button id="export-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <!-- Results Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Riwayat Transaksi</h3>
                            <div class="flex items-center space-x-4">
                                <!-- Results Info -->
                                <div id="results-info" class="text-sm text-gray-600">
                                    Menampilkan <span id="results-from">0</span>-<span id="results-to">0</span> dari <span id="results-total">0</span> transaksi
                                </div>
                                
                                <!-- Per Page -->
                                <select id="per-page" class="px-3 py-1 border border-gray-300 rounded text-sm">
                                    <option value="20">20 per halaman</option>
                                    <option value="50">50 per halaman</option>
                                    <option value="100">100 per halaman</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loading-state" class="hidden p-8 text-center">
                        <div class="inline-flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-600">Memuat Service History...</span>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div id="results-container" class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="results-tbody" class="bg-white divide-y divide-gray-200">
                                <!-- Dynamic content will be inserted here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="hidden p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada transaksi ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah filter atau rentang tanggal.</p>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination-container" class="px-6 py-4 border-t border-gray-200">
                        <!-- Dynamic pagination will be inserted here -->
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            Detail Transaksi
                        </h3>
                        <div id="modal-content">
                            <!-- Dynamic content will be inserted here -->
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="close-modal" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // History Transaksi JavaScript Implementation
        class HistoryTransaksi {
            constructor() {
                this.currentPage = 1;
                this.perPage = 20;
                this.filters = {
                    search: '',
                    status: '',
                    date_from: '',
                    date_to: '',
                    total_min: '',
                    total_max: '',
                    sort_by: 'tanggal_transaksi',
                    sort_order: 'desc',
                    quick_filter: 'today'
                };

                this.init();
                this.loadInitialData();
            }

            init() {
                // Bind events
                document.getElementById('search-input').addEventListener('input', this.debounce(this.handleSearch.bind(this), 500));
                document.getElementById('status-filter').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('date-from').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('date-to').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('total-min').addEventListener('input', this.debounce(this.handleFilterChange.bind(this), 500));
                document.getElementById('total-max').addEventListener('input', this.debounce(this.handleFilterChange.bind(this), 500));
                document.getElementById('sort-by').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('sort-order').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('per-page').addEventListener('change', this.handlePerPageChange.bind(this));
                
                document.getElementById('search-btn').addEventListener('click', this.handleSearch.bind(this));
                document.getElementById('reset-btn').addEventListener('click', this.resetFilters.bind(this));
                document.getElementById('export-btn').addEventListener('click', this.exportHistory.bind(this));
                document.getElementById('toggle-advanced').addEventListener('click', this.toggleAdvancedFilters.bind(this));
                document.getElementById('close-modal').addEventListener('click', this.closeModal.bind(this));

                // Quick filter buttons
                document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                    btn.addEventListener('click', this.handleQuickFilter.bind(this));
                });

                // Close modal on background click
                document.getElementById('detail-modal').addEventListener('click', (e) => {
                    if (e.target.id === 'detail-modal') {
                        this.closeModal();
                    }
                });
            }

            async loadInitialData() {
                await this.loadStats();
                await this.searchHistory();
            }

            async loadStats() {
                try {
                    const params = new URLSearchParams({
                        period: this.filters.quick_filter
                    });

                    const response = await fetch(`/kasir/history-transaksi/stats?${params}`);
                    const result = await response.json();
                    
                    if (result.success) {
                        document.getElementById('total-transaksi').textContent = result.data.total_transaksi;
                        document.getElementById('total-penjualan').textContent = result.data.total_penjualan_formatted;
                    }
                } catch (error) {
                    console.error('Error loading stats:', error);
                }
            }

            async searchHistory() {
                this.showLoading();
                
                try {
                    const params = new URLSearchParams({
                        ...this.filters,
                        page: this.currentPage,
                        per_page: this.perPage
                    });

                    const response = await fetch(`/kasir/history-transaksi/data?${params}`);
                    const result = await response.json();

                    if (result.success) {
                        this.renderResults(result.data);
                        this.renderPagination(result.pagination);
                        this.updateResultsInfo(result.pagination);
                    } else {
                        this.showError(result.message);
                    }
                } catch (error) {
                    console.error('Error searching history:', error);
                    this.showError('Terjadi kesalahan saat mengambil service history');
                } finally {
                    this.hideLoading();
                }
            }

            renderResults(data) {
                const tbody = document.getElementById('results-tbody');
                const emptyState = document.getElementById('empty-state');
                const resultsContainer = document.getElementById('results-container');

                if (data.length === 0) {
                    resultsContainer.classList.add('hidden');
                    emptyState.classList.remove('hidden');
                    return;
                }

                resultsContainer.classList.remove('hidden');
                emptyState.classList.add('hidden');

                tbody.innerHTML = data.map(transaksi => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${transaksi.formatted_nomor}</div>
                            <div class="text-xs text-gray-500">${transaksi.jenis_transaksi}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${transaksi.tanggal_formatted}</div>
                            <div class="text-xs text-gray-500">${transaksi.tanggal_readable}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${transaksi.nama_customer || '-'}</div>
                            ${transaksi.kendaraan ? `<div class="text-xs text-gray-500">${transaksi.kendaraan}</div>` : ''}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${transaksi.jumlah_item} item</div>
                            <div class="text-xs text-gray-500">${transaksi.total_qty} qty</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${transaksi.total_formatted}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="${transaksi.status_badge_class}">
                                ${transaksi.status_transaksi}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${transaksi.kasir_nama}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="historyTransaksi.showDetail(${transaksi.id_transaksi})" 
                                    class="text-purple-600 hover:text-purple-900">
                                Detail
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            renderPagination(pagination) {
                const container = document.getElementById('pagination-container');
                
                if (pagination.last_page <= 1) {
                    container.innerHTML = '';
                    return;
                }

                let paginationHTML = '<div class="flex items-center justify-between">';
                
                // Previous button
                paginationHTML += `
                    <button onclick="historyTransaksi.goToPage(${pagination.current_page - 1})" 
                            ${pagination.current_page <= 1 ? 'disabled' : ''}
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Previous
                    </button>
                `;

                // Page numbers
                paginationHTML += '<div class="flex space-x-1">';
                
                const startPage = Math.max(1, pagination.current_page - 2);
                const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationHTML += `
                        <button onclick="historyTransaksi.goToPage(${i})" 
                                class="px-3 py-1 text-sm rounded ${i === pagination.current_page ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}">
                            ${i}
                        </button>
                    `;
                }
                
                paginationHTML += '</div>';

                // Next button
                paginationHTML += `
                    <button onclick="historyTransaksi.goToPage(${pagination.current_page + 1})" 
                            ${pagination.current_page >= pagination.last_page ? 'disabled' : ''}
                            class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                    </button>
                `;

                paginationHTML += '</div>';
                container.innerHTML = paginationHTML;
            }

            updateResultsInfo(pagination) {
                document.getElementById('results-from').textContent = pagination.from || 0;
                document.getElementById('results-to').textContent = pagination.to || 0;
                document.getElementById('results-total').textContent = pagination.total || 0;
            }

            async showDetail(id) {
                try {
                    const response = await fetch(`/kasir/history-transaksi/${id}/detail`);
                    const result = await response.json();

                    if (result.success) {
                        const { transaksi, detail_items, summary } = result.data;
                        
                        document.getElementById('modal-title').textContent = `Detail ${transaksi.formatted_nomor}`;
                        document.getElementById('modal-content').innerHTML = `
                            <!-- Transaction Info -->
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nomor Transaksi</label>
                                        <p class="text-sm text-gray-900">${transaksi.formatted_nomor}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                        <p class="text-sm text-gray-900">${transaksi.tanggal_formatted}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                                        <p class="text-sm text-gray-900">${transaksi.nama_customer || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kendaraan</label>
                                        <p class="text-sm text-gray-900">${transaksi.kendaraan || '-'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            ${transaksi.status_transaksi === 'Selesai' ? 'bg-green-100 text-green-800' : 
                                              transaksi.status_transaksi === 'Progress' ? 'bg-orange-100 text-orange-800' : 
                                              'bg-red-100 text-red-800'}">
                                            ${transaksi.status_transaksi}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">User</label>
                                        <p class="text-sm text-gray-900">${transaksi.kasir_nama}</p>
                                    </div>
                                </div>
                                ${transaksi.keterangan ? `
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                    <p class="text-sm text-gray-900">${transaksi.keterangan}</p>
                                </div>
                                ` : ''}
                            </div>

                            <!-- Items Table -->
                            <div class="mb-4">
                                <h4 class="text-md font-semibold text-gray-900 mb-2">Detail Items</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Barang</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-700">Qty</th>
                                                <th class="px-3 py-2 text-right font-medium text-gray-700">Harga</th>
                                                <th class="px-3 py-2 text-right font-medium text-gray-700">Subtotal</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-700">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            ${detail_items.map(item => `
                                                <tr>
                                                    <td class="px-3 py-2">
                                                        <div class="font-medium text-gray-900">${item.nama_barang}</div>
                                                        <div class="text-xs text-gray-500">${item.kode_barang} - ${item.kategori}</div>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">${item.qty} ${item.satuan}</td>
                                                    <td class="px-3 py-2 text-right">${item.harga_satuan_formatted}</td>
                                                    <td class="px-3 py-2 text-right font-medium">${item.subtotal_formatted}</td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="${item.status_badge_class}">
                                                            ${item.status_permintaan}
                                                        </span>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-600">
                                        Total: ${summary.jumlah_item} item (${summary.total_qty} qty)
                                    </div>
                                    <div class="text-lg font-semibold text-gray-900">
                                        ${summary.subtotal_formatted}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('detail-modal').classList.remove('hidden');
                    } else {
                        alert('Gagal mengambil detail transaksi');
                    }
                } catch (error) {
                    console.error('Error showing detail:', error);
                    alert('Terjadi kesalahan saat mengambil detail transaksi');
                }
            }

            closeModal() {
                document.getElementById('detail-modal').classList.add('hidden');
            }

            goToPage(page) {
                this.currentPage = page;
                this.searchHistory();
            }

            handleQuickFilter(e) {
                // Update button appearance
                document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                    btn.classList.remove('bg-purple-100', 'text-purple-700');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });
                e.target.classList.remove('bg-gray-100', 'text-gray-700');
                e.target.classList.add('bg-purple-100', 'text-purple-700');

                // Set filter and search
                this.filters.quick_filter = e.target.dataset.filter;
                this.currentPage = 1;
                
                // Clear date inputs if using quick filter
                if (this.filters.quick_filter !== 'all') {
                    document.getElementById('date-from').value = '';
                    document.getElementById('date-to').value = '';
                    this.filters.date_from = '';
                    this.filters.date_to = '';
                }

                this.searchHistory();
                this.loadStats();
            }

            handleSearch() {
                this.filters.search = document.getElementById('search-input').value;
                this.currentPage = 1;
                this.searchHistory();
            }

            handleFilterChange() {
                this.filters.status = document.getElementById('status-filter').value;
                this.filters.date_from = document.getElementById('date-from').value;
                this.filters.date_to = document.getElementById('date-to').value;
                this.filters.total_min = document.getElementById('total-min').value;
                this.filters.total_max = document.getElementById('total-max').value;
                this.filters.sort_by = document.getElementById('sort-by').value;
                this.filters.sort_order = document.getElementById('sort-order').value;
                
                // Clear quick filter if custom dates are set
                if (this.filters.date_from || this.filters.date_to) {
                    this.filters.quick_filter = '';
                    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                        btn.classList.remove('bg-purple-100', 'text-purple-700');
                        btn.classList.add('bg-gray-100', 'text-gray-700');
                    });
                }

                this.currentPage = 1;
                this.searchHistory();
            }

            handlePerPageChange() {
                this.perPage = parseInt(document.getElementById('per-page').value);
                this.currentPage = 1;
                this.searchHistory();
            }

            resetFilters() {
                // Reset form fields
                document.getElementById('search-input').value = '';
                document.getElementById('status-filter').value = '';
                document.getElementById('date-from').value = '';
                document.getElementById('date-to').value = '';
                document.getElementById('total-min').value = '';
                document.getElementById('total-max').value = '';
                document.getElementById('sort-by').value = 'tanggal_transaksi';
                document.getElementById('sort-order').value = 'desc';

                // Reset quick filter buttons
                document.querySelectorAll('.quick-filter-btn').forEach(btn => {
                    btn.classList.remove('bg-purple-100', 'text-purple-700');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });
                document.querySelector('[data-filter="today"]').classList.remove('bg-gray-100', 'text-gray-700');
                document.querySelector('[data-filter="today"]').classList.add('bg-purple-100', 'text-purple-700');

                // Reset filters object
                this.filters = {
                    search: '',
                    status: '',
                    date_from: '',
                    date_to: '',
                    total_min: '',
                    total_max: '',
                    sort_by: 'tanggal_transaksi',
                    sort_order: 'desc',
                    quick_filter: 'today'
                };

                this.currentPage = 1;
                this.searchHistory();
                this.loadStats();
            }

            toggleAdvancedFilters() {
                const container = document.getElementById('advanced-filters');
                const icon = document.getElementById('advanced-icon');
                const text = document.getElementById('advanced-text');

                if (container.classList.contains('hidden')) {
                    container.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                    text.textContent = 'Sembunyikan Filter Lanjutan';
                } else {
                    container.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                    text.textContent = 'Tampilkan Filter Lanjutan';
                }
            }

            async exportHistory() {
                try {
                    const params = new URLSearchParams(this.filters);
                    const response = await fetch(`/kasir/history-transaksi/export?${params}`);
                    const result = await response.json();

                    if (result.success) {
                        // Handle export download
                        alert('Export berhasil!');
                    } else {
                        alert(result.message || 'Export gagal');
                    }
                } catch (error) {
                    console.error('Error exporting:', error);
                    alert('Terjadi kesalahan saat export');
                }
            }

            showLoading() {
                document.getElementById('loading-state').classList.remove('hidden');
                document.getElementById('results-container').classList.add('hidden');
                document.getElementById('empty-state').classList.add('hidden');
            }

            hideLoading() {
                document.getElementById('loading-state').classList.add('hidden');
            }

            showError(message) {
                alert(message);
            }

            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.historyTransaksi = new HistoryTransaksi();
        });
    </script>
</body>
</html>