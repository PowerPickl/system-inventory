<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Monitoring Stock - Gudang Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-emerald-800 shadow-lg relative">
            <!-- Logo/Brand -->
            <div class="p-6 border-b border-emerald-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white font-semibold">Bengkel Inventory</h3>
                        <p class="text-emerald-400 text-sm">Gudang Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <!-- Navigation -->
            <nav class="mt-6 pb-20">
                <div class="px-3">
                    <!-- Dashboard -->
                    <a href="/dashboard-gudang" class="flex items-center px-3 py-2 text-emerald-300 hover:text-white hover:bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Monitoring Stock (Active) -->
                    <a href="{{ route('gudang.monitoring-stock') }}" class="flex items-center px-3 py-2 text-white bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Monitoring Stock
                        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">EOQ</span>
                    </a>

                    <!-- Request Restock -->
                    <a href="{{ route('gudang.restock-requests') }}" class="flex items-center px-3 py-2 text-emerald-300 hover:text-white hover:bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Request Restock
                        {{-- You can add pending count here later --}}
                        {{-- <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">2</span> --}}
                    </a>

                    <!-- Other menu items -->
                    <a href="#" class="flex items-center px-3 py-2 text-emerald-300 hover:text-white hover:bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Verifikasi Permintaan
                    </a>

                    <a href="#" class="flex items-center px-3 py-2 text-emerald-300 hover:text-white hover:bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Kelola Data Barang
                    </a>

                    <a href="#" class="flex items-center px-3 py-2 text-emerald-300 hover:text-white hover:bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Barang Masuk
                    </a>
                </div>
            </nav>

            <!-- User Info & Logout -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-emerald-700 bg-emerald-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-emerald-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-white text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-emerald-400 text-xs">Gudang</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-emerald-400 hover:text-white transition-colors duration-200" title="Logout">
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
                            <h1 class="text-2xl font-semibold text-gray-900">Monitoring Stock - EOQ Analysis</h1>
                            <p class="text-gray-600 text-sm">Real-time inventory monitoring dengan scientific calculations</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Auto Refresh Toggle -->
                            <label class="flex items-center">
                                <input type="checkbox" id="autoRefresh" class="sr-only">
                                <div class="relative">
                                    <div class="block bg-gray-600 w-14 h-8 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                                </div>
                                <span class="ml-2 text-sm text-gray-600">Auto Refresh</span>
                            </label>

                            <!-- Update All Button -->
                            <button onclick="updateAllEOQ()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 text-sm font-medium">
                                🔄 Update All EOQ
                            </button>
                            
                            <!-- Last Update Time -->
                            <div class="text-sm text-gray-600">
                                <div id="lastUpdateTime">
                                    @if($eoqStats['last_batch_update'])
                                        @try
                                            {{ \Carbon\Carbon::parse($eoqStats['last_batch_update'])->format('H:i:s') }}
                                        @catch(\Exception $e)
                                            {{ $eoqStats['last_batch_update'] }}
                                        @endtry
                                    @else
                                        Never
                                    @endif
                                </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- EOQ Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-emerald-100 rounded-lg">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $eoqStats['total_items'] }}</h3>
                                <p class="text-gray-600 text-sm">Total Items</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $eoqStats['with_eoq'] }}</h3>
                                <p class="text-gray-600 text-sm">With EOQ Data</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $eoqStats['optimal_level'] }}</h3>
                                <p class="text-gray-600 text-sm">Optimal Level</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $eoqStats['need_restock'] }}</h3>
                                <p class="text-gray-600 text-sm">Need Restock</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $eoqStats['critical_stock'] }}</h3>
                                <p class="text-gray-600 text-sm">Critical Stock</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Monitoring Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Real-time Stock Monitoring</h3>
                            <div class="flex space-x-3">
                                <button onclick="selectAllRestock()" class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                                    Select All Restock
                                </button>
                                <button onclick="createBulkRestockRequest()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 text-sm font-medium">
                                    📋 Create Restock Request
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="monitoringTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAll" class="rounded">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EOQ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Safety Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recommendation</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="monitoringTableBody">
                                @foreach($items as $data)
                                <tr class="hover:bg-gray-50" data-item-id="{{ $data['item']->id_barang }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($data['recommendation']['need_restock'])
                                        <input type="checkbox" class="restock-checkbox rounded" value="{{ $data['item']->id_barang }}">
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $data['item']->nama_barang }}</div>
                                            <div class="text-sm text-gray-500">{{ $data['item']->kode_barang }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $data['current_stock'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $data['item']->satuan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $data['eoq'] ?? '-' }}</div>
                                        @if($data['eoq'])
                                        <div class="text-xs text-blue-600 cursor-pointer" onclick="showEOQDetails({{ $data['item']->id_barang }})">
                                            📊 View Calculation
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $data['rop'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $data['safety_stock'] ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColor = match($data['eoq_status']) {
                                                'Optimal Level' => 'bg-green-100 text-green-800',
                                                'Monitor Closely' => 'bg-yellow-100 text-yellow-800',
                                                'Reorder Required' => 'bg-orange-100 text-orange-800',
                                                'Critical - Out of Stock' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold {{ $statusColor }} rounded-full">
                                            {{ $data['eoq_status'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($data['recommendation']['need_restock'])
                                            <div class="text-sm font-medium text-orange-600">
                                                Order {{ $data['recommendation']['recommended_qty'] }} units
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Urgency: {{ $data['recommendation']['urgency'] }}
                                            </div>
                                        @else
                                            <span class="text-sm text-green-600">No action needed</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button onclick="updateSingleEOQ({{ $data['item']->id_barang }})" 
                                                class="text-blue-600 hover:text-blue-900" title="Update EOQ">
                                            🔄
                                        </button>
                                        <button onclick="showStockTrends({{ $data['item']->id_barang }})" 
                                                class="text-green-600 hover:text-green-900" title="View Trends">
                                            📈
                                        </button>
                                        @if($data['recommendation']['need_restock'])
                                        <button onclick="quickRestock({{ $data['item']->id_barang }}, {{ $data['recommendation']['recommended_qty'] }})" 
                                                class="text-orange-600 hover:text-orange-900" title="Quick Restock">
                                            ⚡
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($recentMovements->count() > 0)
                <!-- Recent Stock Movements -->
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Stock Movements (Today)</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($recentMovements as $movement)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $movement->barang->nama_barang }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $movement->jenis_perubahan }} - 
                                        Qty: {{ $movement->qty_perubahan > 0 ? '+' : '' }}{{ $movement->qty_perubahan }} 
                                        ({{ $movement->qty_sebelum }} → {{ $movement->qty_sesudah }})
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">{{ $movement->tanggal_log->format('H:i') }}</p>
                                    <p class="text-xs text-gray-400">{{ $movement->user->name }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Replace the existing <script> section with this enhanced version -->
<script>
// Enhanced JavaScript for Monitoring Stock with Restock Request Functionality

let autoRefreshInterval = null;
let isAutoRefreshEnabled = false;

// CSRF Token setup
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Auto refresh toggle functionality
document.getElementById('autoRefresh').addEventListener('change', function() {
    isAutoRefreshEnabled = this.checked;
    
    if (isAutoRefreshEnabled) {
        startAutoRefresh();
        showNotification('Auto-refresh enabled (every 30 seconds)', 'success');
    } else {
        stopAutoRefresh();
        showNotification('Auto-refresh disabled', 'info');
    }
    
    // Update toggle UI
    const toggle = this.nextElementSibling;
    const dot = toggle.querySelector('.dot');
    
    if (this.checked) {
        toggle.classList.remove('bg-gray-600');
        toggle.classList.add('bg-emerald-600');
        dot.style.transform = 'translateX(24px)';
    } else {
        toggle.classList.remove('bg-emerald-600');
        toggle.classList.add('bg-gray-600');
        dot.style.transform = 'translateX(0px)';
    }
});

// Start auto refresh
function startAutoRefresh() {
    if (autoRefreshInterval) clearInterval(autoRefreshInterval);
    
    autoRefreshInterval = setInterval(() => {
        updateRealTimeData();
    }, 30000); // 30 seconds
}

// Stop auto refresh
function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

// Update all EOQ calculations
function updateAllEOQ() {
    showLoadingSpinner('Updating all EOQ calculations...');
    
    fetch('/gudang/monitoring-stock/update-all-eoq', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                updateRealTimeData();
            }, 3000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
        showNotification('Network error while updating EOQ', 'error');
    });
}

// Update single item EOQ
function updateSingleEOQ(itemId) {
    showLoadingSpinner(`Updating EOQ for item ${itemId}...`);
    
    fetch(`/gudang/monitoring-stock/update-eoq/${itemId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                updateRealTimeData();
            }, 2000);
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
        showNotification('Network error while updating EOQ', 'error');
    });
}

// Get real-time data updates
function updateRealTimeData() {
    fetch('/gudang/monitoring-stock/realtime-data')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateTableData(data.data);
            updateLastRefreshTime(data.timestamp);
        } else {
            console.error('Failed to fetch real-time data');
        }
    })
    .catch(error => {
        console.error('Error fetching real-time data:', error);
    });
}

// Update table with new data
function updateTableData(items) {
    const tbody = document.getElementById('monitoringTableBody');
    
    items.forEach(item => {
        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
        if (row) {
            // Update current stock
            const stockCell = row.children[2];
            stockCell.querySelector('.text-sm').textContent = item.current_stock;
            
            // Update EOQ
            const eoqCell = row.children[3];
            eoqCell.querySelector('.text-sm').textContent = item.eoq || '-';
            
            // Update ROP
            const ropCell = row.children[4];
            ropCell.querySelector('.text-sm').textContent = item.rop;
            
            // Update status badge
            const statusCell = row.children[6];
            const badge = statusCell.querySelector('span');
            badge.textContent = item.status;
            
            // Update badge color based on status
            badge.className = 'px-2 py-1 text-xs font-semibold rounded-full ' + getStatusBadgeColor(item.status);
            
            // Update recommendation
            const recCell = row.children[7];
            if (item.need_restock) {
                recCell.innerHTML = `
                    <div class="text-sm font-medium text-orange-600">
                        Order ${item.recommended_qty} units
                    </div>
                    <div class="text-xs text-gray-500">
                        Urgency: ${item.urgency}
                    </div>
                `;
            } else {
                recCell.innerHTML = '<span class="text-sm text-green-600">No action needed</span>';
            }
        }
    });
}

// Get status badge color
function getStatusBadgeColor(status) {
    switch(status) {
        case 'Optimal Level': return 'bg-green-100 text-green-800';
        case 'Monitor Closely': return 'bg-yellow-100 text-yellow-800';
        case 'Reorder Required': return 'bg-orange-100 text-orange-800';
        case 'Critical - Out of Stock': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Update last refresh time
function updateLastRefreshTime(timestamp) {
    document.getElementById('lastUpdateTime').textContent = timestamp;
}

// Show EOQ calculation details modal
function showEOQDetails(itemId) {
    showLoadingSpinner('Loading EOQ calculation details...');
    
    fetch(`/gudang/monitoring-stock/eoq-details/${itemId}`)
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            data.item_id = itemId;
            displayEOQModal(data);
        } else {
            showNotification('Error loading EOQ details: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
        showNotification('Network error while loading EOQ details', 'error');
    });
}

// Display EOQ modal
function displayEOQModal(data) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl border p-4 w-96 max-w-md mx-4">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">EOQ Calculation</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Item Name -->
                <div class="mb-4 p-2 bg-gray-50 rounded">
                    <p class="text-sm font-medium text-gray-700">${data.item.name}</p>
                    <p class="text-xs text-gray-500">${data.item.code || ''}</p>
                </div>
                
                <!-- Main Results -->
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between items-center bg-blue-50 px-4 py-3 rounded-lg">
                        <span class="font-medium text-gray-700">EOQ</span>
                        <span class="font-bold text-lg text-blue-600">${data.calculations.eoq.eoq}</span>
                    </div>
                    <div class="flex justify-between items-center bg-green-50 px-4 py-3 rounded-lg">
                        <span class="font-medium text-gray-700">Safety Stock</span>
                        <span class="font-bold text-lg text-green-600">${data.calculations.safety_stock.safety_stock}</span>
                    </div>
                    <div class="flex justify-between items-center bg-orange-50 px-4 py-3 rounded-lg">
                        <span class="font-medium text-gray-700">ROP</span>
                        <span class="font-bold text-lg text-orange-600">${data.calculations.rop.rop}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-4 flex justify-end space-x-3">
                    <button onclick="updateSingleEOQ(${data.item_id || 'null'})" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        🔄 Update EOQ
                    </button>
                    <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 text-sm font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// ===========================================
// RESTOCK REQUEST FUNCTIONALITY - NEW!
// ===========================================

// Create bulk restock request
function createBulkRestockRequest() {
    const selectedItems = Array.from(document.querySelectorAll('.restock-checkbox:checked')).map(cb => cb.value);
    
    if (selectedItems.length === 0) {
        showNotification('Please select items for restock request', 'warning');
        return;
    }
    
    // Show loading while getting recommendations
    showLoadingSpinner('Loading restock recommendations...');
    
    // Get recommendations for selected items
    fetch('/gudang/monitoring-stock/restock-recommendations', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            item_ids: selectedItems
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            showRestockRequestModal(data.items, data.total_estimasi);
        } else {
            showNotification('Error loading recommendations: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
        showNotification('Network error while loading recommendations', 'error');
    });
}

// Show restock request modal
function showRestockRequestModal(items, totalEstimasi) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl border max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">🚀 Create Restock Request</h3>
                        <p class="text-sm text-gray-600 mt-1">Review and adjust quantities based on EOQ calculations</p>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <form id="restockRequestForm">
                        <!-- Items List -->
                        <div class="space-y-4 mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">📦 Items to Restock (${items.length} items)</h4>
                            
                            <div class="space-y-3" id="restockItemsList">
                                ${items.map((item, index) => `
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition-colors">
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                                            <!-- Item Info -->
                                            <div class="md:col-span-1">
                                                <h5 class="font-medium text-gray-900">${item.nama_barang}</h5>
                                                <p class="text-sm text-gray-600">${item.kode_barang}</p>
                                                <p class="text-xs text-gray-500">Current: ${item.current_stock} ${item.satuan}</p>
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full mt-1 ${getUrgencyBadgeColor(item.urgency)}">
                                                    ${item.urgency}
                                                </span>
                                            </div>
                                            
                                            <!-- Quantity Input -->
                                            <div class="md:col-span-1">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                                <input 
                                                    type="number" 
                                                    name="items[${index}][qty_request]" 
                                                    value="${item.recommended_qty}"
                                                    min="1"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                    onchange="updateItemTotal(${index}, ${item.harga_beli})"
                                                >
                                                <p class="text-xs text-blue-600 mt-1">💡 EOQ: ${item.eoq}</p>
                                                <input type="hidden" name="items[${index}][id_barang]" value="${item.id_barang}">
                                            </div>
                                            
                                            <!-- Price Info -->
                                            <div class="md:col-span-1">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price</label>
                                                <p class="text-sm text-gray-900">Rp ${formatCurrency(item.harga_beli)}</p>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Total: <span class="font-medium item-total-${index}">Rp ${formatCurrency(item.estimasi_total)}</span>
                                                </p>
                                            </div>
                                            
                                            <!-- Reason -->
                                            <div class="md:col-span-1">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                                <textarea 
                                                    name="items[${index}][alasan_request]" 
                                                    rows="2"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                                                    placeholder="Enter reason for restock..."
                                                >${item.alasan_default}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">📝 Additional Notes</label>
                            <textarea 
                                name="catatan_request" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Enter any additional notes for this restock request..."
                            ></textarea>
                        </div>
                        
                        <!-- Summary -->
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-emerald-900">📊 Request Summary</h4>
                                    <p class="text-sm text-emerald-700">Total Items: ${items.length}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-emerald-700">Estimated Total Cost</p>
                                    <p class="text-xl font-bold text-emerald-900" id="totalEstimatedCost">Rp ${formatCurrency(totalEstimasi)}</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="this.closest('.fixed').remove()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        onclick="submitRestockRequest()" 
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 font-medium"
                    >
                        🚀 Create Request
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Submit restock request
function submitRestockRequest() {
    const form = document.getElementById('restockRequestForm');
    const formData = new FormData(form);
    
    // Convert FormData to JSON structure
    const requestData = {
        items: [],
        catatan_request: formData.get('catatan_request')
    };
    
    // Parse items data
    const itemsData = {};
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('items[')) {
            const match = key.match(/items\[(\d+)\]\[(.+)\]/);
            if (match) {
                const index = match[1];
                const field = match[2];
                
                if (!itemsData[index]) itemsData[index] = {};
                itemsData[index][field] = value;
            }
        }
    }
    
    // Convert to array
    requestData.items = Object.values(itemsData);
    
    // Show loading
    showLoadingSpinner('Creating restock request...');
    
    // Submit request
    fetch('/gudang/monitoring-stock/create-restock-request', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            // Close modal
            document.querySelector('.fixed.inset-0').remove();
            
            // Show success notification
            showSuccessModal(
                '🎉 Request Created Successfully!',
                `Restock request ${data.request_number} has been created with ${data.total_items} items and sent to Owner for approval.`,
                [
                    {
                        text: '📋 View Request',
                        class: 'bg-emerald-600 text-white hover:bg-emerald-700',
                        onclick: `window.location.href='/gudang/restock-requests'`
                    }
                ]
            );
            
            // Clear selections
            document.querySelectorAll('.restock-checkbox:checked').forEach(cb => cb.checked = false);
            
        } else {
            showNotification('Error creating request: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
        showNotification('Network error while creating request', 'error');
    });
}

// Quick restock for single item
function quickRestock(itemId, recommendedQty) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl border max-w-md w-full">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">⚡ Quick Restock</h3>
                    <p class="text-sm text-gray-600 mt-1">Create instant restock request</p>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <form id="quickRestockForm">
                        <input type="hidden" name="id_barang" value="${itemId}">
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input 
                                type="number" 
                                name="qty_request" 
                                value="${recommendedQty}"
                                min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                required
                            >
                            <p class="text-xs text-blue-600 mt-1">💡 Recommended: ${recommendedQty} units</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                            <textarea 
                                name="alasan_request" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="Enter reason for quick restock..."
                            >Urgent restock based on EOQ analysis</textarea>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="this.closest('.fixed').remove()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        onclick="submitQuickRestock()" 
                        class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 font-medium"
                    >
                        ⚡ Quick Restock
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Submit quick restock
function submitQuickRestock() {
    const form = document.getElementById('quickRestockForm');
    const formData = new FormData(form);
    
    const requestData = {
        id_barang: formData.get('id_barang'),
        qty_request: parseInt(formData.get('qty_request')),
        alasan_request: formData.get('alasan_request')
    };
    
    // Show loading
    showLoadingSpinner('Creating quick restock request...');
    
    // Submit request
    fetch('/gudang/monitoring-stock/quick-restock', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            // Close modal
            document.querySelector('.fixed.inset-0').remove();
            
            // Show success notification
            showNotification(data.message, 'success');
            
        } else {
            showNotification('Error creating quick restock: ' + data.message, 'error');
        }
    })
    .catch(error => {
        hideLoadingSpinner();
        console.error('Error:', error);
        showNotification('Network error while creating quick restock', 'error');
    });
}

// Update item total calculation
function updateItemTotal(index, unitPrice) {
    const qtyInput = document.querySelector(`input[name="items[${index}][qty_request]"]`);
    const totalElement = document.querySelector(`.item-total-${index}`);
    
    if (qtyInput && totalElement) {
        const qty = parseInt(qtyInput.value) || 0;
        const total = qty * unitPrice;
        totalElement.textContent = `Rp ${formatCurrency(total)}`;
        
        // Update grand total
        updateGrandTotal();
    }
}

// Update grand total
function updateGrandTotal() {
    let grandTotal = 0;
    
    document.querySelectorAll('[class*="item-total-"]').forEach(element => {
        const amount = element.textContent.replace(/[Rp\s,.]/g, '');
        grandTotal += parseInt(amount) || 0;
    });
    
    const grandTotalElement = document.getElementById('totalEstimatedCost');
    if (grandTotalElement) {
        grandTotalElement.textContent = `Rp ${formatCurrency(grandTotal)}`;
    }
}

// Get urgency badge color
function getUrgencyBadgeColor(urgency) {
    switch(urgency) {
        case 'Critical': return 'bg-red-100 text-red-800';
        case 'High': return 'bg-orange-100 text-orange-800';
        case 'Normal': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Select all restock items
function selectAllRestock() {
    const checkboxes = document.querySelectorAll('.restock-checkbox');
    const selectAllChecked = document.querySelectorAll('.restock-checkbox:checked').length === checkboxes.length;
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !selectAllChecked;
    });
}

// Show stock trends (placeholder)
function showStockTrends(itemId) {
    showNotification('📈 Stock trends feature coming soon!', 'info');
}

// Utility functions
function showLoadingSpinner(message) {
    const spinner = document.createElement('div');
    spinner.id = 'loadingSpinner';
    spinner.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50';
    spinner.innerHTML = `
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-emerald-600"></div>
            <span class="text-gray-700">${message}</span>
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
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 4 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Enhanced success modal
function showSuccessModal(title, message, actions = []) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl border max-w-md w-full">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">${title}</h3>
                    <p class="text-sm text-gray-600 mb-6">${message}</p>
                    
                    <div class="flex justify-center space-x-3">
                        ${actions.map(action => `
                            <button 
                                onclick="${action.onclick}"
                                class="px-4 py-2 ${action.class} rounded-lg font-medium transition-colors duration-200"
                            >
                                ${action.text}
                            </button>
                        `).join('')}
                        <button 
                            onclick="this.closest('.fixed').remove()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Auto-remove after 10 seconds
    setTimeout(() => {
        if (modal.parentNode) {
            modal.remove();
        }
    }, 10000);
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID').format(amount);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Monitoring Stock Dashboard with Restock Request functionality initialized');
    
    // Set up select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.restock-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Update select all based on individual selections
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('restock-checkbox')) {
            const allCheckboxes = document.querySelectorAll('.restock-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.restock-checkbox:checked');
            const selectAllCheckbox = document.getElementById('selectAll');
            
            selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
        }
    });
});
</script>
</body>
</html>