<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Business Reports</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        /* Main Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .refresh-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .period-select {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
        }

        .refresh-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Financial Cards */
        .financial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .financial-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 5px solid;
            transition: transform 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .financial-card:hover {
            transform: translateY(-2px);
        }

        .financial-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            background: var(--card-color);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .financial-card.revenue {
            border-left-color: #3b82f6;
            --card-color: #3b82f6;
        }

        .financial-card.profit {
            border-left-color: #10b981;
            --card-color: #10b981;
        }

        .financial-card.expense {
            border-left-color: #f59e0b;
            --card-color: #f59e0b;
        }

        .financial-card.growth {
            border-left-color: #8b5cf6;
            --card-color: #8b5cf6;
        }

        .financial-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .financial-icon {
            font-size: 2.5rem;
            margin-right: 15px;
            opacity: 0.8;
        }

        .financial-title {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }

        .financial-value {
            font-size: 2.2rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .financial-change {
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .financial-change.positive {
            color: #10b981;
        }

        .financial-change.negative {
            color: #ef4444;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Insights Section */
        .insights-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .insight-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .insight-header {
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .insight-title {
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
        }

        .insight-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .insight-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s ease;
        }

        .insight-item:hover {
            background: #f9fafb;
        }

        .insight-item:last-child {
            border-bottom: none;
        }

        .item-info h4 {
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .item-info span {
            color: #6b7280;
            font-size: 0.85rem;
        }

        .item-value {
            text-align: right;
        }

        .item-value .primary {
            font-weight: 600;
            color: #1f2937;
            display: block;
        }

        .item-value .secondary {
            color: #6b7280;
            font-size: 0.85rem;
        }

        /* Export Section */
        .export-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            text-align: center;
        }

        .export-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .export-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .export-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .export-btn.secondary {
            background: #10b981;
        }

        .export-btn.secondary:hover {
            background: #059669;
        }

        /* Loading States */
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: #6b7280;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .status-low {
            background: #dcfce7;
            color: #166534;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .financial-grid {
                grid-template-columns: 1fr;
            }
            
            .charts-section {
                grid-template-columns: 1fr;
            }
            
            .insights-section {
                grid-template-columns: 1fr;
            }
            
            .export-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex">
        <!-- Sidebar (Same as other pages) -->
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
                    <a href="#" class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v1M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Kelola Data User
                    </a>

                    <!-- Restock Approval -->
                    <a href="{{ route('owner.restock-approval.index') }}" class="flex items-center px-3 py-2 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Restock Approval
                        @php
                            $pendingCount = \App\Models\RestockRequest::where('status_request', 'Pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>

                    <!-- Laporan - ACTIVE STATE -->
                    <a href="{{ route('owner.reports.index') }}" class="flex items-center px-3 py-2 text-white bg-blue-600 rounded-lg transition-colors duration-200 mb-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Laporan
                        <span class="ml-auto bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Active</span>
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
            <!-- Main Content -->
            <div class="container">
                <!-- Header -->
                <div class="header">
                    <div>
                        <h1>🎯 Business Reports</h1>
                        <p>Dashboard analytics untuk monitoring performa bisnis</p>
                    </div>
                    <div class="refresh-section">
                        <select id="periodSelect" class="period-select">
                            <option value="current_month">Bulan Ini</option>
                            <option value="last_month">Bulan Lalu</option>
                            <option value="this_year">Tahun Ini</option>
                        </select>
                        <button class="refresh-btn" onclick="refreshDashboard()">
                            🔄 Refresh Data
                        </button>
                    </div>
                </div>

                <!-- Financial Cards -->
                <div class="financial-grid" id="financialCards">
                    <!-- Loading state -->
                    <div class="loading" style="grid-column: 1 / -1;">
                        <div class="spinner"></div>
                        Loading financial data...
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="charts-section">
                    <!-- Revenue Trend -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">📈 Revenue Trend (6 Bulan)</div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- Top Categories -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <div class="chart-title">🥧 Top Categories</div>
                        </div>
                        <div class="chart-container">
                            <canvas id="topCategoriesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Insights Section -->
                <div class="insights-section">
                    <!-- Top Selling Items -->
                    <div class="insight-card">
                        <div class="insight-header">
                            <div class="insight-title">🏆 Top Selling Items</div>
                        </div>
                        <div class="insight-list" id="topItemsList">
                            <div class="loading">
                                <div class="spinner"></div>
                                Loading top items...
                            </div>
                        </div>
                    </div>

                    <!-- Slow Moving Items -->
                    <div class="insight-card">
                        <div class="insight-header">
                            <div class="insight-title">🐌 Slow Moving Items</div>
                        </div>
                        <div class="insight-list" id="slowMovingList">
                            <div class="loading">
                                <div class="spinner"></div>
                                Loading slow moving items...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Section -->
                <div class="export-section">
                    <div class="export-title">📤 Export Reports</div>
                    <div class="export-buttons">
                        <button class="export-btn" onclick="exportPdfSummary()">
                            📄 Export PDF Summary
                        </button>
                        <button class="export-btn secondary" onclick="exportExcelDetail()">
                            📊 Export Excel Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration - UPDATED API ROUTES
        const API_ROUTES = {
            dashboardData: '{{ route("owner.reports.owner-dashboard") }}',
            exportPdf: '{{ route("owner.reports.export-pdf-summary") }}',
            exportExcel: '{{ route("owner.reports.export-excel-detail") }}'
        };

        let charts = {};
        let currentPeriod = 'current_month';

        // Initialize Dashboard
        $(document).ready(function() {
            setupEventHandlers();
            loadDashboard();
        });

        function setupEventHandlers() {
            $('#periodSelect').on('change', function() {
                currentPeriod = $(this).val();
                loadDashboard();
            });
        }

        function refreshDashboard() {
            loadDashboard();
        }

        async function loadDashboard() {
            try {
                showLoading();
                
                const response = await fetch(`${API_ROUTES.dashboardData}?period=${currentPeriod}`);
                const result = await response.json();
                
                if (result.success) {
                    alert(`📄 PDF Summary Export Ready!

File: ${result.file_info.filename}
Size: ${result.file_info.size}
Pages: ${result.file_info.pages}

${result.export_note}`);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                alert('Export failed: ' + error.message);
            }
        }

        async function exportExcelDetail() {
            try {
                const response = await fetch(`${API_ROUTES.exportExcel}?period=${currentPeriod}`);
                const result = await response.json();
                
                if (result.success) {
                    alert(`📊 Excel Detail Export Ready!

File: ${result.file_info.filename}
Records: ${result.file_info.records}
Size: ${result.file_info.size}

Total Revenue: Rp ${result.export_summary.total_revenue.toLocaleString()}
Total Profit: Rp ${result.export_summary.total_profit.toLocaleString()}

${result.export_note}`);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                alert('Export failed: ' + error.message);
            }
        }
    </script>
</body>
</html>
        