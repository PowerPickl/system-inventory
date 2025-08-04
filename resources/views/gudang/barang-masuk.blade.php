<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barang Masuk - Gudang Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .search-results { 
            max-height: 200px; 
            overflow-y: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="min-h-screen flex">
        <!-- Sidebar Component -->
        <x-gudang.sidebar active="incoming" />
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Barang Masuk</h1>
                    <p class="text-gray-500 text-sm mt-1">Process incoming stock and update inventory</p>
                </div>
                
                <!-- Tab Switcher -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button onclick="switchTab('by-request')" 
                            class="tab-btn px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 bg-blue-600 text-white"
                            data-tab="by-request">
                        📦 By Request
                    </button>
                    <button onclick="switchTab('direct-entry')" 
                            class="tab-btn px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:text-gray-900 transition-all duration-200"
                            data-tab="direct-entry">
                        ➕ Direct Entry
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8">
        <!-- BY REQUEST TAB -->
        <div id="by-request" class="tab-content active space-y-6">
            <!-- Search Request Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Find Restock Request</h2>
                            <p class="text-gray-500 text-sm">Search and process approved requests</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Request Number</label>
                            <div class="flex gap-2">
                                <input type="text" 
                                       id="requestNumber" 
                                       placeholder="Enter request number (e.g., REQ20250708001)" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <button onclick="searchRequest()" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                                    Search
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Quick Select <span class="text-xs text-gray-400">(Pending requests)</span>
                            </label>
                            <select id="quickSelectRequest" 
                                    onchange="loadQuickRequest(this.value)" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Choose from pending...</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Details (Hidden initially) -->
            <div id="requestDetails" class="hidden space-y-6">
                <!-- Request Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Request Information</h3>
                            <span id="requestStatus" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Request Number</label>
                                <p id="requestNumberDisplay" class="text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Request Date</label>
                                <p id="requestDateDisplay" class="text-sm text-gray-900 bg-gray-50 px-2 py-1 rounded"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Total Items</label>
                                <p id="requestItemsDisplay" class="text-sm text-gray-900 bg-gray-50 px-2 py-1 rounded"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier/Vendor *</label>
                                <input type="text" 
                                       id="supplier" 
                                       placeholder="Enter supplier name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                                <input type="text" 
                                       id="invoiceNumber" 
                                       placeholder="Enter invoice number" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date *</label>
                                <input type="datetime-local" 
                                       id="deliveryDate" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <input type="text" 
                                       id="deliveryNotes" 
                                       placeholder="Optional notes" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Items Validation</h3>
                            <div class="flex gap-2">
                                <button onclick="validateAllItems()" 
                                        class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                                    ✓ Accept All
                                </button>
                                <button onclick="resetQuantities()" 
                                        class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
                                    ↻ Reset
                                </button>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Item</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Current Stock</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Ordered</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Received</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Unit Price</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-500">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="requestItemsTableBody" class="divide-y divide-gray-200">
                                    <!-- Populated by JS -->
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-right font-medium text-gray-900">Total:</td>
                                        <td id="totalValue" class="px-4 py-3 font-semibold text-blue-600">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="mt-6 flex justify-center">
                            <button onclick="processIncomingStock()" 
                                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-lg font-semibold">
                                Process Incoming Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DIRECT ENTRY TAB -->
        <div id="direct-entry" class="tab-content space-y-6">
            <!-- Direct Entry Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Direct Stock Entry</h2>
                            <p class="text-gray-500 text-sm">Add stock directly without request</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supplier/Vendor *</label>
                            <input type="text" 
                                   id="directSupplier" 
                                   placeholder="Enter supplier name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                            <input type="text" 
                                   id="directInvoice" 
                                   placeholder="Enter invoice number" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Entry Date *</label>
                            <input type="datetime-local" 
                                   id="directDate" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <input type="text" 
                                   id="directNotes" 
                                   placeholder="Optional notes" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Items</h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3">
                        <div class="lg:col-span-6 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Item</label>
                            <input type="text" 
                                   id="itemSearch" 
                                   placeholder="Search by name or code..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                   oninput="searchItems(this.value)">
                            <div id="itemSearchResults" class="absolute z-20 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg hidden search-results"></div>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                            <input type="number" 
                                   id="itemQuantity" 
                                   min="1" 
                                   placeholder="Qty" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price *</label>
                            <input type="number" 
                                   id="itemPrice" 
                                   step="0.01" 
                                   placeholder="Price" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="lg:col-span-1 flex items-end">
                            <button onclick="addDirectItem()" 
                                    class="w-full px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Direct Items Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Items to Process</h3>
                        <button onclick="clearDirectItems()" 
                                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                            Clear All
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Item</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Current Stock</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Quantity</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Unit Price</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Subtotal</th>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="directItemsTableBody" class="divide-y divide-gray-200">
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No items added. Use search above to add items.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right font-medium text-gray-900">Total:</td>
                                    <td id="directTotalValue" class="px-4 py-3 font-semibold text-green-600">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-6 flex justify-center">
                        <button onclick="processDirectStock()" 
                                class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-lg font-semibold">
                            Process Direct Entry
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Entries (shared between both tabs) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-12">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Stock Entries</h3>
                    <button onclick="refreshRecentEntries()" 
                            class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
                        Refresh
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Entry Number</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Type</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Supplier</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Items</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Total Value</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentEntriesBody" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Loading recent entries...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification -->
    <div id="notification" class="fixed top-4 right-4 z-50 hidden">
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-4 min-w-80">
            <div class="flex items-center">
                <div id="notificationIcon" class="mr-3"></div>
                <div>
                    <p id="notificationMessage" class="text-sm font-medium text-gray-900"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <span id="loadingMessage" class="text-gray-700">Loading...</span>
        </div>
    </div>

    <script>
        // Global variables
        let currentRequest = null;
        let directItems = [];
        let selectedItemId = null;
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Set default dates
            const now = new Date().toISOString().slice(0, 16);
            document.getElementById('deliveryDate').value = now;
            document.getElementById('directDate').value = now;
            
            // Load initial data
            loadPendingRequests();
            loadRecentEntries();
        });

        // Tab switching
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tab).classList.add('active');
            
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'bg-green-600');
                btn.classList.add('text-gray-600');
            });
            
            const activeBtn = document.querySelector(`[data-tab="${tab}"]`);
            activeBtn.classList.remove('text-gray-600');
            
            if (tab === 'by-request') {
                activeBtn.classList.add('bg-blue-600', 'text-white');
            } else {
                activeBtn.classList.add('bg-green-600', 'text-white');
            }
        }

        // Load pending requests
        async function loadPendingRequests() {
            try {
                const response = await fetch('/gudang/barang-masuk/pending-requests', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                
                const select = document.getElementById('quickSelectRequest');
                select.innerHTML = '<option value="">Choose from pending...</option>';
                
                if (data.success && data.requests.length > 0) {
                    data.requests.forEach(request => {
                        const option = document.createElement('option');
                        option.value = request.nomor_request;
                        option.textContent = `${request.nomor_request} - ${request.total_items} items`;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading pending requests:', error);
            }
        }

        // Search request
        async function searchRequest() {
            const requestNumber = document.getElementById('requestNumber').value.trim();
            
            if (!requestNumber) {
                showNotification('Please enter a request number', 'warning');
                return;
            }

            showLoading('Searching request...');

            try {
                const response = await fetch(`/gudang/barang-masuk/search-request/${requestNumber}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                
                hideLoading();

                if (data.success) {
                    currentRequest = data.request;
                    displayRequestDetails(data.request);
                    document.getElementById('requestDetails').classList.remove('hidden');
                    showNotification('Request found and loaded', 'success');
                } else {
                    showNotification('Request not found or not ready for processing', 'error');
                    document.getElementById('requestDetails').classList.add('hidden');
                }
            } catch (error) {
                hideLoading();
                console.error('Error searching request:', error);
                showNotification('Error searching request', 'error');
            }
        }

        // Quick load request
        function loadQuickRequest(requestNumber) {
            if (requestNumber) {
                document.getElementById('requestNumber').value = requestNumber;
                searchRequest();
            }
        }

        // Display request details
        function displayRequestDetails(request) {
            document.getElementById('requestNumberDisplay').textContent = request.nomor_request;
            document.getElementById('requestDateDisplay').textContent = new Date(request.tanggal_request).toLocaleDateString('id-ID');
            document.getElementById('requestItemsDisplay').textContent = `${request.details.length} items`;

            // Status badge
            const statusElement = document.getElementById('requestStatus');
            const statusColors = {
                'Ordered': 'bg-purple-100 text-purple-800',
                'Approved': 'bg-blue-100 text-blue-800'
            };
            statusElement.className = `px-3 py-1 rounded-full text-sm font-medium ${statusColors[request.status_request] || 'bg-gray-100 text-gray-800'}`;
            statusElement.textContent = request.status_request;

            // Populate items table
            populateRequestItemsTable(request.details);
        }

        // Populate request items table
        function populateRequestItemsTable(details) {
            const tbody = document.getElementById('requestItemsTableBody');
            tbody.innerHTML = '';

            details.forEach((detail, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                const currentStock = detail.barang.stok?.jumlah_stok || 0;
                const orderedQty = detail.qty_approved || detail.qty_request;
                const unitPrice = detail.barang.harga_beli;

                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${detail.barang.nama_barang}</div>
                        <div class="text-xs text-gray-500">${detail.barang.kode_barang}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-900">${currentStock} ${detail.barang.satuan}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${orderedQty} ${detail.barang.satuan}</div>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" 
                               id="receivedQty_${index}" 
                               value="${orderedQty}"
                               min="0" 
                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500"
                               onchange="updateItemSubtotal(${index}, ${unitPrice})">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" 
                               id="unitPrice_${index}" 
                               value="${unitPrice}"
                               min="0" 
                               step="0.01"
                               class="w-24 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500"
                               onchange="updateItemSubtotal(${index}, this.value)">
                    </td>
                    <td class="px-4 py-3">
                        <div id="subtotal_${index}" class="font-medium text-gray-900">
                            Rp ${formatNumber(orderedQty * unitPrice)}
                        </div>
                    </td>
                `;
                
                tbody.appendChild(row);
            });

            updateTotalValue();
        }

        // Update item subtotal
        function updateItemSubtotal(index, unitPrice) {
            const receivedQty = parseFloat(document.getElementById(`receivedQty_${index}`).value) || 0;
            const price = parseFloat(unitPrice) || 0;
            const subtotal = receivedQty * price;
            
            document.getElementById(`subtotal_${index}`).textContent = `Rp ${formatNumber(subtotal)}`;
            updateTotalValue();
        }

        // Update total value
        function updateTotalValue() {
            const subtotalElements = document.querySelectorAll('[id^="subtotal_"]');
            let total = 0;
            
            subtotalElements.forEach(element => {
                const value = element.textContent.replace(/[^\d]/g, '');
                total += parseFloat(value) || 0;
            });
            
            document.getElementById('totalValue').textContent = `Rp ${formatNumber(total)}`;
        }

        // Validate all items
        function validateAllItems() {
            if (!currentRequest) return;
            
            currentRequest.details.forEach((detail, index) => {
                const orderedQty = detail.qty_approved || detail.qty_request;
                document.getElementById(`receivedQty_${index}`).value = orderedQty;
                updateItemSubtotal(index, detail.barang.harga_beli);
            });
            
            showNotification('All quantities validated', 'success');
        }

        // Reset quantities
        function resetQuantities() {
            if (!currentRequest) return;
            
            currentRequest.details.forEach((detail, index) => {
                const orderedQty = detail.qty_approved || detail.qty_request;
                document.getElementById(`receivedQty_${index}`).value = orderedQty;
                document.getElementById(`unitPrice_${index}`).value = detail.barang.harga_beli;
                updateItemSubtotal(index, detail.barang.harga_beli);
            });
            
            showNotification('Quantities reset', 'info');
        }

        // Process incoming stock
        async function processIncomingStock() {
            if (!currentRequest) {
                showNotification('No request selected', 'error');
                return;
            }

            const supplier = document.getElementById('supplier').value.trim();
            const deliveryDate = document.getElementById('deliveryDate').value;

            if (!supplier || !deliveryDate) {
                showNotification('Please fill required fields', 'warning');
                return;
            }

            if (!confirm('Process this incoming stock? This will update inventory.')) {
                return;
            }

            const processData = {
                request_id: currentRequest.id_request,
                supplier: supplier,
                invoice_number: document.getElementById('invoiceNumber').value,
                delivery_date: deliveryDate,
                notes: document.getElementById('deliveryNotes').value,
                items: []
            };

            currentRequest.details.forEach((detail, index) => {
                const receivedQty = parseFloat(document.getElementById(`receivedQty_${index}`).value) || 0;
                const unitPrice = parseFloat(document.getElementById(`unitPrice_${index}`).value) || 0;
                
                if (receivedQty > 0) {
                    processData.items.push({
                        id_barang: detail.id_barang,
                        qty_received: receivedQty,
                        unit_price: unitPrice,
                        subtotal: receivedQty * unitPrice
                    });
                }
            });

            showLoading('Processing incoming stock...');

            try {
                const response = await fetch('/gudang/barang-masuk/process-request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(processData)
                });

                const data = await response.json();
                hideLoading();

                if (data.success) {
                    showNotification(`Stock processed! Entry: ${data.entry_number}`, 'success');
                    resetRequestForm();
                    loadPendingRequests();
                    loadRecentEntries();
                } else {
                    showNotification(`Error: ${data.message}`, 'error');
                }
            } catch (error) {
                hideLoading();
                showNotification('Network error', 'error');
            }
        }

        // Search items for direct entry
        async function searchItems(query) {
            if (query.length < 2) {
                document.getElementById('itemSearchResults').classList.add('hidden');
                return;
            }

            try {
                const response = await fetch(`/gudang/barang-masuk/search-items?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    displayItemSearchResults(data.items);
                }
            } catch (error) {
                console.error('Error searching items:', error);
            }
        }

        // Display search results
        function displayItemSearchResults(items) {
            const resultsDiv = document.getElementById('itemSearchResults');
            
            if (items.length === 0) {
                resultsDiv.classList.add('hidden');
                return;
            }

            resultsDiv.innerHTML = '';
            
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                div.onclick = () => selectItem(item);
                
                div.innerHTML = `
                    <div class="font-medium text-gray-900 text-sm">${item.nama_barang}</div>
                    <div class="text-xs text-gray-500">${item.kode_barang} | Stock: ${item.stok?.jumlah_stok || 0} ${item.satuan}</div>
                `;
                
                resultsDiv.appendChild(div);
            });
            
            resultsDiv.classList.remove('hidden');
        }

        // Select item
        function selectItem(item) {
            selectedItemId = item.id_barang;
            document.getElementById('itemSearch').value = `${item.nama_barang} (${item.kode_barang})`;
            document.getElementById('itemPrice').value = item.harga_beli || '';
            document.getElementById('itemSearchResults').classList.add('hidden');
        }

        // Add direct item
        function addDirectItem() {
            const itemSearch = document.getElementById('itemSearch').value;
            const quantity = parseFloat(document.getElementById('itemQuantity').value);
            const price = parseFloat(document.getElementById('itemPrice').value);

            if (!selectedItemId || !quantity || !price || quantity <= 0 || price <= 0) {
                showNotification('Please fill all fields correctly', 'warning');
                return;
            }

            const existingIndex = directItems.findIndex(item => item.id_barang === selectedItemId);
            
            if (existingIndex >= 0) {
                directItems[existingIndex].quantity += quantity;
                directItems[existingIndex].subtotal = directItems[existingIndex].quantity * directItems[existingIndex].unit_price;
            } else {
                directItems.push({
                    id_barang: selectedItemId,
                    name: itemSearch,
                    quantity: quantity,
                    unit_price: price,
                    subtotal: quantity * price
                });
            }

            // Reset form
            document.getElementById('itemSearch').value = '';
            document.getElementById('itemQuantity').value = '';
            document.getElementById('itemPrice').value = '';
            selectedItemId = null;

            updateDirectItemsTable();
            showNotification('Item added', 'success');
        }

        // Update direct items table
        function updateDirectItemsTable() {
            const tbody = document.getElementById('directItemsTableBody');
            
            if (directItems.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No items added. Use search above to add items.
                        </td>
                    </tr>
                `;
                document.getElementById('directTotalValue').textContent = 'Rp 0';
                return;
            }

            tbody.innerHTML = '';
            let total = 0;

            directItems.forEach((item, index) => {
                total += item.subtotal;
                
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${item.name}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-900">-</div>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" 
                               value="${item.quantity}"
                               min="1" 
                               class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500"
                               onchange="updateDirectItemQuantity(${index}, this.value)">
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" 
                               value="${item.unit_price}"
                               min="0" 
                               step="0.01"
                               class="w-24 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500"
                               onchange="updateDirectItemPrice(${index}, this.value)">
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">Rp ${formatNumber(item.subtotal)}</div>
                    </td>
                    <td class="px-4 py-3">
                        <button onclick="removeDirectItem(${index})" 
                                class="text-red-600 hover:text-red-800 text-sm">
                            Remove
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            document.getElementById('directTotalValue').textContent = `Rp ${formatNumber(total)}`;
        }

        // Update direct item quantity
        function updateDirectItemQuantity(index, newQuantity) {
            directItems[index].quantity = parseFloat(newQuantity) || 1;
            directItems[index].subtotal = directItems[index].quantity * directItems[index].unit_price;
            updateDirectItemsTable();
        }

        // Update direct item price
        function updateDirectItemPrice(index, newPrice) {
            directItems[index].unit_price = parseFloat(newPrice) || 0;
            directItems[index].subtotal = directItems[index].quantity * directItems[index].unit_price;
            updateDirectItemsTable();
        }

        // Remove direct item
        function removeDirectItem(index) {
            directItems.splice(index, 1);
            updateDirectItemsTable();
            showNotification('Item removed', 'info');
        }

        // Clear direct items
        function clearDirectItems() {
            if (directItems.length === 0) return;
            
            if (confirm('Clear all items?')) {
                directItems = [];
                updateDirectItemsTable();
                showNotification('Items cleared', 'info');
            }
        }

        // Process direct stock
        async function processDirectStock() {
            if (directItems.length === 0) {
                showNotification('No items to process', 'warning');
                return;
            }

            const supplier = document.getElementById('directSupplier').value.trim();
            const date = document.getElementById('directDate').value;

            if (!supplier || !date) {
                showNotification('Please fill required fields', 'warning');
                return;
            }

            if (!confirm('Process direct stock entry? This will update inventory.')) {
                return;
            }

            const processData = {
                supplier: supplier,
                invoice_number: document.getElementById('directInvoice').value,
                entry_date: date,
                notes: document.getElementById('directNotes').value,
                items: directItems.map(item => ({
                    id_barang: item.id_barang,
                    qty_received: item.quantity,
                    unit_price: item.unit_price,
                    subtotal: item.subtotal
                }))
            };

            showLoading('Processing direct entry...');

            try {
                const response = await fetch('/gudang/barang-masuk/process-direct', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(processData)
                });

                const data = await response.json();
                hideLoading();

                if (data.success) {
                    showNotification(`Direct entry processed! Entry: ${data.entry_number}`, 'success');
                    resetDirectForm();
                    loadRecentEntries();
                } else {
                    showNotification(`Error: ${data.message}`, 'error');
                }
            } catch (error) {
                hideLoading();
                showNotification('Network error', 'error');
            }
        }

        // Load recent entries
        async function loadRecentEntries() {
            try {
                const response = await fetch('/gudang/barang-masuk/recent-entries', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    displayRecentEntries(data.entries);
                }
            } catch (error) {
                console.error('Error loading recent entries:', error);
            }
        }

        // Display recent entries
        function displayRecentEntries(entries) {
            const tbody = document.getElementById('recentEntriesBody');
            
            if (entries.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No recent entries found.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = '';
            
            entries.forEach(entry => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                
                const typeColors = {
                    'Restock Request': 'bg-blue-100 text-blue-800',
                    'Direct Entry': 'bg-green-100 text-green-800',
                    'Manual': 'bg-purple-100 text-purple-800'
                };

                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">${entry.nomor_masuk}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-900">${new Date(entry.tanggal_masuk).toLocaleDateString('id-ID')}</div>
                        <div class="text-xs text-gray-500">${new Date(entry.tanggal_masuk).toLocaleTimeString('id-ID')}</div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium ${typeColors[entry.jenis_masuk]} rounded-full">
                            ${entry.jenis_masuk}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-900">${entry.supplier || '-'}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-gray-900">${entry.details_count} items</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">Rp ${formatNumber(entry.total_nilai || 0)}</div>
                    </td>
                    <td class="px-4 py-3">
                        <button onclick="viewEntryDetail('${entry.nomor_masuk}')" 
                                class="text-blue-600 hover:text-blue-800 text-sm">
                            View
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }

        // Refresh recent entries
        function refreshRecentEntries() {
            showNotification('Refreshing...', 'info');
            loadRecentEntries();
        }

        // View entry detail
        function viewEntryDetail(entryNumber) {
            showNotification(`Viewing ${entryNumber}`, 'info');
            // Implement detail view
        }

        // Reset forms
        function resetRequestForm() {
            currentRequest = null;
            document.getElementById('requestDetails').classList.add('hidden');
            document.getElementById('requestNumber').value = '';
            document.getElementById('quickSelectRequest').value = '';
            document.getElementById('supplier').value = '';
            document.getElementById('invoiceNumber').value = '';
            document.getElementById('deliveryNotes').value = '';
        }

        function resetDirectForm() {
            directItems = [];
            document.getElementById('directSupplier').value = '';
            document.getElementById('directInvoice').value = '';
            document.getElementById('directNotes').value = '';
            updateDirectItemsTable();
        }

        // Utility functions
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function showLoading(message) {
            document.getElementById('loadingMessage').textContent = message;
            document.getElementById('loadingSpinner').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }

        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notificationMessage');
            const iconEl = document.getElementById('notificationIcon');
            
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            
            const colors = {
                success: 'text-green-600',
                error: 'text-red-600',
                warning: 'text-yellow-600',
                info: 'text-blue-600'
            };
            
            iconEl.textContent = icons[type];
            iconEl.className = `text-lg ${colors[type]}`;
            messageEl.textContent = message;
            
            notification.classList.remove('hidden');
            
            setTimeout(() => {
                notification.classList.add('hidden');
            }, 4000);
        }

        // Close search results when clicking outside
        document.addEventListener('click', function(event) {
            const searchInput = document.getElementById('itemSearch');
            const searchResults = document.getElementById('itemSearchResults');
            
            if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                searchResults.classList.add('hidden');
            }
        });
    </script>
</body>
</html>