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
    
    <style>
        /* Custom styles for better modal and UI */
        .modal-overlay {
            backdrop-filter: blur(2px);
        }
        
        .restock-item-card {
            transition: all 0.2s ease;
        }
        
        .restock-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-success {
            background-color: #059669;
            border-color: #059669;
        }
        .btn-success:hover {
            background-color: #047857;
            border-color: #047857;
        }
        
        /* Better scrollbar for modal */
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }
        .modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .modal-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

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

        /* Row highlighting based on urgency */
        .row-urgent {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
        }

        .row-high {
            background-color: #fff7ed;
            border-left: 4px solid #f97316;
        }

        .row-medium {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
        }

    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <x-gudang.sidebar active="monitoring" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">Monitoring Stock - EOQ Analysis</h1>
                            <p class="text-gray-600 text-sm mt-1">Real-time inventory monitoring dengan scientific calculations</p>
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
                            <button onclick="updateAllEOQ()" class="btn-success text-white px-4 py-2 rounded-lg transition-colors duration-200 text-sm font-medium shadow-sm">
                                🔄 Update All EOQ
                            </button>
                            
                            
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 space-y-6">
                <!-- EOQ Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Total Items -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-emerald-100 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $eoqStats['total_items'] }}</h3>
                            <p class="text-gray-600 text-xs">Total Items</p>
                        </div>
                    </div>
                </div>

                <!-- URGENT Items -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-red-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <span class="text-red-600 text-lg">🚨</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-red-900">{{ $eoqStats['urgency_breakdown']['urgent'] ?? 0 }}</h3>
                            <p class="text-red-600 text-xs font-medium">URGENT</p>
                        </div>
                    </div>
                </div>

                <!-- HIGH Priority Items -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-orange-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <span class="text-orange-600 text-lg">⚠️</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-orange-900">{{ $eoqStats['urgency_breakdown']['high'] ?? 0 }}</h3>
                            <p class="text-orange-600 text-xs font-medium">HIGH</p>
                        </div>
                    </div>
                </div>

                <!-- MEDIUM Priority Items -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-yellow-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <span class="text-yellow-600 text-lg">📋</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-yellow-900">{{ $eoqStats['urgency_breakdown']['medium'] ?? 0 }}</h3>
                            <p class="text-yellow-600 text-xs font-medium">MEDIUM</p>
                        </div>
                    </div>
                </div>

                <!-- LOW Priority Items -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-blue-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <span class="text-blue-600 text-lg">📅</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-blue-900">{{ $eoqStats['urgency_breakdown']['low'] ?? 0 }}</h3>
                            <p class="text-blue-600 text-xs font-medium">LOW</p>
                        </div>
                    </div>
                </div>

                <!-- NORMAL Items -->
                <div class="bg-white rounded-xl shadow-sm p-4 border border-green-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <span class="text-green-600 text-lg">✅</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-green-900">{{ $eoqStats['urgency_breakdown']['normal'] ?? 0 }}</h3>
                            <p class="text-green-600 text-xs font-medium">NORMAL</p>
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
                                <button onclick="selectAllRestock()" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition-colors">
                                    Select All Restock
                                </button>
                                <button onclick="createBulkRestockRequest()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 text-sm font-medium shadow-sm">
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
                                @php
                                    $urgency = $data['urgency_data']['final_urgency'] ?? 'NORMAL';
                                    $rowClass = match($urgency) {
                                        'URGENT' => 'row-urgent',
                                        'HIGH' => 'row-high', 
                                        'MEDIUM' => 'row-medium',
                                        default => ''
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors {{ $rowClass }}" data-item-id="{{ $data['item']->id_barang }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($data['urgency_data']['action_needed'] ?? $data['recommendation']['need_restock'])
                                        <input type="checkbox" class="restock-checkbox rounded" value="{{ $data['item']->id_barang }}">
                                        @endif
                                    </td>
                                    
                                    <!-- Item Info dengan Kategori -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $data['item']->nama_barang }}</div>
                                                @if(isset($data['urgency_badge']))
                                                <span class="urgency-badge urgency-{{ strtolower($urgency) }}">
                                                    {{ $data['urgency_badge']['icon'] ?? '📋' }} {{ $urgency }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $data['item']->kode_barang }}</div>
                                            @if($data['item']->kategori ?? false)
                                            <div class="text-xs text-gray-400 mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $data['item']->kategori->nama_kategori }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Current Stock dengan Urgency Info -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $data['current_stock'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $data['item']->satuan }}</div>
                                        @if(isset($data['urgency_data']['days_until_stockout']) && $data['urgency_data']['days_until_stockout'])
                                        <div class="text-xs text-red-600">
                                            ~{{ $data['urgency_data']['days_until_stockout'] }} days left
                                        </div>
                                        @endif
                                    </td>
                                    
                                    <!-- EOQ -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $data['eoq'] ?? '-' }}</div>
                                        @if($data['eoq'])
                                        <div class="text-xs text-blue-600 cursor-pointer hover:text-blue-800" onclick="showEOQDetails({{ $data['item']->id_barang }})">
                                            📊 View Calculation
                                        </div>
                                        @endif
                                    </td>
                                    
                                    <!-- ROP -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $data['rop'] }}</div>
                                    </td>
                                    
                                    <!-- Safety Stock -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $data['safety_stock'] ?? '-' }}</div>
                                    </td>
                                    
                                    <!-- Status dengan Demand Level -->
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
                                        @if(isset($data['urgency_data']['demand_level']))
                                        <div class="mt-1">
                                            @php
                                                $demandColor = match($data['urgency_data']['demand_level']) {
                                                    'High' => 'bg-red-100 text-red-800',
                                                    'Medium' => 'bg-yellow-100 text-yellow-800',
                                                    'Low' => 'bg-blue-100 text-blue-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold {{ $demandColor }} rounded-full">
                                                {{ $data['urgency_data']['demand_level'] }} Demand
                                            </span>
                                        </div>
                                        @endif
                                    </td>
                                    
                                    <!-- Recommendation dengan AI Reason -->
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs">
                                            @if($data['urgency_data']['action_needed'] ?? $data['recommendation']['need_restock'])
                                                <div class="text-sm font-medium text-orange-600 mb-1">
                                                    Order {{ $data['recommendation']['recommended_qty'] }} units
                                                </div>
                                                @if(isset($data['auto_reason']))
                                                <div class="text-xs text-gray-600 leading-relaxed">
                                                    {{ $data['auto_reason'] }}
                                                </div>
                                                @else
                                                <div class="text-xs text-gray-500">
                                                    Urgency: {{ $data['recommendation']['urgency'] }}
                                                </div>
                                                @endif
                                            @else
                                                <span class="text-sm text-green-600">✅ Stock adequate</span>
                                                <div class="text-xs text-gray-500 mt-1">No action needed</div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button onclick="updateSingleEOQ({{ $data['item']->id_barang }})" 
                                                class="text-blue-600 hover:text-blue-900 transition-colors" title="Update EOQ">
                                            🔄
                                        </button>
                                        <button onclick="showStockTrends({{ $data['item']->id_barang }})" 
                                                class="text-green-600 hover:text-green-900 transition-colors" title="View Trends">
                                            📈
                                        </button>
                                        @if($data['urgency_data']['action_needed'] ?? $data['recommendation']['need_restock'])
                                        <button onclick="quickRestock({{ $data['item']->id_barang }}, {{ $data['recommendation']['recommended_qty'] }}, '{{ addslashes($data['auto_reason'] ?? 'Urgent restock needed') }}')" 
                                                class="text-orange-600 hover:text-orange-900 transition-colors" title="Quick Restock">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Stock Movements (Today)</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($recentMovements as $movement)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
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

<script>
// Enhanced JavaScript for Monitoring Stock with Improved Modal UX

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
// Update table with new data including urgency
function updateTableData(items) {
    const tbody = document.getElementById('monitoringTableBody');
    
    items.forEach(item => {
        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
        if (row) {
            // Update current stock
            const stockCell = row.children[2];
            stockCell.querySelector('.text-sm').textContent = item.current_stock;
            
            // Update urgency badge if exists
            const itemCell = row.children[1];
            const urgencyBadge = itemCell.querySelector('.urgency-badge');
            if (urgencyBadge && item.urgency_level) {
                urgencyBadge.className = `urgency-badge urgency-${item.urgency_level.toLowerCase()}`;
                urgencyBadge.innerHTML = `${item.urgency_badge?.icon || '📋'} ${item.urgency_level}`;
            }
            
            // Update days until stockout
            const daysDiv = stockCell.querySelector('.text-red-600');
            if (item.days_until_stockout && daysDiv) {
                daysDiv.textContent = `~${item.days_until_stockout} days left`;
            }
            
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
            badge.className = 'px-2 py-1 text-xs font-semibold rounded-full ' + getStatusBadgeColor(item.status);
            
            // Update recommendation with urgency context
            const recCell = row.children[7];
            if (item.need_restock || item.urgency_level !== 'NORMAL') {
                recCell.innerHTML = `
                    <div class="max-w-xs">
                        <div class="text-sm font-medium text-orange-600 mb-1">
                            Order ${item.recommended_qty || 'N/A'} units
                        </div>
                        <div class="text-xs text-gray-600 leading-relaxed">
                            ${item.auto_reason || `Urgency: ${item.urgency || 'Normal'}`}
                        </div>
                    </div>
                `;
            } else {
                recCell.innerHTML = `
                    <span class="text-sm text-green-600">✅ Stock adequate</span>
                    <div class="text-xs text-gray-500 mt-1">No action needed</div>
                `;
            }
            
            // Update row highlighting based on urgency
            row.className = row.className.replace(/row-(urgent|high|medium)/, '');
            if (item.urgency_level === 'URGENT') {
                row.classList.add('row-urgent');
            } else if (item.urgency_level === 'HIGH') {
                row.classList.add('row-high');
            } else if (item.urgency_level === 'MEDIUM') {
                row.classList.add('row-medium');
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

   // Ultra compact EOQ details modal
function showEOQDetails(itemId) {
    showLoadingSpinner('Loading EOQ calculation details...');
    
    fetch(`/gudang/monitoring-stock/eoq-details/${itemId}`)
    .then(response => response.json())
    .then(data => {
        hideLoadingSpinner();
        
        if (data.success) {
            data.item_id = itemId;
            displayUltraCompactEOQModal(data);
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

// Ultra compact EOQ modal
function displayUltraCompactEOQModal(data) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 modal-overlay overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4';
    
    // Extract calculation data
    const eoq = data.calculations.eoq;
    const safetyStock = data.calculations.safety_stock;
    const rop = data.calculations.rop;
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl border max-w-md w-full mx-4">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-blue-50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">📊 EOQ Calculation</h3>
                    <p class="text-sm text-gray-600">${data.item.name}</p>
                </div>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Results Grid -->
            <div class="p-4">
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="text-center p-3 bg-blue-50 rounded border">
                        <div class="text-2xl font-bold text-blue-600">${eoq.eoq}</div>
                        <div class="text-xs text-gray-600">EOQ</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded border">
                        <div class="text-2xl font-bold text-green-600">${safetyStock.safety_stock}</div>
                        <div class="text-xs text-gray-600">Safety Stock</div>
                    </div>
                    <div class="text-center p-3 bg-orange-50 rounded border">
                        <div class="text-2xl font-bold text-orange-600">${rop.rop}</div>
                        <div class="text-xs text-gray-600">ROP</div>
                    </div>
                </div>

                <!-- Compact Accordion -->
                <div class="space-y-2">
                    
                    <!-- EOQ Details -->
                    <div class="border border-blue-200 rounded">
                        <button onclick="toggleCompactAccordion('eoq-calc')" class="w-full flex items-center justify-between px-3 py-2 bg-blue-50 hover:bg-blue-100 text-sm rounded-t">
                            <span class="font-medium text-blue-900">🔵 EOQ Details</span>
                            <svg id="eoq-calc-icon" class="w-4 h-4 text-blue-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="eoq-calc" class="hidden px-3 py-2 border-t border-blue-200 bg-white text-xs">
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <div class="flex justify-between">
                                    <span>D:</span><span class="font-medium">${eoq.D}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>S:</span><span class="font-medium">Rp ${formatCurrency(eoq.S)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>H:</span><span class="font-medium">${eoq.H_percentage}%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>H(Rp):</span><span class="font-medium">Rp ${formatCurrency(eoq.H)}</span>
                                </div>
                            </div>
                            <div class="text-center space-y-1">
                                <div class="font-mono bg-gray-100 p-1 rounded text-xs">√(2×D×S/H)</div>
                                <div class="font-mono bg-blue-100 p-1 rounded text-xs">√(2×${eoq.D}×${eoq.S}/${eoq.H})</div>
                                <div class="font-bold text-blue-600">${eoq.eoq} units</div>
                            </div>
                        </div>
                    </div>

                    <!-- Safety Stock Details -->
                    <div class="border border-green-200 rounded">
                        <button onclick="toggleCompactAccordion('ss-calc')" class="w-full flex items-center justify-between px-3 py-2 bg-green-50 hover:bg-green-100 text-sm rounded-t">
                            <span class="font-medium text-green-900">🟢 Safety Stock Details</span>
                            <svg id="ss-calc-icon" class="w-4 h-4 text-green-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="ss-calc" class="hidden px-3 py-2 border-t border-green-200 bg-white text-xs">
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <div class="flex justify-between">
                                    <span>Avg Daily:</span><span class="font-medium">${safetyStock.davg}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Max Daily:</span><span class="font-medium">${safetyStock.dmax}</span>
                                </div>
                                <div class="flex justify-between col-span-2">
                                    <span>Lead Time:</span><span class="font-medium">${safetyStock.lead_time} days</span>
                                </div>
                            </div>
                            <div class="text-center space-y-1">
                                <div class="font-mono bg-gray-100 p-1 rounded text-xs">(Max - Avg) × Lead</div>
                                <div class="font-mono bg-green-100 p-1 rounded text-xs">(${safetyStock.dmax} - ${safetyStock.davg}) × ${safetyStock.lead_time}</div>
                                <div class="font-bold text-green-600">${safetyStock.safety_stock} units</div>
                            </div>
                        </div>
                    </div>

                    <!-- ROP Details -->
                    <div class="border border-orange-200 rounded">
                        <button onclick="toggleCompactAccordion('rop-calc')" class="w-full flex items-center justify-between px-3 py-2 bg-orange-50 hover:bg-orange-100 text-sm rounded-t">
                            <span class="font-medium text-orange-900">🟠 ROP Details</span>
                            <svg id="rop-calc-icon" class="w-4 h-4 text-orange-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="rop-calc" class="hidden px-3 py-2 border-t border-orange-200 bg-white text-xs">
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <div class="flex justify-between">
                                    <span>Avg Daily:</span><span class="font-medium">${rop.davg}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Lead Time:</span><span class="font-medium">${rop.lead_time} days</span>
                                </div>
                                <div class="flex justify-between col-span-2">
                                    <span>Safety Stock:</span><span class="font-medium">${rop.safety_stock}</span>
                                </div>
                            </div>
                            <div class="text-center space-y-1">
                                <div class="font-mono bg-gray-100 p-1 rounded text-xs">(Avg × Lead) + Safety</div>
                                <div class="font-mono bg-orange-100 p-1 rounded text-xs">(${rop.davg} × ${rop.lead_time}) + ${rop.safety_stock}</div>
                                <div class="font-mono bg-orange-50 p-1 rounded text-xs">${rop.davg * rop.lead_time} + ${rop.safety_stock}</div>
                                <div class="font-bold text-orange-600">${rop.rop} units</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="mt-4 bg-gray-50 rounded p-3 text-sm">
                    <span class="font-medium">📋 Summary:</span> Order <strong>${eoq.eoq}</strong> when stock ≤ <strong>${rop.rop}</strong>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                <button onclick="updateSingleEOQ(${data.item_id || 'null'})" class="px-4 py-2 btn-success text-white rounded text-sm font-medium">
                    🔄 Update
                </button>
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 text-sm font-medium">
                    Close
                </button>
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

// Compact accordion toggle
function toggleCompactAccordion(detailId) {
    const detailElement = document.getElementById(detailId);
    const iconElement = document.getElementById(detailId + '-icon');
    
    if (detailElement.classList.contains('hidden')) {
        detailElement.classList.remove('hidden');
        iconElement.style.transform = 'rotate(180deg)';
    } else {
        detailElement.classList.add('hidden');
        iconElement.style.transform = 'rotate(0deg)';
    }
}

// Helper function for currency formatting
function formatCurrency(amount) {
    if (!amount || isNaN(amount)) return '0';
    return new Intl.NumberFormat('id-ID').format(parseFloat(amount));
}

// ===========================================
// RESTOCK REQUEST FUNCTIONALITY - IMPROVED RESPONSIVE MODAL
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

// Generate smart reasoning with demand level context
function generateSmartReason(item) {
    const stock = item.current_stock || 0;
    const itemName = item.nama_barang || 'Item';
    
    // Get status and demand info
    const urgency = item.urgency || item.urgency_level || 'Normal';
    const status = item.status || item.eoq_status || '';
    const demandLevel = item.demand_level || 'Unknown';
    
    console.log('Item data for reasoning:', item); // Debug log
    
    // Enhanced reasoning with demand context
    if (urgency === 'Critical' || status.includes('Critical') || status.includes('Out of Stock')) {
        if (demandLevel === 'High') {
            return `🚨 CRITICAL + HIGH DEMAND: ${itemName} has critical stock (${stock}) with high demand pattern. IMMEDIATE restock required - this should be Owner's TOP PRIORITY to avoid major service disruption.`;
        } else if (demandLevel === 'Medium') {
            return `⚠️ CRITICAL + MEDIUM DEMAND: ${itemName} has critical stock (${stock}) with moderate demand. Urgent restock needed within 24 hours.`;
        } else if (demandLevel === 'Low') {
            return `📋 CRITICAL + LOW DEMAND: ${itemName} has critical stock (${stock}) but low demand pattern. Can be scheduled after high-demand items.`;
        } else {
            return `🚨 CRITICAL: ${itemName} has critical stock level (${stock}). Immediate restock required.`;
        }
    } else if (urgency === 'High' || status.includes('Reorder Required')) {
        if (demandLevel === 'High') {
            return `🔥 HIGH PRIORITY + HIGH DEMAND: ${itemName} is below reorder point with high demand. Restock within 24-48 hours to prevent stockout.`;
        } else if (demandLevel === 'Medium') {
            return `⚠️ HIGH PRIORITY + MEDIUM DEMAND: ${itemName} below reorder point with moderate demand. Plan restock within 3-5 days.`;
        } else if (demandLevel === 'Low') {
            return `📅 HIGH PRIORITY + LOW DEMAND: ${itemName} below reorder point but low demand. Can wait for bulk procurement.`;
        } else {
            return `⚠️ HIGH PRIORITY: ${itemName} is below reorder point. Restock needed within 24-48 hours.`;
        }
    } else if (urgency === 'Medium' || status.includes('Monitor')) {
        if (demandLevel === 'High') {
            return `📊 MEDIUM + HIGH DEMAND: ${itemName} needs monitoring due to high demand pattern. Consider preventive restock.`;
        } else {
            return `📋 MEDIUM PRIORITY: ${itemName} requires monitoring. Plan restock with next procurement cycle.`;
        }
    } else if (urgency === 'Low') {
        return `📅 LOW PRIORITY: ${itemName} - Low urgency, restock when convenient or combine with other orders.`;
    } else {
        // Default with demand context if available
        if (demandLevel && demandLevel !== 'Unknown') {
            return `📋 STANDARD + ${demandLevel.toUpperCase()} DEMAND: ${itemName} - Restock recommended based on ${demandLevel.toLowerCase()} demand pattern.`;
        } else {
            return `📋 STANDARD: ${itemName} - Restock recommended based on current inventory analysis.`;
        }
    }
}

// Toggle custom reason input
function toggleCustomReason(index) {
    const customDiv = document.getElementById(`customReason-${index}`);
    const checkbox = event.target;
    
    if (checkbox.checked) {
        customDiv.classList.remove('hidden');
        customDiv.querySelector('textarea').focus();
    } else {
        customDiv.classList.add('hidden');
        // Reset to auto-generated reason
        const hiddenInput = document.querySelector(`input[name="items[${index}][alasan_request]"]`);
        const originalReason = customDiv.parentElement.parentElement.querySelector('.text-blue-800').textContent;
        hiddenInput.value = originalReason;
    }
}

// Update custom reason
function updateCustomReason(index) {
    const textarea = event.target;
    const hiddenInput = document.querySelector(`input[name="items[${index}][alasan_request]"]`);
    hiddenInput.value = textarea.value;
}

// Toggle custom reason input
function toggleCustomReason(index) {
    const customDiv = document.getElementById(`customReason-${index}`);
    const checkbox = event.target;
    
    if (checkbox.checked) {
        customDiv.classList.remove('hidden');
    } else {
        customDiv.classList.add('hidden');
        // Reset to AI reason
        const hiddenInput = document.querySelector(`input[name="items[${index}][alasan_request]"]`);
        const originalReason = customDiv.previousElementSibling.previousElementSibling.querySelector('.text-blue-800').textContent;
        hiddenInput.value = originalReason;
    }
}

// Get demand badge color
function getDemandBadgeColor(demandLevel) {
    switch(demandLevel) {
        case 'High': return 'bg-red-100 text-red-800';
        case 'Medium': return 'bg-yellow-100 text-yellow-800';
        case 'Low': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

// Calculate priority score for visual emphasis
function calculatePriorityScore(urgency, demandLevel) {
    let score = 0;
    
    // Urgency scoring
    switch(urgency) {
        case 'Critical': score += 40; break;
        case 'High': score += 30; break;
        case 'Medium': score += 20; break;
        case 'Low': score += 10; break;
        default: score += 15;
    }
    
    // Demand scoring
    switch(demandLevel) {
        case 'High': score += 30; break;
        case 'Medium': score += 20; break;
        case 'Low': score += 10; break;
        default: score += 15;
    }
    
    return score;
}

// Get priority score color
function getPriorityScoreColor(urgency, demandLevel) {
    const score = calculatePriorityScore(urgency, demandLevel);
    if (score >= 60) return 'text-red-600';
    if (score >= 45) return 'text-orange-600';
    if (score >= 30) return 'text-yellow-600';
    return 'text-blue-600';
}


// Update custom reason
function updateCustomReason(index) {
    const textarea = event.target;
    const hiddenInput = document.querySelector(`input[name="items[${index}][alasan_request]"]`);
    hiddenInput.value = textarea.value;
}

// Show restock request modal with auto-generated reasoning (Fixed)
function showRestockRequestModal(items, totalEstimasi) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 modal-overlay overflow-y-auto h-full w-full z-50 p-4';
    modal.innerHTML = `
        <div class="min-h-full flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl border w-full max-w-3xl max-h-[85vh] overflow-hidden flex flex-col my-8">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">🚀 Create Restock Request</h3>
                        <p class="text-sm text-gray-600 mt-1">Review ${items.length} items for restocking</p>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Content -->
                <div class="flex-1 overflow-y-auto modal-content">
                    <form id="restockRequestForm" class="p-4">
                        <!-- Items List -->
                        <div class="space-y-3 mb-4">
                            <h4 class="font-medium text-gray-900 mb-3">📦 Items to Restock</h4>
                            
                            <div class="space-y-3" id="restockItemsList">
                                ${items.map((item, index) => {
                                    // Generate smart reasoning
                                    const autoReason = generateSmartReason(item);
                                    
                                    return `
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition-colors restock-item-card">
                                        <!-- Item Header -->
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <h5 class="font-medium text-gray-900 text-sm">${item.nama_barang}</h5>
                                                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${getUrgencyBadgeColor(item.urgency)}">
                                                        ${item.urgency}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-600">${item.kode_barang}</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="text-xs text-gray-500">Stock: ${item.current_stock} ${item.satuan}</span>
                                                    ${item.demand_level ? `
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full ${getDemandBadgeColor(item.demand_level)}">
                                                        ${item.demand_level} Demand
                                                    </span>
                                                    ` : ''}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-gray-500">Priority Score</div>
                                                <div class="text-lg font-bold ${getPriorityScoreColor(item.urgency, item.demand_level)}">
                                                    ${calculatePriorityScore(item.urgency, item.demand_level)}
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Auto-Generated Reason Display -->
                                        <div class="mb-3 p-3 bg-blue-50 rounded border-l-4 border-blue-400">
                                            <div class="text-sm font-medium text-blue-900 mb-1">📋 Recommended Reason:</div>
                                            <div class="text-sm text-blue-800">${autoReason}</div>
                                        </div>
                                        
                                        <!-- Form Fields -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <!-- Quantity -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                                                <input 
                                                    type="number" 
                                                    name="items[${index}][qty_request]" 
                                                    value="${item.recommended_qty || item.eoq || 1}"
                                                    min="1"
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                                                    onchange="updateItemTotal(${index}, ${item.harga_beli || 0})"
                                                >
                                                <p class="text-xs text-blue-600 mt-0.5">EOQ: ${item.eoq || 'N/A'}</p>
                                                <input type="hidden" name="items[${index}][id_barang]" value="${item.id_barang}">
                                                <input type="hidden" name="items[${index}][alasan_request]" value="${autoReason}">
                                            </div>
                                            
                                            <!-- Unit Price -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Unit Price</label>
                                                <p class="text-sm text-gray-900 py-1.5">Rp ${formatCurrency(item.harga_beli || 0)}</p>
                                            </div>
                                            
                                            <!-- Total -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Total</label>
                                                <p class="text-sm font-medium text-gray-900 py-1.5 item-total-${index}">Rp ${formatCurrency(item.estimasi_total || 0)}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Override Option -->
                                        <div class="mt-3">
                                            <label class="flex items-center text-xs text-gray-600">
                                                <input type="checkbox" class="mr-2 rounded" onchange="toggleCustomReason(${index})">
                                                Use custom reason instead
                                            </label>
                                            <div id="customReason-${index}" class="hidden mt-2">
                                                <textarea 
                                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500"
                                                    rows="2"
                                                    placeholder="Enter your custom reason here..."
                                                    onchange="updateCustomReason(${index})"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                `}).join('')}
                            </div>
                        </div>
                        
                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">📝 Additional Notes</label>
                            <textarea 
                                name="catatan_request" 
                                rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                                placeholder="Optional notes for this restock request..."
                            ></textarea>
                        </div>
                        
                        <!-- Summary -->
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-emerald-900 text-sm">📊 Request Summary</h4>
                                    <p class="text-xs text-emerald-700">Total Items: ${items.length}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-emerald-700">Estimated Cost</p>
                                    <p class="text-lg font-bold text-emerald-900" id="totalEstimatedCost">Rp ${formatCurrency(totalEstimasi || 0)}</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 flex-shrink-0">
                    <button 
                        type="button" 
                        onclick="this.closest('.fixed').remove()" 
                        class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        onclick="submitRestockRequest()" 
                        class="w-full sm:w-auto px-6 py-2 btn-success text-white rounded-lg transition-colors duration-200 font-medium"
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
                        class: 'btn-success text-white',
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

// Quick restock for single item - IMPROVED COMPACT VERSION
// Enhanced quick restock with urgency context
function quickRestock(itemId, recommendedQty, autoReason = '') {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 modal-overlay overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl border max-w-md w-full mx-4">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">⚡ Smart Quick Restock</h3>
                <p class="text-sm text-gray-600 mt-1">AI-powered instant restock request</p>
            </div>
            
            <!-- Content -->
            <div class="p-4">
                <form id="quickRestockForm">
                    <input type="hidden" name="id_barang" value="${itemId}">
                    
                    ${autoReason ? `
                    <!-- AI-Generated Reason Display -->
                    <div class="mb-4 p-3 bg-blue-50 rounded border-l-4 border-blue-400">
                        <div class="text-sm font-medium text-blue-900 mb-1">🤖 AI Analysis:</div>
                        <div class="text-sm text-blue-800">${autoReason}</div>
                    </div>
                    ` : ''}
                    
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
                        <p class="text-xs text-blue-600 mt-1">💡 AI Recommended: ${recommendedQty} units</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Override Reason (Optional)</label>
                        <textarea 
                            name="alasan_request" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Leave empty to use AI-generated reason..."
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">AI reason will be used if left empty</p>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="p-4 border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                <button 
                    type="button" 
                    onclick="this.closest('.fixed').remove()" 
                    class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 font-medium"
                >
                    Cancel
                </button>
                <button 
                    type="button" 
                    onclick="submitQuickRestock()" 
                    class="w-full sm:w-auto px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 font-medium"
                >
                    ⚡ Smart Restock
                </button>
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
    spinner.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50';
    spinner.innerHTML = `
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3 mx-4">
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
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 max-w-sm`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 4 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Enhanced success modal - RESPONSIVE VERSION
function showSuccessModal(title, message, actions = []) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 modal-overlay overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl border max-w-md w-full mx-4">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">${title}</h3>
                <p class="text-sm text-gray-600 mb-6">${message}</p>
                
                <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-3">
                    ${actions.map(action => `
                        <button 
                            onclick="${action.onclick}"
                            class="w-full sm:w-auto px-4 py-2 ${action.class} rounded-lg font-medium transition-colors duration-200"
                        >
                            ${action.text}
                        </button>
                    `).join('')}
                    <button 
                        onclick="this.closest('.fixed').remove()" 
                        class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 font-medium transition-colors"
                    >
                        Close
                    </button>
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
    console.log('🚀 Smart Monitoring Stock Dashboard with AI-Powered Urgency System initialized');
    
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