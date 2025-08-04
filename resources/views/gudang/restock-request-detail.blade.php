<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Request Detail #{{ $request->nomor_request }} - Gudang Dashboard</title>

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

                    <!-- Request Restock (Active) -->
                    <a href="{{ route('gudang.restock-requests') }}" class="flex items-center px-3 py-2 text-white bg-emerald-600 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-white">Request Restock</span>
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
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('gudang.restock-requests') }}" class="text-gray-500 hover:text-gray-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">Request Detail #{{ $request->nomor_request }}</h1>
                                <p class="text-gray-600 text-sm">View complete details of your restock request</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            @php
                                $statusColor = match($request->status_request) {
                                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'Approved' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Ordered' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'Completed' => 'bg-green-100 text-green-800 border-green-200',
                                    'Rejected' => 'bg-red-100 text-red-800 border-red-200',
                                    'Terminated' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'Cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                            @endphp
                            <span class="px-4 py-2 text-sm font-semibold {{ $statusColor }} rounded-full border">
                                {{ $request->status_display }}
                            </span>
                            @if($request->status_request === 'Pending')
                            <button onclick="cancelRequest({{ $request->id_request }})" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
                                ❌ Cancel Request
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 space-y-6">
                <!-- Request Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Request Information</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Request Number</dt>
                                    <dd class="text-sm text-gray-900 font-mono">{{ $request->nomor_request }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Date Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $request->tanggal_request->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Requested by</dt>
                                    <dd class="text-sm text-gray-900">{{ $request->userGudang->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Total Items</dt>
                                    <dd class="text-sm text-gray-900">{{ $stats['total_items'] }} items</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Total Estimated Cost</dt>
                                    <dd class="text-sm text-gray-900 font-semibold">Rp {{ number_format($stats['total_cost']) }}</dd>
                                </div>
                                @if($request->catatan_request)
                                <div class="pt-3 border-t">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">Request Notes</dt>
                                    <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $request->catatan_request }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Approval Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">✅ Approval Information</h3>
                            <dl class="space-y-3">
                                @if($request->userApproved)
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Approved by</dt>
                                    <dd class="text-sm text-gray-900">{{ $request->userApproved->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Approval Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $request->tanggal_approved ? $request->tanggal_approved->format('d/m/Y H:i') : '-' }}</dd>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <p class="text-gray-500 text-sm">⏳ Waiting for approval</p>
                                </div>
                                @endif
                                
                                @if($request->userOrdered)
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Ordered by</dt>
                                    <dd class="text-sm text-gray-900">{{ $request->userOrdered->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $request->tanggal_ordered ? $request->tanggal_ordered->format('d/m/Y H:i') : '-' }}</dd>
                                </div>
                                @endif

                                @if($request->catatan_approval)
                                <div class="pt-3 border-t">
                                    <dt class="text-sm font-medium text-gray-500 mb-2">Approval Notes</dt>
                                    <dd class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $request->catatan_approval }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Workflow Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">🔄 Workflow Progress</h3>
                        <div class="relative">
                            <div class="absolute left-4 top-8 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="space-y-6">
                                @foreach($workflow as $index => $step)
                                <div class="relative flex items-start">
                                    <div class="flex-shrink-0">
                                        @if($step['status'] === 'completed')
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        @elseif($step['status'] === 'current')
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <div class="w-3 h-3 bg-blue-600 rounded-full animate-pulse"></div>
                                        </div>
                                        @elseif($step['status'] === 'rejected')
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        @elseif($step['status'] === 'terminated')
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        @else
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">{{ $step['icon'] }} {{ $step['name'] }}</p>
                                            @if($step['date'])
                                            <p class="text-xs text-gray-500">{{ $step['date']->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                        @if($step['user'])
                                        <p class="text-xs text-gray-500 mt-1">by {{ $step['user'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">📦 Requested Items</h3>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                @if($stats['original_items'] > 0)
                                <span class="flex items-center">
                                    <div class="w-3 h-3 bg-blue-100 rounded-full mr-2"></div>
                                    {{ $stats['original_items'] }} Original Items
                                </span>
                                @endif
                                @if($stats['additional_items'] > 0)
                                <span class="flex items-center">
                                    <div class="w-3 h-3 bg-green-100 rounded-full mr-2"></div>
                                    {{ $stats['additional_items'] }} Added by Owner
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 rounded-lg">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Requested</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Approved</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Cost</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($request->details as $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if(str_contains($detail->alasan_request ?? '', 'Additional item added by Owner'))
                                                <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                                @else
                                                <div class="w-3 h-3 bg-blue-400 rounded-full mr-3"></div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $detail->barang->nama_barang }}</div>
                                                    <div class="text-sm text-gray-500">{{ $detail->barang->kode_barang }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $currentStock = $detail->barang->stok ? $detail->barang->stok->jumlah_stok : 0;
                                                $stockStatus = $detail->barang->status_stok;
                                                $stockColor = match($stockStatus) {
                                                    'Habis' => 'text-red-600 bg-red-100',
                                                    'Perlu Restock' => 'text-yellow-600 bg-yellow-100',
                                                    'Aman' => 'text-green-600 bg-green-100',
                                                    default => 'text-gray-600 bg-gray-100'
                                                };
                                            @endphp
                                            <div class="text-sm text-gray-900">{{ $currentStock }} {{ $detail->barang->satuan }}</div>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $stockColor }} rounded-full">
                                                {{ $stockStatus }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $detail->qty_request }} {{ $detail->barang->satuan }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($detail->qty_approved)
                                            <div class="text-sm font-semibold text-green-600">{{ $detail->qty_approved }} {{ $detail->barang->satuan }}</div>
                                            @else
                                            <span class="text-sm text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">Rp {{ number_format($detail->barang->harga_beli) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($detail->estimasi_harga) }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                @if(str_contains($detail->alasan_request ?? '', 'Additional item added by Owner'))
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                                    ➕ Added by Owner
                                                </span>
                                                @else
                                                <span class="text-gray-600">{{ $detail->alasan_request ?? 'Standard restock' }}</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Total Estimated Cost:</td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900">Rp {{ number_format($stats['total_cost']) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('gudang.restock-requests') }}" 
                       class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        ← Back to Requests
                    </a>
                    
                    <div class="flex space-x-3">
                        @if($request->status_request === 'Pending')
                        <button onclick="cancelRequest({{ $request->id_request }})" 
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            ❌ Cancel Request
                        </button>
                        @endif
                        
                        <button onclick="window.print()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            🖨️ Print Details
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Cancel request
        function cancelRequest(requestId) {
            if (!confirm('Are you sure you want to cancel this request? This action cannot be undone.')) {
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
                    // Redirect back to requests list after 2 seconds
                    setTimeout(() => {
                        window.location.href = "{{ route('gudang.restock-requests') }}";
                    }, 2000);
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

        // Auto-refresh status every 30 seconds for pending requests
        @if($request->status_request === 'Pending')
        setInterval(() => {
            checkRequestStatus();
        }, 30000);

        function checkRequestStatus() {
            fetch(`/gudang/restock-requests/{{ $request->id_request }}/status`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.status !== 'Pending') {
                    showNotification('Request status has been updated!', 'info');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            })
            .catch(error => {
                console.log('Status check failed:', error);
            });
        }
        @endif

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
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Print styling
        const printStyles = `
            <style>
                @media print {
                    .no-print { display: none !important; }
                    body { font-size: 12px; }
                    .bg-emerald-800 { display: none; }
                    .flex { display: block; }
                    .w-64 { display: none; }
                    .flex-1 { width: 100%; }
                    .shadow-sm, .shadow-lg { box-shadow: none; }
                    .border { border: 1px solid #ccc; }
                    .rounded-xl, .rounded-lg { border-radius: 0; }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', printStyles);
    </script>
</body>
</html>