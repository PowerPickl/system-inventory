<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Request Restock - Gudang Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


    <!-- BASIC TEST -->
<div style="background-color: red; color: white; padding: 20px; margin-bottom: 20px;">
    🔴 BASIC INLINE STYLES - This should be RED with WHITE text
</div>

<div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; background-color: lightblue; padding: 1rem;">
    <div style="background-color: white; padding: 1rem; text-align: center;">Col 1</div>
    <div style="background-color: white; padding: 1rem; text-align: center;">Col 2</div>
    <div style="background-color: white; padding: 1rem; text-align: center;">Col 3</div>
    <div style="background-color: white; padding: 1rem; text-align: center;">Col 4</div>
    <div style="background-color: white; padding: 1rem; text-align: center;">Col 5</div>
</div>
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
            <nav class="mt-6 pb-20">
                <div class="px-3">
                    <!-- Dashboard -->
                    <a href="/dashboard-gudang" class="flex items-center px-3 py-2 text-emerald-300 hover:text-white hover:bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Monitoring Stock -->
                    <a href="{{ route('gudang.monitoring-stock') }}" class="flex items-center px-3 py-2 text-emerald-300 hover:text-white hover:bg-emerald-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Monitoring Stock
                        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">EOQ</span>
                    </a>

                    <!-- Request Restock (Active) - FIXED COLORS -->
                    <a href="{{ route('gudang.restock-requests') }}" class="flex items-center px-3 py-2 text-white bg-emerald-600 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-white">Request Restock</span>
                        @if(isset($stats['pending']) && $stats['pending'] > 0)
                        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $stats['pending'] }}</span>
                        @endif
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
                            <h1 class="text-2xl font-semibold text-gray-900">Request Restock</h1>
                            <p class="text-gray-600 text-sm">Track and manage your restock requests</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('gudang.monitoring-stock') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200 text-sm font-medium">
                                📊 Create New Request
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- FIXED: Horizontal Statistics Cards -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Statistics</h3>
                        <div class="grid grid-cols-5 gap-6">
                            <!-- Total Requests -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $stats['total'] ?? 0 }}</h3>
                                    <p class="text-sm text-gray-600">Total Requests</p>
                                </div>
                            </div>

                            <!-- Pending -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-yellow-600 mb-1">{{ $stats['pending'] ?? 0 }}</h3>
                                    <p class="text-sm text-gray-600">Pending</p>
                                </div>
                            </div>

                            <!-- Approved -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-blue-600 mb-1">{{ $stats['approved'] ?? 0 }}</h3>
                                    <p class="text-sm text-gray-600">Approved</p>
                                </div>
                            </div>

                            <!-- Completed -->
                            <div class="text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-green-600 mb-1">{{ $stats['completed'] ?? 0 }}</h3>
                                    <p class="text-sm text-gray-600">Completed</p>
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
                                    <h3 class="text-2xl font-bold text-red-600 mb-1">{{ $stats['rejected'] ?? 0 }}</h3>
                                    <p class="text-sm text-gray-600">Rejected</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requests Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Restock Requests</h3>
                            <div class="flex space-x-3">
                                <select class="px-3 py-1 border border-gray-300 rounded-md text-sm" onchange="filterByStatus(this.value)">
                                    <option value="">All Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                                <button onclick="refreshRequests()" class="px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                                    🔄 Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="requestsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimated Cost</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="requestsTableBody">
                                @forelse($requests ?? [] as $request)
                                <tr class="hover:bg-gray-50" data-request-id="{{ $request->id_request }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->nomor_request }}</div>
                                        @if($request->catatan_request)
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($request->catatan_request, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->tanggal_request->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->tanggal_request->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->total_items }} items</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $request->details->take(2)->pluck('barang.nama_barang')->implode(', ') }}
                                            @if($request->details->count() > 2)
                                                <span class="text-blue-600">+{{ $request->details->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColor = match($request->status_request) {
                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                'Approved' => 'bg-blue-100 text-blue-800',
                                                'Completed' => 'bg-green-100 text-green-800',
                                                'Rejected' => 'bg-red-100 text-red-800',
                                                'Cancelled' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-semibold {{ $statusColor }} rounded-full">
                                            {{ $request->status_request }}
                                        </span>
                                        @if($request->tanggal_approved)
                                        <div class="text-xs text-gray-500 mt-1">{{ $request->tanggal_approved->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">Rp {{ number_format($request->total_estimasi_biaya) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($request->userApproved)
                                        <div class="text-sm text-gray-900">{{ $request->userApproved->name }}</div>
                                        @else
                                        <span class="text-xs text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('gudang.restock-requests.show', $request->id_request) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="View Details">
                                            👁️ View
                                        </a>
                                        @if($request->status_request === 'Pending')
                                        <button onclick="cancelRequest({{ $request->id_request }})" 
                                                class="text-red-600 hover:text-red-900" title="Cancel Request">
                                            ❌ Cancel
                                        </button>
                                        @endif
                                        @if($request->status_request === 'Approved')
                                        <span class="text-green-600" title="Waiting for delivery">⏳ Ordered</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Restock Requests</h3>
                                            <p class="text-gray-600 mb-4">You haven't created any restock requests yet.</p>
                                            <a href="{{ route('gudang.monitoring-stock') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                                                Create Your First Request
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($requests) && $requests->hasPages())
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $requests->links() }}
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Cancel request
        function cancelRequest(requestId) {
            if (!confirm('Are you sure you want to cancel this request?')) {
                return;
            }

            showLoadingSpinner('Cancelling request...');

            fetch(`/gudang/restock-requests/${requestId}/cancel`, {
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
                    // Refresh the page to update the table
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                hideLoadingSpinner();
                console.error('Error:', error);
                showNotification('Network error while cancelling request', 'error');
            });
        }

        // Filter by status
        function filterByStatus(status) {
            const rows = document.querySelectorAll('#requestsTableBody tr');
            
            rows.forEach(row => {
                if (status === '') {
                    row.style.display = '';
                } else {
                    const statusCell = row.querySelector('td:nth-child(4)');
                    if (statusCell) {
                        const statusSpan = statusCell.querySelector('span');
                        if (statusSpan) {
                            const statusText = statusSpan.textContent.trim();
                            
                            if (statusText === status) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    }
                }
            });
        }

        // Refresh requests
        function refreshRequests() {
            showNotification('Refreshing requests...', 'info');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
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

        // Auto-refresh pending requests status every 30 seconds
        setInterval(() => {
            const pendingRows = document.querySelectorAll('tr[data-request-id]');
            pendingRows.forEach(row => {
                const statusSpan = row.querySelector('td:nth-child(4) span');
                if (statusSpan && statusSpan.textContent.trim() === 'Pending') {
                    // Optionally check for status updates
                    console.log('Checking status for pending request...');
                }
            });
        }, 30000);
    </script>
</body>
</html>