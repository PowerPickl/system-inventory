<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Verifikasi Permintaan - Gudang Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <style>
        /* Custom green colors for better visibility */
        .btn-green {
            background-color: #059669;
            border-color: #059669;
        }
        .btn-green:hover {
            background-color: #047857;
            border-color: #047857;
        }
        .btn-green:focus {
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.3);
        }
        
        /* Custom dropdown styling */
        .custom-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 8px center;
            background-repeat: no-repeat;
            background-size: 16px 16px;
            padding-right: 32px;
        }
        
        /* Better spacing for action buttons */
        .action-btn-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <x-gudang.sidebar active="verification" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Verifikasi Permintaan</h1>
                            <p class="text-gray-600 text-sm mt-1">Validasi permintaan barang dari transaksi service</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Auto Refresh Toggle -->
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="autoRefresh" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                                <label for="autoRefresh" class="text-sm text-gray-700">Auto Refresh</label>
                            </div>
                            
                            <!-- Refresh Button -->
                            <button id="btn-refresh" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium shadow-sm">
                                🔄 Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 space-y-6">
                <!-- Statistics Cards -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Validation Statistics</h3>
                        <div class="grid grid-cols-5 gap-6">
                            <!-- Pending -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 id="pending-count" class="text-2xl font-bold text-yellow-600 mb-1">0</h3>
                                    <p class="text-sm text-gray-600">Pending</p>
                                </div>
                            </div>

                            <!-- Approved -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <h3 id="approved-count" class="text-2xl font-bold text-green-600 mb-1">0</h3>
                                    <p class="text-sm text-gray-600">Approved</p>
                                </div>
                            </div>

                            <!-- Rejected -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <h3 id="rejected-count" class="text-2xl font-bold text-red-600 mb-1">0</h3>
                                    <p class="text-sm text-gray-600">Rejected</p>
                                </div>
                            </div>

                            <!-- Total Transactions -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <h3 id="total-transactions" class="text-2xl font-bold text-blue-600 mb-1">0</h3>
                                    <p class="text-sm text-gray-600">Total Transaksi</p>
                                </div>
                            </div>
                             <!-- Today's Validations -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                    </div>
                                    <h3 id="today-validations" class="text-2xl font-bold text-purple-600 mb-1">0</h3>
                                    <p class="text-sm text-gray-600">Hari Ini</p>
                                </div>
                            </div>          
                        </div>
                    </div>
                </div>

                <!-- Priority Pending Requests -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200" id="pending-section">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Permintaan Menunggu Validasi</h3>
                                    <p class="text-sm text-gray-600">Prioritas tinggi - butuh validasi segera</p>
                                </div>
                            </div>
                            <button onclick="bulkApproveVisible()" class="btn-green text-white px-5 py-3 rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                                ✅ Approve All Available
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="pending-requests" class="space-y-4">
                            <!-- Pending requests will be populated here -->
                        </div>
                    </div>
                </div>

                <!-- All Transactions Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Semua Transaksi Service</h3>
                            <div class="action-btn-group">
                                <select id="status-filter" class="custom-select px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="">Semua Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                                <input type="text" id="search-input" placeholder="Cari transaksi..." 
                                       class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="all-transactions-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer & Service</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Items</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Estimasi</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="all-transactions-body" class="bg-white divide-y divide-gray-200">
                                <!-- All transactions will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Detail Transaksi - COMPACT VERSION -->
    <div id="modal-detail" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg w-full max-w-5xl max-h-[85vh] overflow-hidden shadow-2xl">
                <!-- Modal Header - Compact -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Validasi Transaksi</h3>
                        <p id="modal-transaction-info" class="text-sm text-gray-600">Loading...</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="modal-pending-badge" class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                            Loading...
                        </span>
                        <button id="btn-close-detail" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Content - Compact & Scrollable -->
                <div id="detail-content" class="p-4 overflow-y-auto max-h-[calc(85vh-80px)]">
                    <!-- Content will be populated by JavaScript -->
                    <div class="text-center py-8">
                        <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                        <p class="text-gray-500">Loading data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let autoRefreshInterval;
        let currentFilter = '';
        let currentSearch = '';

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Format time
        function formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };
            
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        // Load verification requests
        async function loadVerificationRequests() {
            try {
                const response = await fetch('/gudang/verifikasi-permintaan/data?' + new URLSearchParams({
                    status: currentFilter,
                    search: currentSearch
                }), {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    updateStats(data.stats);
                    renderPendingRequests(data.pending_requests);
                    renderAllTransactions(data.all_transactions);
                } else {
                    showNotification('Error loading data: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error loading verification requests:', error);
                showNotification('Network error while loading data', 'error');
            }
        }

        // Update stats
        function updateStats(stats) {
            document.getElementById('pending-count').textContent = stats.pending || 0;
            document.getElementById('approved-count').textContent = stats.approved || 0;
            document.getElementById('rejected-count').textContent = stats.rejected || 0;
            document.getElementById('total-transactions').textContent = stats.total || 0;
            document.getElementById('today-validations').textContent = stats.today || 0;
        }

        // Render pending requests
        function renderPendingRequests(requests) {
            const container = document.getElementById('pending-requests');
            
            if (requests.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Semua Permintaan Telah Divalidasi!</h4>
                        <p class="text-gray-600">Tidak ada permintaan yang menunggu validasi saat ini.</p>
                    </div>
                `;
                return;
            }

            const requestsHtml = requests.map(request => `
                <div class="border border-orange-200 bg-orange-50 rounded-lg p-6 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-orange-500 text-white rounded-lg flex items-center justify-center font-bold">
                                ${request.pending_count}
                            </div>
                            <div>
                                <div class="flex items-center space-x-3 mb-1">
                                    <h4 class="font-semibold text-gray-900">${request.nomor_transaksi}</h4>
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-xs font-medium">
                                        ${request.pending_count} items
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">${request.nama_customer} - ${request.kendaraan}</p>
                                <p class="text-xs text-orange-600 font-medium">${request.jenis_transaksi}</p>
                                <p class="text-xs text-gray-500">Kasir: ${request.kasir_name} | ${formatTime(request.tanggal_transaksi)}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button onclick="openDetailModal('${request.id_transaksi}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                Validasi Items
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            container.innerHTML = requestsHtml;
        }

        // Render all transactions
        function renderAllTransactions(transactions) {
            const tbody = document.getElementById('all-transactions-body');
            
            if (transactions.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada transaksi ditemukan</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            const transactionsHtml = transactions.map(transaction => {
                const statusBadges = {
                    'Progress': 'bg-orange-100 text-orange-800',
                    'Selesai': 'bg-green-100 text-green-800'
                };

                return `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${transaction.nomor_transaksi}</div>
                            <div class="text-xs text-gray-500">${formatTime(transaction.tanggal_transaksi)}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">${transaction.nama_customer}</div>
                            <div class="text-xs text-gray-500">${transaction.kendaraan}</div>
                            <div class="text-xs text-blue-600 font-medium">${transaction.jenis_transaksi}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                ${transaction.pending_count > 0 ? `<span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">${transaction.pending_count} Pending</span>` : ''}
                                ${transaction.approved_count > 0 ? `<span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">${transaction.approved_count} Approved</span>` : ''}
                                ${transaction.rejected_count > 0 ? `<span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">${transaction.rejected_count} Rejected</span>` : ''}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">${formatCurrency(transaction.total_harga)}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold ${statusBadges[transaction.status_transaksi]} rounded-full">
                                ${transaction.status_transaksi}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="openDetailModal('${transaction.id_transaksi}')" 
                                    class="text-blue-600 hover:text-blue-900 transition-colors">
                                👁️ Detail
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = transactionsHtml;
        }

        // Open detail modal
        async function openDetailModal(transactionId) {
            try {
                const response = await fetch(`/gudang/verifikasi-permintaan/${transactionId}/detail`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    renderDetailModal(data.data);
                    document.getElementById('modal-detail').classList.remove('hidden');
                } else {
                    showNotification('Error loading detail: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error loading transaction detail:', error);
                showNotification('Network error while loading detail', 'error');
            }
        }

        // Render detail modal - COMPACT VERSION
        function renderDetailModal(transaction) {
            // Update modal header info
            document.getElementById('modal-transaction-info').textContent = 
                `${transaction.nomor_transaksi} - ${transaction.nama_customer} - ${transaction.kendaraan}`;
            
            const pendingCount = transaction.items.filter(item => item.status_permintaan === 'Pending').length;
            const pendingBadge = document.getElementById('modal-pending-badge');
            if (pendingCount > 0) {
                pendingBadge.textContent = `${pendingCount} Items Pending`;
                pendingBadge.className = 'px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium';
            } else {
                pendingBadge.textContent = 'Semua Items Validated';
                pendingBadge.className = 'px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium';
            }

            // Create compact table for items
            const itemsTableHtml = transaction.items.map((item, index) => {
                const stockStatus = getStockStatus(item.stok_tersedia, item.reorder_point);
                const stockStatusColors = {
                    'Habis': 'text-red-600 bg-red-100',
                    'Perlu Restock': 'text-yellow-600 bg-yellow-100',
                    'Aman': 'text-green-600 bg-green-100'
                };

                const canApprove = item.status_permintaan === 'Pending' && item.stok_tersedia >= item.qty;
                const rowBgColor = item.status_permintaan === 'Pending' ? 'bg-yellow-50' : 
                                  item.status_permintaan === 'Approved' ? 'bg-green-50' : 'bg-red-50';

                return `
                    <tr class="border-b hover:bg-gray-50 ${rowBgColor} transition-colors">
                        <td class="px-3 py-3">
                            <div class="w-7 h-7 ${
                                item.status_permintaan === 'Pending' ? 'bg-yellow-500' : 
                                item.status_permintaan === 'Approved' ? 'bg-green-500' : 'bg-red-500'
                            } text-white rounded-full flex items-center justify-center text-xs font-bold">
                                ${index + 1}
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">${item.nama_barang}</p>
                                <p class="text-xs text-gray-500">${item.kode_barang} • ${item.satuan || 'PCS'}</p>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <p class="text-lg font-bold">${item.qty}</p>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <div>
                                <p class="text-lg font-bold ${item.stok_tersedia >= item.qty ? 'text-green-600' : 'text-red-600'}">${item.stok_tersedia}</p>
                                <span class="text-xs ${stockStatusColors[stockStatus]} px-2 py-1 rounded-full font-medium">
                                    ${stockStatus}
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-right">
                            <div>
                                <p class="font-medium text-sm">${formatCurrency(item.harga_satuan)}</p>
                                <p class="text-xs font-bold text-green-600">${formatCurrency(item.subtotal)}</p>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium ${
                                item.status_permintaan === 'Pending' ? 'bg-yellow-500 text-white' :
                                item.status_permintaan === 'Approved' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                            }">
                                ${item.status_permintaan}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-center">
                            ${item.status_permintaan === 'Pending' ? `
                                <div class="flex gap-2 justify-center">
                                    <button onclick="validateItem('${item.id_detail}', 'approve')" 
                                            class="btn-green text-white px-3 py-1 rounded text-xs font-medium transition-all ${!canApprove ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-md'}" 
                                            ${!canApprove ? 'disabled' : ''}
                                            title="${canApprove ? 'Approve item' : 'Stok tidak cukup'}">
                                        ✓ OK
                                    </button>
                                    <button onclick="validateItem('${item.id_detail}', 'reject')" 
                                            class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 font-medium transition-all hover:shadow-md"
                                            title="Reject item">
                                        ✗ NO
                                    </button>
                                </div>
                            ` : `
                                <div class="text-xs text-gray-500">
                                    ${item.status_permintaan === 'Approved' ? '✓ Sudah OK' : '✗ Ditolak'}
                                </div>
                            `}
                        </td>
                    </tr>
                `;
            }).join('');

            // Count approvable items
            const pendingItems = transaction.items.filter(item => item.status_permintaan === 'Pending');
            const approvableItems = pendingItems.filter(item => item.stok_tersedia >= item.qty);

            const detailHtml = `
                <!-- Quick Info Row - Compact -->
                <div class="grid grid-cols-4 gap-4 mb-4 p-4 bg-blue-50 rounded-lg text-sm">
                    <div><span class="font-medium">Service:</span> ${transaction.jenis_transaksi}</div>
                    <div><span class="font-medium">Kasir:</span> ${transaction.kasir_name}</div>
                    <div><span class="font-medium">Total:</span> <span class="font-bold text-green-600">${formatCurrency(transaction.total_harga)}</span></div>
                    <div><span class="font-medium">Waktu:</span> ${formatTime(transaction.tanggal_transaksi)}</div>
                </div>

                <!-- Items Table - Compact -->
                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-3 py-3 text-left font-medium text-gray-700">#</th>
                                <th class="px-3 py-3 text-left font-medium text-gray-700">Item</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-700">Qty</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-700">Stok</th>
                                <th class="px-3 py-3 text-right font-medium text-gray-700">Harga</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-700">Status</th>
                                <th class="px-3 py-3 text-center font-medium text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsTableHtml}
                        </tbody>
                    </table>
                </div>

                <!-- Quick Actions Footer - Only show if has pending items -->
                ${pendingItems.length > 0 ? `
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">${pendingItems.length} items pending</span> • 
                                <span class="text-blue-600 font-medium">${approvableItems.length} dapat di-approve</span>
                                ${approvableItems.length < pendingItems.length ? ` • <span class="text-red-600 font-medium">${pendingItems.length - approvableItems.length} stok kurang</span>` : ''}
                            </div>
                            <div class="action-btn-group">
                                ${approvableItems.length > 0 ? `
                                    <button onclick="bulkApproveTransaction('${transaction.id_transaksi}')" 
                                            class="btn-green text-white px-4 py-3 rounded-lg text-sm font-medium transition-all hover:shadow-md">
                                        ✓ Approve All Available (${approvableItems.length})
                                    </button>
                                ` : ''}
                                <button onclick="bulkRejectTransaction('${transaction.id_transaksi}')" 
                                        class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition-all hover:shadow-md">
                                    ✗ Reject All Pending (${pendingItems.length})
                                </button>
                            </div>
                        </div>
                    </div>
                ` : `
                    <div class="mt-6 p-4 bg-green-50 rounded-lg text-center shadow-sm">
                        <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-green-700 font-medium">Semua Item Telah Divalidasi!</p>
                    </div>
                `}
            `;

            document.getElementById('detail-content').innerHTML = detailHtml;
        }

        // Validate item - WITH DEBUG
        async function validateItem(detailId, action) {
            console.log('Validating item:', detailId, action);
            
            try {
                const url = '/gudang/verifikasi-permintaan/validate-item';
                console.log('URL:', url);
                
                const requestData = {
                    detail_id: detailId,
                    action: action
                };
                console.log('Request data:', requestData);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(requestData)
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Refresh modal content
                    const currentModal = document.getElementById('modal-detail');
                    if (!currentModal.classList.contains('hidden')) {
                        const modalInfo = document.getElementById('modal-transaction-info').textContent;
                        const transactionId = modalInfo.split(' - ')[0].replace('TRX', '');
                        const detailButtons = document.querySelectorAll('[onclick^="openDetailModal"]');
                        if (detailButtons.length > 0) {
                            const lastClickedId = detailButtons[0].getAttribute('onclick').match(/'([^']+)'/)[1];
                            await openDetailModal(lastClickedId);
                        }
                    }
                    await loadVerificationRequests();
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Full error object:', error);
                console.error('Error validating item:', error.message);
                showNotification('Network error: ' + error.message, 'error');
            }
        }

        // Bulk approve for specific transaction
        async function bulkApproveTransaction(transactionId) {
            if (!confirm('Yakin ingin approve semua item yang tersedia untuk transaksi ini?')) {
                return;
            }

            try {
                const response = await fetch('/gudang/verifikasi-permintaan/bulk-approve-transaction', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        transaction_id: transactionId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification(`Berhasil approve ${data.approved_count} items`, 'success');
                    await openDetailModal(transactionId);
                    await loadVerificationRequests();
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error bulk approving transaction:', error);
                showNotification('Network error while bulk approving', 'error');
            }
        }

        // Bulk reject for specific transaction
        async function bulkRejectTransaction(transactionId) {
            if (!confirm('Yakin ingin reject semua item pending untuk transaksi ini?')) {
                return;
            }

            try {
                const response = await fetch('/gudang/verifikasi-permintaan/bulk-reject-transaction', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        transaction_id: transactionId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification(`Berhasil reject ${data.rejected_count} items`, 'success');
                    await openDetailModal(transactionId);
                    await loadVerificationRequests();
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error bulk rejecting transaction:', error);
                showNotification('Network error while bulk rejecting', 'error');
            }
        }

        // Bulk approve visible pending items
        async function bulkApproveVisible() {
            if (!confirm('Yakin ingin approve semua item yang visible dan memiliki stok cukup?')) {
                return;
            }

            try {
                const response = await fetch('/gudang/verifikasi-permintaan/bulk-approve', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status: currentFilter,
                        search: currentSearch
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showNotification(`Berhasil approve ${data.approved_count} items`, 'success');
                    await loadVerificationRequests();
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error bulk approving:', error);
                showNotification('Network error while bulk approving', 'error');
            }
        }

        // Get stock status
        function getStockStatus(currentStock, reorderPoint) {
            if (currentStock <= 0) return 'Habis';
            if (currentStock <= reorderPoint) return 'Perlu Restock';
            return 'Aman';
        }

        // Setup auto refresh
        function setupAutoRefresh() {
            const autoRefreshCheckbox = document.getElementById('autoRefresh');
            
            function toggleAutoRefresh() {
                if (autoRefreshCheckbox.checked) {
                    autoRefreshInterval = setInterval(loadVerificationRequests, 30000); // 30 seconds
                } else {
                    if (autoRefreshInterval) {
                        clearInterval(autoRefreshInterval);
                    }
                }
            }

            autoRefreshCheckbox.addEventListener('change', toggleAutoRefresh);
            toggleAutoRefresh(); // Initialize
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Load initial data
            loadVerificationRequests();
            setupAutoRefresh();

            // Refresh button
            document.getElementById('btn-refresh').addEventListener('click', function() {
                this.disabled = true;
                this.textContent = '🔄 Refreshing...';
                loadVerificationRequests().finally(() => {
                    this.disabled = false;
                    this.textContent = '🔄 Refresh';
                });
            });

            // Filter change
            document.getElementById('status-filter').addEventListener('change', function() {
                currentFilter = this.value;
                loadVerificationRequests();
            });

            // Search input
            document.getElementById('search-input').addEventListener('input', function() {
                currentSearch = this.value;
                // Debounce search
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    loadVerificationRequests();
                }, 500);
            });

            // Close modal
            document.getElementById('btn-close-detail').addEventListener('click', function() {
                document.getElementById('modal-detail').classList.add('hidden');
            });

            // Close modal on backdrop click
            document.getElementById('modal-detail').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>