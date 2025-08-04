<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Katalog Barang</title>

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
                       class="flex items-center px-3 py-2 text-white bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Katalog Barang
                    </a>

                    <!-- History Transaksi -->
                    <a href="{{ route('kasir.history-transaksi') }}" 
                       class="flex items-center px-3 py-2 text-purple-300 hover:text-white hover:bg-purple-700 rounded-lg transition-colors duration-200 mb-1">
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
                            <h1 class="text-2xl font-semibold text-gray-900">Katalog Barang</h1>
                            <p class="text-gray-600 text-sm">Cari dan lihat detail barang untuk service</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Quick Stats -->
                            <div id="quick-stats" class="flex space-x-4 text-sm">
                                <div class="text-center">
                                    <div class="font-semibold text-purple-600" id="total-barang">-</div>
                                    <div class="text-gray-500">Total Barang</div>
                                </div>
                                <div class="text-center">
                                    <div class="font-semibold text-green-600" id="barang-tersedia">-</div>
                                    <div class="text-gray-500">Terdaftar</div> <!-- UBAH dari "Tersedia" ke "Terdaftar" -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Search & Filter Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <!-- Main Search -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Barang</label>
                                
                                <div class="relative">
                                    <input type="text" 
                                           id="search-input"
                                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" 
                                           placeholder="Nama barang, kode, merk, atau model...">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                       
                                    </div>
                                </div>
                            </div>

                            <!-- Kategori Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                                <select id="kategori-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Semua Kategori</option>
                                </select>
                            </div>

                            <!-- Availability Filter - REMOVED since kasir only sees available items -->
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
                                <!-- Price Range -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Range Harga</label>
                                    <div class="flex space-x-2">
                                        <input type="number" id="harga-min" placeholder="Min" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                        <input type="number" id="harga-max" placeholder="Max" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    </div>
                                </div>

                                <!-- Sort By -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                                    <select id="sort-by" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                        <option value="nama_barang">Nama Barang</option>
                                        <option value="harga">Harga</option>
                                        <option value="kode_barang">Kode Barang</option>
                                    </select>
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                                    <select id="sort-order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                        <option value="asc">A-Z / Rendah-Tinggi</option>
                                        <option value="desc">Z-A / Tinggi-Rendah</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2 mt-4">
                                <button id="search-btn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Cari Barang
                                </button>
                                <button id="reset-btn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Reset Filter
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
                            <h3 class="text-lg font-semibold text-gray-900">Hasil Pencarian</h3>
                            <div class="flex items-center space-x-4">
                                <!-- Results Info -->
                                <div id="results-info" class="text-sm text-gray-600">
                                    Menampilkan <span id="results-from">0</span>-<span id="results-to">0</span> dari <span id="results-total">0</span> barang
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
                            <span class="text-gray-600">Mencari barang...</span>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div id="results-container" class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Aksi</th>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada barang ditemukan</h3>
                        <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau filter pencarian.</p>
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

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Detail Barang
                            </h3>
                            <div id="modal-content">
                                <!-- Dynamic content will be inserted here -->
                            </div>
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
        // Search Barang JavaScript Implementation
        class SearchBarang {
            constructor() {
                this.currentPage = 1;
                this.perPage = 20;
                this.filters = {
                    search: '',
                    kategori_id: '',
                    harga_min: '',
                    harga_max: '',
                    sort_by: 'nama_barang',
                    sort_order: 'asc'
                };

                this.init();
                this.loadInitialData();
            }

            init() {
                // Bind events
                document.getElementById('search-input').addEventListener('input', this.debounce(this.handleSearch.bind(this), 500));
                document.getElementById('kategori-filter').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('harga-min').addEventListener('input', this.debounce(this.handleFilterChange.bind(this), 500));
                document.getElementById('harga-max').addEventListener('input', this.debounce(this.handleFilterChange.bind(this), 500));
                document.getElementById('sort-by').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('sort-order').addEventListener('change', this.handleFilterChange.bind(this));
                document.getElementById('per-page').addEventListener('change', this.handlePerPageChange.bind(this));
                
                document.getElementById('search-btn').addEventListener('click', this.handleSearch.bind(this));
                document.getElementById('reset-btn').addEventListener('click', this.resetFilters.bind(this));
                document.getElementById('toggle-advanced').addEventListener('click', this.toggleAdvancedFilters.bind(this));
                document.getElementById('close-modal').addEventListener('click', this.closeModal.bind(this));

                // Close modal on background click
                document.getElementById('detail-modal').addEventListener('click', (e) => {
                    if (e.target.id === 'detail-modal') {
                        this.closeModal();
                    }
                });
            }

            async loadInitialData() {
                await this.loadKategoriOptions();
                await this.loadStats();
                await this.searchBarang();
            }

            async loadKategoriOptions() {
                try {
                    const response = await fetch('/kasir/search-barang/kategori-options');
                    const result = await response.json();
                    
                    if (result.success) {
                        const select = document.getElementById('kategori-filter');
                        select.innerHTML = '<option value="">Semua Kategori</option>';
                        
                        result.data.forEach(kategori => {
                            const option = document.createElement('option');
                            option.value = kategori.id;
                            option.textContent = `${kategori.nama} (${kategori.jumlah_barang})`;
                            select.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Error loading kategori options:', error);
                }
            }

            async loadStats() {
                try {
                    const response = await fetch('/kasir/search-barang/stats');
                    const result = await response.json();
                    
                    if (result.success) {
                        document.getElementById('total-barang').textContent = result.data.total_barang;
                        document.getElementById('barang-tersedia').textContent = result.data.barang_tersedia;
                    }
                } catch (error) {
                    console.error('Error loading stats:', error);
                }
            }

            async searchBarang() {
                this.showLoading();
                
                try {
                    const params = new URLSearchParams({
                        ...this.filters,
                        page: this.currentPage,
                        per_page: this.perPage
                    });

                    const response = await fetch(`/kasir/search-barang/search?${params}`);
                    const result = await response.json();

                    if (result.success) {
                        this.renderResults(result.data);
                        this.renderPagination(result.pagination);
                        this.updateResultsInfo(result.pagination);
                    } else {
                        this.showError(result.message);
                    }
                } catch (error) {
                    console.error('Error searching barang:', error);
                    this.showError('Terjadi kesalahan saat mencari barang');
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

                tbody.innerHTML = data.map(barang => `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">${barang.nama_lengkap}</div>
                                <div class="text-sm text-gray-500">${barang.kode_barang}</div>
                                ${barang.merk ? `<div class="text-xs text-gray-400">${barang.merk}${barang.model_tipe ? ' ' + barang.model_tipe : ''}</div>` : ''}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="${barang.kategori_badge.class}" style="${barang.kategori_badge.style}">
                                ${barang.kategori_badge.icon} ${barang.kategori_badge.nama}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${barang.harga_jual_formatted}</div>
                            <div class="text-xs text-gray-500">per ${barang.satuan}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">${barang.satuan}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium w-36">
                            <div class="flex flex-col space-y-1">
                                <button onclick="searchBarang.showDetail(${barang.id_barang})" 
                                        class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs hover:bg-purple-200 transition-colors">
                                    Detail
                                </button>
                            </div>
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
                    <button onclick="searchBarang.goToPage(${pagination.current_page - 1})" 
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
                        <button onclick="searchBarang.goToPage(${i})" 
                                class="px-3 py-1 text-sm rounded ${i === pagination.current_page ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}">
                            ${i}
                        </button>
                    `;
                }
                
                paginationHTML += '</div>';

                // Next button
                paginationHTML += `
                    <button onclick="searchBarang.goToPage(${pagination.current_page + 1})" 
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
                    const response = await fetch(`/kasir/search-barang/${id}/detail`);
                    const result = await response.json();

                    if (result.success) {
                        const barang = result.data;
                        document.getElementById('modal-title').textContent = `Detail ${barang.nama_lengkap}`;
                        document.getElementById('modal-content').innerHTML = `
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kode Barang</label>
                                        <p class="text-sm text-gray-900">${barang.kode_barang}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                        <p class="text-sm text-gray-900">${barang.nama_barang}</p>
                                    </div>
                                </div>

                                ${barang.merk || barang.model_tipe ? `
                                <div class="grid grid-cols-2 gap-4">
                                    ${barang.merk ? `
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Merk</label>
                                        <p class="text-sm text-gray-900">${barang.merk}</p>
                                    </div>
                                    ` : ''}
                                    ${barang.model_tipe ? `
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Model/Tipe</label>
                                        <p class="text-sm text-gray-900">${barang.model_tipe}</p>
                                    </div>
                                    ` : ''}
                                </div>
                                ` : ''}

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                        <p class="text-sm text-gray-900">${barang.kategori ? barang.kategori.nama : 'Uncategorized'}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                        <p class="text-sm text-gray-900">${barang.satuan}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                                        <p class="text-lg font-semibold text-green-600">${barang.harga_jual_formatted}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                        <p class="text-lg font-semibold text-gray-900">${barang.satuan}</p>
                                    </div>
                                </div>

                                ${barang.deskripsi ? `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <p class="text-sm text-gray-900">${barang.deskripsi}</p>
                                </div>
                                ` : ''}

                                ${barang.keterangan_detail ? `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Keterangan Detail</label>
                                    <p class="text-sm text-gray-900">${barang.keterangan_detail}</p>
                                </div>
                                ` : ''}
                            </div>
                        `;
                        
                        document.getElementById('detail-modal').classList.remove('hidden');
                    } else {
                        alert('Gagal mengambil detail barang');
                    }
                } catch (error) {
                    console.error('Error showing detail:', error);
                    alert('Terjadi kesalahan saat mengambil detail barang');
                }
            }

            closeModal() {
                document.getElementById('detail-modal').classList.add('hidden');
            }

            goToPage(page) {
                this.currentPage = page;
                this.searchBarang();
            }

            handleSearch() {
                this.filters.search = document.getElementById('search-input').value;
                this.currentPage = 1;
                this.searchBarang();
            }

            handleFilterChange() {
                this.filters.kategori_id = document.getElementById('kategori-filter').value;
                this.filters.harga_min = document.getElementById('harga-min').value;
                this.filters.harga_max = document.getElementById('harga-max').value;
                this.filters.sort_by = document.getElementById('sort-by').value;
                this.filters.sort_order = document.getElementById('sort-order').value;
                this.currentPage = 1;
                this.searchBarang();
            }

            handlePerPageChange() {
                this.perPage = parseInt(document.getElementById('per-page').value);
                this.currentPage = 1;
                this.searchBarang();
            }

            resetFilters() {
                // Reset form fields
                document.getElementById('search-input').value = '';
                document.getElementById('kategori-filter').value = '';
                document.getElementById('harga-min').value = '';
                document.getElementById('harga-max').value = '';
                document.getElementById('sort-by').value = 'nama_barang';
                document.getElementById('sort-order').value = 'asc';

                // Reset filters object
                this.filters = {
                    search: '',
                    kategori_id: '',
                    harga_min: '',
                    harga_max: '',
                    sort_by: 'nama_barang',
                    sort_order: 'asc'
                };

                this.currentPage = 1;
                this.searchBarang();
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

            showLoading() {
                document.getElementById('loading-state').classList.remove('hidden');
                document.getElementById('results-container').classList.add('hidden');
                document.getElementById('empty-state').classList.add('hidden');
            }

            hideLoading() {
                document.getElementById('loading-state').classList.add('hidden');
            }

            showError(message) {
                // Simple error handling - you can improve this
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
            window.searchBarang = new SearchBarang();
        });
    </script>
</body>
</html>