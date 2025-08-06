<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - RESTOCK APPROVAL FIXED</title>


       
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

        /* Horizontal Stats Divider */
        .stats-divider > div:not(:last-child) {
            border-right: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .stats-divider > div:nth-child(2n) {
                border-right: none;
            }
            .stats-divider > div:nth-child(1),
            .stats-divider > div:nth-child(2) {
                border-bottom: 1px solid #e5e7eb;
            }
        }


        #editModal .bg-white {
        max-height: 95vh !important;
        height: auto !important;
    }

    #modalContent {
        max-height: calc(95vh - 120px);
        overflow-y: auto !important;
    }

    /* Better scrollbar untuk modal */
    #modalContent::-webkit-scrollbar {
        width: 8px;
    }

    #modalContent::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    #modalContent::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    #modalContent::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Responsive table di modal */
    @media (max-width: 1024px) {
        #editModal .max-w-6xl {
            max-width: 95vw !important;
            margin: 1rem;
        }
        
        #itemsTable {
            font-size: 0.875rem;
        }
        
        #itemsTable th,
        #itemsTable td {
            padding: 0.5rem !important;
        }
        
        #itemsTable input[type="number"] {
            width: 60px !important;
        }
    }

    @media (max-width: 768px) {
        #editModal .max-w-6xl {
            max-width: 100vw !important;
            margin: 0;
            border-radius: 0;
        }
        
        #itemsTable {
            font-size: 0.75rem;
        }
    }


    </style>


</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-slate-800 shadow-lg relative">
            <!-- Logo/Brand -->
            <div class="p-6 border-b border-slate-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-white font-semibold">Bengkel Inventory</h3>
                        <p class="text-slate-400 text-sm">Owner Panel</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 pb-20">
                <div class="px-3">
                    <!-- Dashboard -->
                    <a href="{{ route('owner.dashboard') }}" class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v10H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Kelola Data User -->
                    <a href="{{ route('owner.kelola-user.index') }}" class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v1M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Kelola Data User
                    </a>

                    <!-- Restock Approval - ACTIVE -->
                    <a href="{{ route('owner.restock-approval.index') }}" class="flex items-center px-3 py-2 text-white bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Restock Approval
                        @if($stats['total_pending'] > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $stats['total_pending'] }}</span>
                        @endif
                    </a>

                    <!-- Laporan -->
                    <a href="{{ route('owner.simple-reports.index') }}" class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Laporan
                    </a>
                </div>
            </nav>

            <!-- User Info & Logout -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700 bg-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-white text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="text-slate-400 text-xs">Owner</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white transition-colors duration-200" title="Logout">
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
                            <h1 class="text-2xl font-semibold text-gray-900">Restock Approval</h1>
                            <p class="text-gray-600 text-sm">Review and approve restock requests from warehouse</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span id="pendingCount">{{ $stats['total_pending'] }}</span> Pending
                            </div>
                            <button onclick="refreshData()" id="refreshBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                    <!-- Compact Stats Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                            <!-- Pending Requests -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-yellow-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xl font-bold text-gray-900" id="statPending">{{ $stats['total_pending'] }}</p>
                                        <p class="text-xs text-gray-600">Pending Requests</p>
                                    </div>
                                </div>
                            </div>

                            <!-- This Month -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-green-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xl font-bold text-gray-900">{{ $stats['total_this_month'] }}</p>
                                        <p class="text-xs text-gray-600">This Month</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Est Cost - FULL NUMBER -->
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 rounded-lg flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_estimated_cost'], 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-600">Est. Cost</p>
                                    </div>
                                </div>
                            </div>

                            <!-- URGENT Requests -->
                            <div class="bg-white rounded-xl shadow-sm border border-red-200 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-red-100 rounded-lg flex-shrink-0">
                                        <span class="text-red-600 text-lg">🚨</span>
                                    </div>
                                    <div>
                                        <p class="text-xl font-bold text-red-900">{{ $stats['urgent_requests'] ?? 0 }}</p>
                                        <p class="text-xs text-red-600 font-medium">URGENT</p>
                                    </div>
                                </div>
                            </div>

                            <!-- HIGH Priority -->
                            <div class="bg-white rounded-xl shadow-sm border border-orange-200 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-orange-100 rounded-lg flex-shrink-0">
                                        <span class="text-orange-600 text-lg">⚠️</span>
                                    </div>
                                    <div>
                                        <p class="text-xl font-bold text-orange-900">{{ $stats['high_priority_requests'] ?? 0 }}</p>
                                        <p class="text-xs text-orange-600 font-medium">HIGH</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    

              
                <!-- Main Table - Clean & Simple -->
               
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Simple Header -->
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Pending Restock Requests
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">Review and approve inventory restock requests from warehouse team</p>
                                </div>
                                
                                <!-- Summary Stats - Horizontal Layout with Better Spacing -->
                                <div class="flex items-center space-x-8">
                                    @php
                                        $totalCost = $pendingRequests->sum('total_estimasi_biaya');
                                        $totalRequests = $pendingRequests->count();
                                        $urgentCount = 0;
                                        $urgentRequests = $pendingRequests->where('primary_urgency', 'URGENT')->count();
                                        $highRequests = $pendingRequests->where('primary_urgency', 'HIGH')->count();
                                        $totalUrgentItems = $pendingRequests->sum('urgent_items_count');
                                        foreach($pendingRequests as $request) {
                                            $urgentItems = $request->details->filter(function($detail) {
                                                return $detail->barang->stok && $detail->barang->stok->jumlah_stok <= 0;
                                            })->count();
                                            if($urgentItems > 0) $urgentCount++;
                                        }   
                                    @endphp
                                    
                                    <div class="text-center px-4 py-2">
                                        <div class="text-xs text-orange-600 font-medium uppercase tracking-wide mb-1">Pending Requests</div>
                                        <div class="text-lg font-bold text-orange-700">{{ $totalRequests }}</div>
                                    </div>
                                    <div class="text-center px-4 py-2">
                                        <div class="text-xs text-red-600 font-medium uppercase tracking-wide mb-1">URGENT Requests</div>
                                        <div class="text-lg font-bold text-red-700">{{ $urgentRequests }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Clean Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span>Request Details</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                <span>Items</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                <span>Est. Cost</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                <span>Priority</span>
                                            </div>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>Actions</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pendingRequests as $request)
                                @php
                                        $urgency = $request->primary_urgency ?? 'NORMAL';
                                        $rowClass = match($urgency) {
                                            'URGENT' => 'row-urgent',
                                            'HIGH' => 'row-high', 
                                            'MEDIUM' => 'row-medium',
                                            default => ''
                                        };
                                    @endphp
                                        <tr class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer {{ $rowClass }}" onclick="toggleDetails('{{ $request->id_request }}')">
                                            <!-- Request Info Column -->
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-gray-400 mr-3 transition-transform" id="arrow-{{ $request->id_request }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="flex items-center space-x-2">
                                                            <div class="text-sm font-medium text-gray-900">{{ $request->nomor_request }}</div>
                                                                @if(isset($request->primary_urgency))
                                                                <span class="urgency-badge urgency-{{ strtolower($request->primary_urgency) }}">
                                                                    {{ $request->urgency_data['priority_badge']['icon'] ?? '📋' }} {{ $request->primary_urgency }}
                                                                </span>
                                                                @endif
                                                                
                                                            </div>
                                                            
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                REQ
                                                            </span>
                                                                <div class="text-sm text-gray-500">   
                                                                    by {{ $request->userGudang->name }}
                                                                </div>
                                                                <div class="text-xs text-gray-400">
                                                                    {{ $request->tanggal_request->format('d M Y, H:i') }}
                                                                </div>

                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- Items Column -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->details->count() }} item{{ $request->details->count() > 1 ? 's' : '' }}</div>
                                                <div class="text-xs text-gray-500">
                                                    @php
                                                        $categories = $request->details->pluck('barang.kategori.nama_kategori')->unique()->take(2);
                                                        $urgentCount = $request->urgent_items_count ?? 0;
                                                        $highCount = $request->urgency_data['high_items_count'] ?? 0;
                                                    @endphp
                                                    @if($urgentCount > 0)
                                                        <span class="text-red-600 font-medium">{{ $urgentCount }} URGENT</span>
                                                    @endif
                                                    @if($highCount > 0)
                                                        <span class="text-orange-600 font-medium">{{ $highCount }} HIGH</span>
                                                    @endif
                                                    @if($categories->count() > 1)
                                                        | Mixed categories
                                                    @else
                                                        | {{ $categories->first() ?: 'Various items' }}
                                                    @endif
                                                </div>
                                            </td>
                                            
                                            <!-- Cost Column -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($request->total_estimasi_biaya, 0, ',', '.') }}</div>
                                                
                                            </td>
                                            
                                            <!-- Priority Column -->
                                            <td class="px-6 py-4">
                                                @php
                                                $urgency = $request->primary_urgency ?? 'NORMAL';
                                                $urgencyBadge = match($urgency) {
                                                    'URGENT' => [
                                                        'text' => 'URGENT',
                                                        'class' => 'bg-red-100 text-red-800',
                                                        'icon' => '🚨'
                                                    ],
                                                    'HIGH' => [
                                                        'text' => 'HIGH',
                                                        'class' => 'bg-orange-100 text-orange-800',
                                                        'icon' => '⚠️'
                                                    ],
                                                    'MEDIUM' => [
                                                        'text' => 'MEDIUM',
                                                        'class' => 'bg-yellow-100 text-yellow-800',
                                                        'icon' => '📋'
                                                    ],
                                                    'LOW' => [
                                                        'text' => 'LOW',
                                                        'class' => 'bg-blue-100 text-blue-800',
                                                        'icon' => '📅'
                                                    ],
                                                    default => [
                                                        'text' => 'NORMAL',
                                                        'class' => 'bg-green-100 text-green-800',
                                                        'icon' => '✅'
                                                    ]
                                                };
                                                
                                                $urgentItemsCount = $request->urgent_items_count ?? 0;
                                                $avgScore = round($request->priority_score ?? 0, 1);
                                            @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $urgencyBadge['class'] }}">
                                                    {{ $urgencyBadge['icon'] }} {{ $urgency }}
                                                </span>
                                                @if($urgentItemsCount > 0)
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        🔥 {{ $urgentItemsCount }} urgent items
                                                    </span>
                                                </div>
                                                @endif
                                                <div class="mt-1 text-xs text-gray-500">
                                                    Score: {{ $avgScore }}
                                                </div>
                                            </td>
                                            
                                            <!-- Actions Column -->
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <button onclick="quickApprove('{{ $request->id_request }}'); event.stopPropagation();" 
                                                            class="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors whitespace-nowrap">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Quick Approve
                                                    </button>
                                                    <button onclick="showEditModal('{{ $request->id_request }}'); event.stopPropagation();" 
                                                            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors whitespace-nowrap">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Review
                                                    </button>
                                                    <button onclick="rejectRequest('{{ $request->id_request }}'); event.stopPropagation();" 
                                                            class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors whitespace-nowrap">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Expandable Details Row - SIMPLIFIED -->
                                        <tr id="details-{{ $request->id_request }}" class="hidden bg-gray-50">
                                            <td colspan="5" class="px-6 py-4">
                                                <div class="bg-white rounded-lg p-4 border">
                                                    <h4 class="font-medium text-gray-900 mb-3">Request Details - {{ $request->nomor_request }}</h4>
                                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                        <!-- Request Info -->
                                                        <div>
                                                            <p class="text-sm text-gray-600 mb-2"><strong>Request Notes:</strong></p>
                                                            <p class="text-sm text-gray-800 bg-gray-50 p-2 rounded">{{ $request->catatan_request ?: 'No notes provided for this request.' }}</p>
                                                            
                                                            <div class="mt-4 space-y-1 text-sm">
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-600">Requested by:</span>
                                                                    <span class="font-medium">{{ $request->userGudang->name }}</span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-600">Date submitted:</span>
                                                                    <span class="font-medium">{{ $request->tanggal_request->format('d M Y, H:i') }}</span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-600">Total items:</span>
                                                                    <span class="font-medium">{{ $request->details->count() }} items</span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-gray-600">Estimated cost:</span>
                                                                    <span class="font-bold text-blue-600">Rp {{ number_format($request->total_estimasi_biaya, 0, ',', '.') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Items Preview -->
                                                        <div>
                                                            <p class="text-sm text-gray-600 mb-2"><strong>Items with AI Analysis:</strong></p>
                                                            <div class="space-y-3">
                                                                @foreach($request->details->take(3) as $detail)
                                                                    @php
                                                                        // Calculate urgency for this item
                                                                        $urgencyData = app('App\Services\UrgencyCalculationService')->calculateUrgencyLevel($detail->barang);
                                                                    @endphp
                                                                    <div class="p-3 bg-gray-50 rounded-lg border-l-4 {{ $urgencyData['final_urgency'] == 'URGENT' ? 'border-red-400' : ($urgencyData['final_urgency'] == 'HIGH' ? 'border-orange-400' : 'border-blue-400') }}">
                                                                        <div class="flex justify-between items-start mb-2">
                                                                            <span class="font-medium text-gray-900">{{ $detail->barang->nama_barang }}</span>
                                                                            <div class="flex items-center space-x-2">
                                                                                <span class="urgency-badge urgency-{{ strtolower($urgencyData['final_urgency']) }}">
                                                                                    {{ $urgencyData['priority_badge']['icon'] ?? '📋' }} {{ $urgencyData['final_urgency'] }}
                                                                                </span>
                                                                                <span class="text-sm text-gray-600">{{ $detail->qty_request }} {{ $detail->barang->satuan }}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-xs text-gray-600 bg-white p-2 rounded">
                                                                            <strong>{{ $urgencyData['auto_reason'] }}</strong> 
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                @if($request->details->count() > 3)
                                                                    <div class="text-sm text-gray-500 text-center py-2 border-t">
                                                                        ... and {{ $request->details->count() - 3 }} more items with demand analysis (click Review for complete details)
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <!-- Empty State -->
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2m16-7L9 8l4 4 7-7"></path>
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Pending Requests</h3>
                                                    <p class="text-sm text-gray-600">
                                                        All restock requests have been processed. New requests will appear here for review.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Simple Footer -->
                        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div>
                                    Showing {{ $pendingRequests->count() }} pending request{{ $pendingRequests->count() !== 1 ? 's' : '' }}
                                    • Total value: <span class="font-semibold">Rp {{ number_format($totalCost, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    // Simple toggle function
                    function toggleDetails(requestId) {
                        const detailsRow = document.getElementById('details-' + requestId);
                        const arrow = document.getElementById('arrow-' + requestId);
                        
                        if (detailsRow.classList.contains('hidden')) {
                            detailsRow.classList.remove('hidden');
                            arrow.style.transform = 'rotate(90deg)';
                        } else {
                            detailsRow.classList.add('hidden');
                            arrow.style.transform = 'rotate(0deg)';
                        }
                    }

                    // Action functions
                    function quickApprove(requestId) {
                        if (confirm('Are you sure you want to approve this request?')) {
                            // Add your approval logic here
                            console.log('Approving request:', requestId);
                        }
                    }

                    function showEditModal(requestId) {
                        // Add your review modal logic here
                        console.log('Opening review modal for:', requestId);
                    }

                    function rejectRequest(requestId) {
                        if (confirm('Are you sure you want to reject this request?')) {
                            // Add your rejection logic here
                            console.log('Rejecting request:', requestId);
                        }
                    }
                    </script>
                
                    
                

                <!-- Orders to Process Section (Replace Recent Request History) -->
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-gray-900">📦 Orders to Process</h3>
                                <span class="ml-3 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $approvedRequests->count() }} Ready to Order
                                </span>
                            </div>
                            <button onclick="refreshOrdersData()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if($approvedRequests->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($approvedRequests as $request)
                                <div class="p-6">
                                    <!-- Request Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $request->nomor_request }}</h4>
                                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                    <span>{{ $request->details->count() }} items</span>
                                                    <span>•</span>
                                                    <span>Approved {{ $request->tanggal_approved->diffForHumans() }}</span>
                                                    <span>•</span>
                                                    <span class="font-medium text-green-600">
                                                        Rp {{ number_format($request->details->sum('estimasi_harga')) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="toggleOrderDetails('{{ $request->id_request }}')" 
                                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View Items
                                            </button>
                                            <button onclick="markAsOrdered('{{ $request->id_request }}')" 
                                                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Mark as Ordered
                                            </button>
                                            <button onclick="exportOrderList('{{ $request->id_request }}')" 
                                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Export
                                            </button>
                                            <button onclick="forceTerminate('{{ $request->id_request }}')" 
                                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Cancel Order
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Collapsible Order Details -->
                                    <div id="order-details-{{ $request->id_request }}" class="hidden">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                <!-- Items List -->
                                                <div>
                                                    <h5 class="font-medium text-gray-900 mb-3">📋 Items to Order</h5>
                                                    <div class="space-y-2 max-h-60 overflow-y-auto">
                                                        @foreach($request->details->where('qty_approved', '>', 0) as $detail)
                                                            <div class="flex items-center justify-between p-3 bg-white rounded border 
                                                                {{ $detail->alasan_request == 'Additional item added by Owner during approval' ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                                                                <div class="flex items-center">
                                                                    @if($detail->alasan_request == 'Additional item added by Owner during approval')
                                                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                                                    @else
                                                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                                                    @endif
                                                                    <div>
                                                                        <p class="text-sm font-medium text-gray-900">{{ $detail->barang->nama_barang }}</p>
                                                                        <p class="text-xs text-gray-500">{{ $detail->barang->kode_barang }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="text-right">
                                                                    <p class="text-sm font-bold text-gray-900">{{ $detail->qty_approved }} {{ $detail->barang->satuan }}</p>
                                                                    <p class="text-xs text-gray-500">Rp {{ number_format($detail->estimasi_harga) }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Order Summary -->
                                                <div>
                                                    <h5 class="font-medium text-gray-900 mb-3">💰 Order Summary</h5>
                                                    <div class="space-y-3">
                                                        <div class="bg-white rounded border p-4">
                                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                                <div>
                                                                    <p class="text-gray-500">Total Items:</p>
                                                                    <p class="font-semibold">{{ $request->details->where('qty_approved', '>', 0)->count() }}</p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-gray-500">Original Items:</p>
                                                                    <p class="font-semibold">{{ $request->details->where('qty_approved', '>', 0)->where('alasan_request', '!=', 'Additional item added by Owner during approval')->count() }}</p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-gray-500">Owner Added:</p>
                                                                    <p class="font-semibold text-green-600">{{ $request->details->where('alasan_request', 'Additional item added by Owner during approval')->count() }}</p>
                                                                </div>
                                                                <div>
                                                                    <p class="text-gray-500">Total Budget:</p>
                                                                    <p class="font-semibold text-lg">Rp {{ number_format($request->details->sum('estimasi_harga')) }}</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Supplier Contact Suggestions -->
                                                        <div class="bg-blue-50 rounded border border-blue-200 p-4">
                                                            <h6 class="text-sm font-medium text-blue-900 mb-2">📞 Supplier Contact Tips</h6>
                                                            <ul class="text-xs text-blue-800 space-y-1">
                                                                <li>• Include request ID: {{ $request->nomor_request }}</li>
                                                                <li>• Mention urgent items first (if any)</li>
                                                                <li>• Ask for bulk discounts on large quantities</li>
                                                                <li>• Confirm delivery timeline</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4m0 0l-4-4m4 4v6"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">All Orders Processed</h3>
                            <p class="text-gray-600">No approved requests waiting for procurement action.</p>
                        </div>
                    @endif
                </div>

                <!-- Ordered Requests Section (Waiting for Warehouse) -->
                <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-gray-900">🚛 Ordered - Awaiting Delivery</h3>
                                <span class="ml-3 bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $orderedRequests->count() }} Waiting for Warehouse
                                </span>
                            </div>
                            <button onclick="refreshOrdersData()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    @if($orderedRequests->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($orderedRequests as $request)
                                <div class="p-6">
                                    <!-- Request Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4m0 0l-4-4m4 4v6"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">{{ $request->nomor_request }}</h4>
                                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                    <span>{{ $request->details->count() }} items</span>
                                                    <span>•</span>
                                                    <span>Ordered {{ $request->tanggal_ordered->diffForHumans() }}</span>
                                                    <span>•</span>
                                                    <span>By {{ $request->userOrdered->name ?? 'System' }}</span>
                                                    <span>•</span>
                                                    <span class="font-medium text-purple-600">
                                                        Rp {{ number_format($request->details->sum('estimasi_harga')) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                                📦 Awaiting Delivery
                                            </span>
                                            <button onclick="toggleOrderDetails('{{ $request->id_request }}')" 
                                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View Items
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Note for Warehouse -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <h5 class="text-sm font-medium text-blue-900">⚠️ Waiting for Warehouse Team</h5>
                                                <p class="text-sm text-blue-800 mt-1">
                                                    This order has been placed and is awaiting physical delivery. 
                                                    Warehouse team will complete this request when items are received.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Collapsible Order Details (same structure as approved requests) -->
                                    <div id="order-details-{{ $request->id_request }}" class="hidden">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                <!-- Items List -->
                                                <div>
                                                    <h5 class="font-medium text-gray-900 mb-3">📋 Ordered Items</h5>
                                                    <div class="space-y-2 max-h-60 overflow-y-auto">
                                                        @foreach($request->details->where('qty_approved', '>', 0) as $detail)
                                                            <div class="flex items-center justify-between p-3 bg-white rounded border">
                                                                <div class="flex items-center">
                                                                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                                                                    <div>
                                                                        <p class="text-sm font-medium text-gray-900">{{ $detail->barang->nama_barang }}</p>
                                                                        <p class="text-xs text-gray-500">{{ $detail->barang->kode_barang }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="text-right">
                                                                    <p class="text-sm font-bold text-gray-900">{{ $detail->qty_approved }} {{ $detail->barang->satuan }}</p>
                                                                    <p class="text-xs text-gray-500">Rp {{ number_format($detail->estimasi_harga) }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Order Summary -->
                                                <div>
                                                    <h5 class="font-medium text-gray-900 mb-3">📊 Order Summary</h5>
                                                    <div class="bg-white rounded border p-4">
                                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                                            <div>
                                                                <p class="text-gray-500">Status:</p>
                                                                <p class="font-semibold text-purple-600">📦 Ordered</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-500">Total Items:</p>
                                                                <p class="font-semibold">{{ $request->details->where('qty_approved', '>', 0)->count() }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-500">Ordered By:</p>
                                                                <p class="font-semibold">{{ $request->userOrdered->name ?? 'System' }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="text-gray-500">Total Cost:</p>
                                                                <p class="font-semibold text-lg">Rp {{ number_format($request->details->sum('estimasi_harga')) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Warehouse Instructions -->
                                                    <div class="bg-yellow-50 rounded border border-yellow-200 p-4 mt-4">
                                                        <h6 class="text-sm font-medium text-yellow-900 mb-2">📋 Next Steps</h6>
                                                        <ul class="text-xs text-yellow-800 space-y-1">
                                                            <li>• Warehouse will receive physical items</li>
                                                            <li>• Items will be processed via "Barang Masuk" form</li>
                                                            <li>• Stock will be automatically updated</li>
                                                            <li>• Request status will change to "Completed"</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8l-4 4m0 0l-4-4m4 4v6"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Orders Awaiting Delivery</h3>
                            <p class="text-gray-600">All orders have been completed by warehouse team.</p>
                        </div>
                    @endif
                </div>



            </main>
        </div>
    </div>

    <!-- Edit/Review Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-screen overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Review Restock Request</h3>
                        <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="px-6 py-4" id="modalContent">
                    <!-- Modal content will be loaded here via AJAX -->
                    <div class="text-center py-8">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <p class="text-gray-600">Loading request details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Search Modal - NEW ADDITION -->
    <div id="itemSearchModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Add Additional Items</h3>
                        <button onclick="closeItemSearchModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="px-6 py-4">
                    <!-- Search Input -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" id="itemSearchInput" placeholder="Search items by name or code..." 
                                   class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onkeyup="searchItems(this.value)">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults" class="space-y-2 max-h-96 overflow-y-auto">
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <p>Start typing to search for items...</p>
                        </div>
                    </div>
                </div>

                <!-- Selected Items Section -->
                <div id="selectedItemsSection" class="hidden px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Selected Items to Add</h4>
                    <div id="selectedItemsList" class="space-y-3 mb-4"></div>
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeItemSearchModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button onclick="addSelectedItems()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Add Selected Items
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        // CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Toggle expandable details
        function toggleDetails(requestId) {
            const detailsRow = document.getElementById(`details-${requestId}`);
            const arrow = document.getElementById(`arrow-${requestId}`);
            
            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
                arrow.style.transform = 'rotate(90deg)';
            } else {
                detailsRow.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        // Show edit modal with AJAX data loading
        function showEditModal(requestId) {
            document.getElementById('editModal').classList.remove('hidden');
            
            // Load request details via AJAX
            fetch(`/owner/restock-approval/${requestId}/details`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateModal(data);
                } else {
                    showMessage('error', 'Failed to load request details');
                    closeEditModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Failed to load request details');
                closeEditModal();
            });
        }

        // Populate modal with request data - FIXED VERSION
        function populateModal(data) {
            const request = data.request;
            const details = data.details;
            
            const modalContent = `
                <!-- Request Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Request Number</p>
                            <p class="text-lg font-bold text-gray-900">${request.nomor_request}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Requested by</p>
                            <p class="text-lg font-bold text-gray-900">${request.user_gudang}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Date</p>
                            <p class="text-lg font-bold text-gray-900">${new Date(request.tanggal_request).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'})}</p>
                        </div>
                    </div>
                </div>

                <!-- Items Table with Add Button -->
                <form id="approvalForm" data-request-id="${request.id}">
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-lg font-medium text-gray-900">Items to Restock</h4>
                            <button type="button" onclick="openItemSearchModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm inline-flex items-center" style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important;">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Additional Items
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 rounded-lg" id="itemsTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approve Qty</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Est. Cost</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reasoning</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" id="itemsTableBody">
                                    ${details.map(detail => `
                                        <tr data-detail-id="${detail.id}">
                                            <td class="px-4 py-3">
                                                <div>
                                                    <p class="font-medium text-gray-900">${detail.barang.nama}</p>
                                                    <p class="text-sm text-gray-500">${detail.barang.kode}</p>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm font-medium ${detail.barang.current_stock <= 0 ? 'text-red-600' : (detail.barang.current_stock <= 5 ? 'text-yellow-600' : 'text-green-600')}">${detail.barang.current_stock} ${detail.barang.satuan}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm font-medium">${detail.qty_request} ${detail.barang.satuan}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="details[${detail.id}][qty_approved]" value="${detail.qty_approved}" min="0" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="updateCost(this, ${detail.barang.harga_beli})">
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm font-medium cost-display">Rp ${new Intl.NumberFormat('id-ID').format(detail.estimasi_harga)}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm text-gray-500">Original</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-xs text-gray-600">
                                                    ${detail.auto_reason || detail.alasan_request || 'Standard restock'}
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Approval Notes (Optional)</label>
                        <textarea name="catatan_approval" rows="3" placeholder="Add any notes for this approval..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="text-sm text-gray-600">
                            <strong>Total Estimated Cost: Rp <span id="totalCost">${new Intl.NumberFormat('id-ID').format(data.totals.total_estimated)}</span></strong>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="button" onclick="rejectFromModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject
                            </button>
                           <button type="button" onclick="approveFromModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve Request
                            </button>
                        </div>
                    </div>
                </form>
            `;
            
            document.getElementById('modalContent').innerHTML = modalContent;
        }

        // Update cost when quantity changes
        function updateCost(input, hargaBeli) {
            const qty = parseInt(input.value) || 0;
            const cost = qty * hargaBeli;
            const costDisplay = input.closest('tr').querySelector('.cost-display');
            costDisplay.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(cost)}`;
            
            // Update total cost
            updateTotalCost();
        }

        // Update total cost
        function updateTotalCost() {
            const costDisplays = document.querySelectorAll('.cost-display');
            let total = 0;
            
            costDisplays.forEach(display => {
                const cost = parseInt(display.textContent.replace(/[^\d]/g, ''));
                total += cost;
            });
            
            document.getElementById('totalCost').textContent = new Intl.NumberFormat('id-ID').format(total);
        }

        // Close edit modal
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Quick approve with AJAX
        function quickApprove(requestId) {
            if (!confirm('Are you sure you want to quickly approve this request?')) {
                return;
            }
            
            fetch(`/owner/restock-approval/${requestId}/quick-approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', data.message);
                    removeRequestFromTable(requestId);
                    updatePendingCount(-1);
                } else {
                    showMessage('error', data.message || 'Failed to approve request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Failed to approve request');
            });
        }

        // Reject request
        function rejectRequest(requestId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (!reason) return;
            
            fetch(`/owner/restock-approval/${requestId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    catatan_approval: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('error', data.message);
                    removeRequestFromTable(requestId);
                    updatePendingCount(-1);
                } else {
                    showMessage('error', data.message || 'Failed to reject request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Failed to reject request');
            });
        }

       function approveFromModal() {
        const form = document.getElementById('approvalForm');
        const requestId = form.dataset.requestId;
        
        // ENHANCED: Better form data collection
        const data = {
            details: {},
            additional_items: [],
            catatan_approval: form.querySelector('[name="catatan_approval"]').value || null
        };
        
        // Collect existing item quantities
        const detailInputs = form.querySelectorAll('input[name^="details["]');
        detailInputs.forEach(input => {
            const match = input.name.match(/details\[(\d+)\]\[qty_approved\]/);
            if (match) {
                const detailId = match[1];
                data.details[detailId] = {
                    qty_approved: parseInt(input.value) || 0
                };
            }
        });
        
        // FIXED: Collect additional items - check for different input patterns
        console.log('🔍 Searching for additional items inputs...');
        
        // Method 1: Try name pattern additional_items[][]
        const additionalInputs1 = form.querySelectorAll('input[name^="additional_items["]');
        console.log('Method 1 found inputs:', additionalInputs1.length);
        
        // Method 2: Try data attributes or other patterns
        const additionalRows = form.querySelectorAll('tr[data-new-item="true"]');
        console.log('Method 2 found rows:', additionalRows.length);
        
        // Process additional items from rows
        additionalRows.forEach((row, index) => {
            const idBarangInput = row.querySelector('input[name*="id_barang"]');
            const qtyInput = row.querySelector('input[name*="qty_approved"]');
            
            console.log(`Row ${index}:`, {
                idBarangInput: idBarangInput?.value,
                qtyInput: qtyInput?.value,
                hasIdInput: !!idBarangInput,
                hasQtyInput: !!qtyInput
            });
            
            if (idBarangInput && qtyInput && idBarangInput.value && qtyInput.value) {
                const additionalItem = {
                    id_barang: idBarangInput.value,
                    qty_approved: parseInt(qtyInput.value) || 0
                };
                
                console.log('✅ Adding additional item:', additionalItem);
                data.additional_items.push(additionalItem);
            }
        });
        
        // Alternative method: Collect from selectedItems array if available
        if (typeof selectedItems !== 'undefined' && selectedItems.length > 0) {
            console.log('🔄 Using selectedItems array:', selectedItems);
            selectedItems.forEach(item => {
                if (item.id && item.qty_selected) {
                    data.additional_items.push({
                        id_barang: item.id,
                        qty_approved: item.qty_selected
                    });
                }
            });
        }
        
        console.log('📤 Final data being sent:', data);
        console.log('📋 Additional items count:', data.additional_items.length);
        
        // Send the request
        fetch(`/owner/restock-approval/${requestId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('📥 Server response:', data);
            
            if (data.success) {
                const additionalText = data.order_summary && data.order_summary.additional_items_count > 0 
                    ? ` (${data.order_summary.additional_items_count} additional items added)`
                    : '';
                
                showMessage('success', `✅ ${data.message}${additionalText}`);
                showMessage('info', '📦 Check "Orders to Process" section below to manage procurement.');
                
                closeEditModal();
                removeRequestFromTable(requestId);
                updatePendingCount(-1);
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showMessage('error', data.message || 'Failed to approve request');
            }
        })
        .catch(error => {
            console.error('❌ Request failed:', error);
            showMessage('error', 'Failed to approve request');
        });
    }

       



        
        // Reject from modal
        function rejectFromModal() {
            const form = document.getElementById('approvalForm');
            const requestId = form.dataset.requestId;
            const notes = form.querySelector('[name="catatan_approval"]').value;
            
            if (!notes.trim()) {
                alert('Please provide rejection notes');
                return;
            }
            
            fetch(`/owner/restock-approval/${requestId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    catatan_approval: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('error', data.message);
                    closeEditModal();
                    removeRequestFromTable(requestId);
                    updatePendingCount(-1);
                } else {
                    showMessage('error', data.message || 'Failed to reject request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Failed to reject request');
            });
        }

        // Remove request from table
        function removeRequestFromTable(requestId) {
            const row = document.querySelector(`tr[onclick="toggleDetails('${requestId}')"]`);
            const detailsRow = document.getElementById(`details-${requestId}`);
            
            if (row) row.remove();
            if (detailsRow) detailsRow.remove();
            
            // Check if table is empty
            const tbody = document.getElementById('requestsTable');
            if (tbody && tbody.children.length === 0) {
                location.reload(); // Reload to show empty state
            }
        }

        // Update pending count
        function updatePendingCount(change) {
            const pendingCount = document.getElementById('pendingCount');
            const statPending = document.getElementById('statPending');
            const currentCount = parseInt(pendingCount.textContent);
            const newCount = Math.max(0, currentCount + change);
            
            pendingCount.textContent = newCount;
            statPending.textContent = newCount;
        }

        // Show success/error messages
        function showMessage(type, message) {
            const container = document.getElementById('messageContainer');
            const messageDiv = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? 
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';
            
            messageDiv.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg mb-4 max-w-md`;
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    ${icon}
                    <span class="ml-3">${message}</span>
                </div>
            `;
            
            container.appendChild(messageDiv);
            
            // Remove message after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }

        // Refresh data
        function refreshData() {
            const btn = document.getElementById('refreshBtn');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Refreshing...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                location.reload();
            }, 1000);
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Owner Restock Approval page loaded');
        });

    
    // Global variables for search functionality
        let currentRequestId = null;
        let selectedItems = [];
        let searchTimeout = null;

        // Open item search modal
        function openItemSearchModal() {
            selectedItems = [];
            document.getElementById('itemSearchModal').classList.remove('hidden');
            document.getElementById('itemSearchInput').value = '';
            resetSearchResults();
            updateSelectedItemsDisplay();
        }

        // Close item search modal
        function closeItemSearchModal() {
            document.getElementById('itemSearchModal').classList.add('hidden');
            selectedItems = [];
        }

        // Reset search results to initial state
        function resetSearchResults() {
            document.getElementById('searchResults').innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p>Start typing to search for items...</p>
                </div>
            `;
        }

        // Search items with debounce - FIXED VERSION
        function searchItems(query) {
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                resetSearchResults();
                return;
            }

            searchTimeout = setTimeout(() => {
                // Show loading
                document.getElementById('searchResults').innerHTML = `
                    <div class="text-center py-4">
                        <svg class="w-6 h-6 mx-auto mb-2 animate-spin text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <p class="text-gray-500">Searching...</p>
                    </div>
                `;

                // Get current request ID from modal
                const form = document.getElementById('approvalForm');
                const requestId = form ? form.dataset.requestId : null;

                // FIXED: Use correct route path
                fetch(`/owner/restock-approval/search/items?q=${encodeURIComponent(query)}&request_id=${requestId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Search response:', data); // Debug log
                    if (data.success) {
                        displaySearchResults(data.items);
                    } else {
                        showMessage('error', data.message || 'Failed to search items');
                        resetSearchResults();
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    showMessage('error', 'Failed to search items: ' + error.message);
                    resetSearchResults();
                });
            }, 300);
        }

        // Display search results
        function displaySearchResults(items) {
            const resultsContainer = document.getElementById('searchResults');
            
            if (items.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>No items found.</p>
                    </div>
                `;
                return;
            }

            const resultsHTML = items.map(item => `
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">${item.nama}</p>
                                <p class="text-sm text-gray-500">${item.kode}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium ${item.current_stock <= 0 ? 'text-red-600' : (item.current_stock <= 5 ? 'text-yellow-600' : 'text-green-600')}">
                                    Stock: ${item.current_stock} ${item.satuan}
                                </p>
                                <p class="text-xs text-gray-500">Rp ${new Intl.NumberFormat('id-ID').format(item.harga_beli)}/${item.satuan}</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="selectItem(${JSON.stringify(item).replace(/"/g, '&quot;')})" class="ml-4 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                        Select
                    </button>
                </div>
            `).join('');

            resultsContainer.innerHTML = resultsHTML;
        }

        // Select item for addition
        function selectItem(item) {
            // Check if item already selected
            if (selectedItems.find(selected => selected.id === item.id)) {
                showMessage('error', 'Item already selected');
                return;
            }

            selectedItems.push({
                ...item,
                qty_selected: 1 // Default quantity
            });

            updateSelectedItemsDisplay();
            showMessage('success', `${item.nama} added to selection`);
        }

        // Update selected items display
        function updateSelectedItemsDisplay() {
            const section = document.getElementById('selectedItemsSection');
            const list = document.getElementById('selectedItemsList');

            if (selectedItems.length === 0) {
                section.classList.add('hidden');
                return;
            }

            section.classList.remove('hidden');

            const itemsHTML = selectedItems.map((item, index) => `
                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">${item.nama}</p>
                        <p class="text-sm text-gray-500">${item.kode} - Rp ${new Intl.NumberFormat('id-ID').format(item.harga_beli)}/${item.satuan}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600">Qty:</label>
                            <input type="number" value="${item.qty_selected}" min="1" max="999" 
                                   class="w-16 px-2 py-1 border border-gray-300 rounded text-sm"
                                   onchange="updateSelectedItemQty(${index}, this.value)">
                        </div>
                        <button onclick="removeSelectedItem(${index})" class="text-red-600 hover:text-red-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `).join('');

            list.innerHTML = itemsHTML;
        }

        // Update selected item quantity
        function updateSelectedItemQty(index, qty) {
            if (selectedItems[index]) {
                selectedItems[index].qty_selected = parseInt(qty) || 1;
            }
        }

        // Remove selected item
        function removeSelectedItem(index) {
            selectedItems.splice(index, 1);
            updateSelectedItemsDisplay();
        }

        function addSelectedItems() {
            if (selectedItems.length === 0) {
                showMessage('error', 'No items selected');
                return;
            }

            const tbody = document.getElementById('itemsTableBody');
            
            selectedItems.forEach((item, index) => {
                const newRow = document.createElement('tr');
                newRow.classList.add('bg-green-50');
                newRow.setAttribute('data-new-item', 'true');
                
                const estimatedCost = item.harga_beli * item.qty_selected;
                
                newRow.innerHTML = `
                    <td class="px-4 py-3">
                        <div>
                            <p class="font-medium text-gray-900">${item.nama}</p>
                            <p class="text-sm text-gray-500">${item.kode}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Item
                            </span>
                        </div>
                        <!-- FIXED: Consistent input naming -->
                        <input type="hidden" name="additional_items[${index}][id_barang]" value="${item.id}">
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm font-medium ${item.current_stock <= 0 ? 'text-red-600' : (item.current_stock <= 5 ? 'text-yellow-600' : 'text-green-600')}">${item.current_stock} ${item.satuan}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm font-medium text-green-600">Owner Added</span>
                    </td>
                    <td class="px-4 py-3">
                        <input type="number" name="additional_items[${index}][qty_approved]" value="${item.qty_selected}" min="1" 
                            class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            onchange="updateCost(this, ${item.harga_beli})">
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm font-medium cost-display">Rp ${new Intl.NumberFormat('id-ID').format(estimatedCost)}</span>
                    </td>
                    <td class="px-4 py-3">
                        <button type="button" onclick="removeNewItem(this)" class="text-red-600 hover:text-red-800 text-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove
                        </button>
                    </td>
                `;
                
                tbody.appendChild(newRow);
            });

            updateTotalCost();
            closeItemSearchModal();
            showMessage('success', `Added ${selectedItems.length} additional items to the request`);
            
            // Clear selectedItems after adding
            selectedItems = [];
        }

        // Remove newly added item
        function removeNewItem(button) {
            const row = button.closest('tr');
            row.remove();
            updateTotalCost();
            showMessage('success', 'Item removed from request');
        }

        // Close search modal when clicking outside
        document.getElementById('itemSearchModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeItemSearchModal();
            }
        });

        // Update keyboard shortcuts to handle both modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
                closeItemSearchModal();
            }
        });
        



        // Toggle order details visibility
        function toggleOrderDetails(requestId) {
            const detailsDiv = document.getElementById(`order-details-${requestId}`);
            const isHidden = detailsDiv.classList.contains('hidden');
            
            if (isHidden) {
                detailsDiv.classList.remove('hidden');
                detailsDiv.style.maxHeight = detailsDiv.scrollHeight + 'px';
            } else {
                detailsDiv.style.maxHeight = '0px';
                setTimeout(() => {
                    detailsDiv.classList.add('hidden');
                }, 300);
            }
        }

        // Mark request as ordered (update status)
        function markAsOrdered(requestId) {
            if (!confirm('Mark this request as ordered? This will move it to Awaiting Delivery.')) {
                return;
            }
            
            fetch(`/owner/restock-approval/${requestId}/mark-ordered`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('success', `Request ${data.request_number} marked as ordered!`);
                    // Remove from orders to process list
                    const requestDiv = document.querySelector(`[onclick*="${requestId}"]`).closest('.p-6');
                    requestDiv.style.opacity = '0';
                    setTimeout(() => {
                        requestDiv.remove();
                        
                        // Check if no more orders
                        const ordersContainer = document.querySelector('.divide-y.divide-gray-200');
                        if (ordersContainer && ordersContainer.children.length === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    }, 300);
                } else {
                    showMessage('error', data.message || 'Failed to update order status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Failed to update order status');
            });
        }

        // Export order list for supplier communication
        function exportOrderList(requestId) {
            // Open print-friendly view in new window
            const printWindow = window.open(`/owner/restock-approval/${requestId}/export-order-list`, '_blank');
            if (printWindow) {
                printWindow.onload = function() {
                    printWindow.print();
                };
            } else {
                // Fallback: download as file
                fetch(`/owner/restock-approval/${requestId}/export-order-list?format=csv`)
                    .then(response => response.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `order-list-${requestId}.csv`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    })
                    .catch(error => {
                        console.error('Export failed:', error);
                        showMessage('error', 'Failed to export order list');
                    });
            }
        }

        // Refresh orders data
        function refreshOrdersData() {
            showMessage('info', 'Refreshing orders data...');
            setTimeout(() => {
                location.reload();
            }, 500);
        }

        // Remove order summary modal and just show simple success message
        function approveFromModal() {
            const form = document.getElementById('approvalForm');
            const formData = new FormData(form);
            const requestId = form.dataset.requestId;
            
            // Convert FormData to JSON (same as before)
            const data = {};
            data.details = {};
            data.additional_items = [];
            data.catatan_approval = formData.get('catatan_approval');
            
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('details[')) {
                    const match = key.match(/details\[(\d+)\]\[qty_approved\]/);
                    if (match) {
                        data.details[match[1]] = { qty_approved: parseInt(value) };
                    }
                }
                else if (key.startsWith('additional_items[')) {
                    const match = key.match(/additional_items\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const field = match[2];
                        
                        if (!data.additional_items[index]) {
                            data.additional_items[index] = {};
                        }
                        data.additional_items[index][field] = field === 'qty_approved' ? parseInt(value) : value;
                    }
                }
            }
            
            data.additional_items = data.additional_items.filter(item => item && item.id_barang && item.qty_approved);
            
            fetch(`/owner/restock-approval/${requestId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message with order info
                    const additionalText = data.order_summary && data.order_summary.additional_items_count > 0 
                        ? ` (${data.order_summary.additional_items_count} additional items added)`
                        : '';
                    
                    showMessage('success', `✅ ${data.message}${additionalText}`);
                    showMessage('info', '📦 Check "Orders to Process" section below to manage procurement.');
                    
                    closeEditModal();
                    removeRequestFromTable(requestId);
                    updatePendingCount(-1);
                    
                    // Refresh page after 2 seconds to show new order in "Orders to Process"
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage('error', data.message || 'Failed to approve request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Failed to approve request');
            });
        }


        function forceTerminate(requestId) {
            const reason = prompt('Reason for force termination (required):');
            if (!reason || reason.length < 10) {
                alert('Please provide a detailed reason (min 10 characters)');
                return;
            }
            
            if (!confirm('Are you sure you want to FORCE TERMINATE this request? This action cannot be undone.')) {
                return;
            }
            
            fetch(`/owner/restock-approval/${requestId}/force-terminate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    termination_reason: reason,
                    confirm_termination: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('error', data.message);
                    if (data.redirect_needed) {
                        setTimeout(() => location.reload(), 2000);
                    }
                } else {
                    showMessage('error', data.message || 'Failed to terminate request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'Failed to terminate request');
            });
        }
        



    </script>
</body>
</html>