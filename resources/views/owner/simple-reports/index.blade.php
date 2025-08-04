<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Laporan Bengkel</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

                    <!-- Laporan - ACTIVE -->
                    <a href="{{ route('owner.simple-reports.index') }}" class="flex items-center px-3 py-2 text-white bg-slate-700 rounded-lg transition-colors duration-200 mb-1">
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
            <!-- Top Navigation - FIXED LAYOUT -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <!-- Header Content Container -->
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Left Side: Title & Description -->
                        <div class="flex-1">
                            <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                                📊 Laporan Bengkel
                            </h1>
                            <p class="text-gray-600 text-sm mt-1">
                                Laporan simple untuk kebutuhan operasional harian
                            </p>
                            <!-- Date Display -->
                            <div class="text-sm text-gray-500 mt-2">
                                <div id="current-date"></div>
                            </div>
                        </div>

                        <!-- Right Side: Action Buttons -->
                        <div class="flex items-center gap-3">
                            <!-- Date Picker -->
                            <div class="flex items-center gap-2">
                                <label for="report-date" class="text-sm text-gray-600 whitespace-nowrap">Tanggal:</label>
                                <input 
                                    type="date" 
                                    id="report-date" 
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                    value="{{ date('Y-m-d') }}"
                                >
                            </div>

                            <!-- Refresh Button -->
                            <button 
                                id="refreshBtn" 
                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2"
                                title="Refresh Data"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="hidden sm:inline">Refresh</span>
                            </button>

                            <!-- Export PDF Button -->
                            <button 
                                id="quickExportBtn" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2 font-medium"
                                style="min-width: 140px; cursor: pointer;"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export PDF
                            </button>
                            
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <!-- Quick Report Cards - IMPROVED GRID -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Laporan Harian -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all duration-300 cursor-pointer group" onclick="loadDailyReport()">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors duration-300">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-300">Laporan Harian</h3>
                                <p class="text-gray-600 text-sm">Transaksi & stok hari ini</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div id="daily-summary" class="text-xs text-gray-500">
                                Loading...
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Belanja -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-lg hover:border-green-300 transition-all duration-300 cursor-pointer group" onclick="loadRestockReport()">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors duration-300">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h7M12 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-green-700 transition-colors duration-300">Laporan Belanja</h3>
                                <p class="text-gray-600 text-sm">Daftar barang harus beli</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div id="restock-summary" class="text-xs text-gray-500">
                                Loading...
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Stok -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-lg hover:border-yellow-300 transition-all duration-300 cursor-pointer group" onclick="loadStockReport()">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 transition-colors duration-300">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-yellow-700 transition-colors duration-300">Laporan Stok</h3>
                                <p class="text-gray-600 text-sm">Inventory overview</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div id="stock-summary" class="text-xs text-gray-500">
                                Loading...
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Bulanan -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 hover:shadow-lg hover:border-purple-300 transition-all duration-300 cursor-pointer group" onclick="loadMonthlyReport()">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors duration-300">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-300">Laporan Bulanan</h3>
                                <p class="text-gray-600 text-sm">Omzet vs pengeluaran</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div id="monthly-summary" class="text-xs text-gray-500">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Report Content - IMPROVED STYLING -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Report Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 id="report-title" class="text-lg font-semibold text-gray-900">
                                    Pilih laporan di atas untuk melihat detail
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Klik salah satu kartu untuk memuat data laporan
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Report Content Area -->
                    <div id="report-content" class="p-6">
                        <!-- Welcome State -->
                        <div class="text-center text-gray-500 py-16">
                            <div class="max-w-md mx-auto">
                                <svg class="w-16 h-16 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    Selamat datang di Laporan Bengkel
                                </h3>
                                <p class="text-gray-600">
                                    Klik salah satu kartu laporan di atas untuk mulai melihat data dan analisis
                                </p>
                                <div class="flex justify-center mt-6 space-x-2">
                                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                    <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- CLEANED UP JAVASCRIPT -->
    <script>
        // Global Variables
        let currentReportType = 'daily';

        // === INITIALIZATION ===
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing Laporan Bengkel...');
            
            updateDate();
            initializeEventListeners();
            loadSummaries();
            
            console.log('Initialization complete!');
        });

        // === EVENT LISTENERS ===
        function initializeEventListeners() {

            // Quick Export PDF
            const pdfBtn = document.getElementById('quickExportBtn');
            pdfBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                exportCurrentReportPdf();
            });

            // Refresh Button
            const refreshBtn = document.getElementById('refreshBtn');
            refreshBtn.addEventListener('click', function() {
                loadSummaries();
                refreshCurrentReport();
            });

            // Date Change
            const dateInput = document.getElementById('report-date');
            dateInput.addEventListener('change', function() {
                loadSummaries();
                refreshCurrentReport();
            });

        }


        function exportCurrentReportPdf() {
            const button = document.getElementById('quickExportBtn');
            const date = document.getElementById('report-date').value;
            const month = date.substring(0, 7);
            
            // Show loading state
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generating PDF...
            `;
            
            // Build URL based on current report type
            let url, filename;
            
            switch(currentReportType) {
                case 'daily':
                    url = `{{ route('owner.simple-reports.export-pdf-summary') }}?date=${date}&type=daily`;
                    filename = `laporan-harian-${date}.pdf`;
                    break;
                case 'restock':
                    url = `{{ route('owner.simple-reports.export-pdf-summary') }}?date=${date}&type=restock`;
                    filename = `laporan-belanja-${date}.pdf`;
                    break;
                case 'stock':
                    url = `{{ route('owner.simple-reports.export-pdf-summary') }}?date=${date}&type=stock`;
                    filename = `laporan-stok-${date}.pdf`;
                    break;
                case 'monthly':
                    url = `{{ route('owner.simple-reports.export-pdf-summary') }}?month=${month}&type=monthly`;
                    filename = `laporan-bulanan-${month}.pdf`;
                    break;
                default:
                    url = `{{ route('owner.simple-reports.export-pdf-summary') }}?date=${date}&type=daily`;
                    filename = `laporan-summary-${date}.pdf`;
            }
            
            console.log('Exporting PDF:', currentReportType, url);
            
            // Create and trigger download
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showNotification(`PDF ${currentReportType} berhasil diunduh!`, 'success');
            
            // Reset button
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 2000);
        }

        // === UTILITY FUNCTIONS ===
        function updateDate() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric'
            };
            const dateElement = document.getElementById('current-date');
            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('id-ID', options);
            }
        }

        function formatNumber(num) {
            if (!num) return '0';
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotifications = document.querySelectorAll('.export-notification');
            existingNotifications.forEach(notification => notification.remove());
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `export-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-xl max-w-sm transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                type === 'warning' ? 'bg-yellow-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2 text-lg">${icon}</span>
                    <span class="font-medium">${message}</span>
                </div>
            `;
            
            // Add to page with animation
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            // Auto remove
            const delay = type === 'error' ? 5000 : 3000;
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }
            }, delay);
        }

        function updatePdfButton(reportType) {
            const pdfButton = document.getElementById('quickExportBtn');
            const iconSvg = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            `;
            
            currentReportType = reportType;
            
            const buttonConfigs = {
                'daily': { text: 'Export PDF Harian', color: 'bg-blue-600 hover:bg-blue-700' },
                'restock': { text: 'Export PDF Belanja', color: 'bg-emerald-600 hover:bg-emerald-700' },
                'stock': { text: 'Export PDF Stok', color: 'bg-yellow-600 hover:bg-yellow-700' },
                'monthly': { text: 'Export PDF Bulanan', color: 'bg-purple-600 hover:bg-purple-700' }
            };
            
            const config = buttonConfigs[reportType] || buttonConfigs['daily'];
            
            pdfButton.innerHTML = `${iconSvg} ${config.text}`;
            pdfButton.className = `px-4 py-2 ${config.color} text-white rounded-lg transition-colors duration-200 flex items-center gap-2`;
        }

        function refreshCurrentReport() {
            const currentTitle = document.getElementById('report-title').textContent;
            
            if (currentTitle.includes('Harian')) {
                loadDailyReport();
            } else if (currentTitle.includes('Belanja')) {
                loadRestockReport();
            } else if (currentTitle.includes('Stok')) {
                loadStockReport();
            } else if (currentTitle.includes('Bulanan')) {
                loadMonthlyReport();
            }
        }

        // === DATA LOADING FUNCTIONS ===
        async function loadSummaries() {
            const date = document.getElementById('report-date').value;
            
            try {
                // Load daily summary
                const dailyResponse = await fetch(`{{ route('owner.simple-reports.daily') }}?date=${date}`);
                if (dailyResponse.ok) {
                    const dailyData = await dailyResponse.json();
                    document.getElementById('daily-summary').innerHTML = `
                        <div class="text-blue-600 font-semibold">${dailyData.summary.total_transaksi} transaksi</div>
                        <div class="text-gray-600">Rp ${formatNumber(dailyData.summary.total_omzet)}</div>
                    `;
                }

                // Load restock summary
                const restockResponse = await fetch(`{{ route('owner.simple-reports.restock') }}`);
                if (restockResponse.ok) {
                    const restockData = await restockResponse.json();
                    document.getElementById('restock-summary').innerHTML = `
                        <div class="text-green-600 font-semibold">${restockData.barang_harus_beli.length} items</div>
                        <div class="text-gray-600">Budget: Rp ${formatNumber(restockData.estimasi_budget)}</div>
                    `;
                }

                // Load stock summary
                const stockResponse = await fetch(`{{ route('owner.simple-reports.stock') }}`);
                if (stockResponse.ok) {
                    const stockData = await stockResponse.json();
                    document.getElementById('stock-summary').innerHTML = `
                        <div class="text-yellow-600 font-semibold">${stockData.summary.total_items} items</div>
                        <div class="text-gray-600">Value: Rp ${formatNumber(stockData.summary.total_value)}</div>
                    `;
                }

                // Load monthly summary
                const monthlyResponse = await fetch(`{{ route('owner.simple-reports.monthly') }}?month=${date.substring(0, 7)}`);
                if (monthlyResponse.ok) {
                    const monthlyData = await monthlyResponse.json();
                    document.getElementById('monthly-summary').innerHTML = `
                        <div class="text-purple-600 font-semibold">Rp ${formatNumber(monthlyData.summary.omzet)}</div>
                        <div class="text-gray-600">Profit: Rp ${formatNumber(monthlyData.summary.profit_estimation)}</div>
                    `;
                }

            } catch (error) {
                console.error('Error loading summaries:', error);
                showNotification('Error loading summary data', 'error');
            }
        }

        // === REPORT LOADING FUNCTIONS ===
        async function loadDailyReport() {
            const date = document.getElementById('report-date').value;
            document.getElementById('report-title').textContent = '📅 Laporan Harian - ' + date;
            document.getElementById('report-content').innerHTML = '<div class="text-center py-8">Loading...</div>';
            
            updatePdfButton('daily');

            try {
                const response = await fetch(`{{ route('owner.simple-reports.daily') }}?date=${date}`);
                const data = await response.json();
                
                let html = `
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-blue-600 text-sm font-medium">Total Transaksi</div>
                            <div class="text-2xl font-bold text-blue-900">${data.summary.total_transaksi}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-green-600 text-sm font-medium">Transaksi Selesai</div>
                            <div class="text-2xl font-bold text-green-900">${data.summary.transaksi_selesai}</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-yellow-600 text-sm font-medium">Total Omzet</div>
                            <div class="text-2xl font-bold text-yellow-900">Rp ${formatNumber(data.summary.total_omzet)}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-purple-600 text-sm font-medium">Rata-rata</div>
                            <div class="text-2xl font-bold text-purple-900">Rp ${formatNumber(data.summary.rata_rata_transaksi)}</div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-3">🧾 Transaksi Hari Ini</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left">No. Transaksi</th>
                                        <th class="px-4 py-2 text-left">Customer</th>
                                        <th class="px-4 py-2 text-left">Kasir</th>
                                        <th class="px-4 py-2 text-left">Status</th>
                                        <th class="px-4 py-2 text-left">Total</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                data.transactions.forEach(trx => {
                    const statusColor = trx.status_transaksi === 'Selesai' ? 'green' : 'yellow';
                    html += `
                        <tr class="border-b">
                            <td class="px-4 py-2 font-medium">${trx.nomor_transaksi}</td>
                            <td class="px-4 py-2">${trx.nama_customer || '-'}</td>
                            <td class="px-4 py-2">${trx.kasir?.name || '-'}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-${statusColor}-100 text-${statusColor}-800">
                                    ${trx.status_transaksi}
                                </span>
                            </td>
                            <td class="px-4 py-2 font-medium">Rp ${formatNumber(trx.total_harga)}</td>
                        </tr>`;
                });

                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Stock Alerts -->
                    <div>
                        <h4 class="text-lg font-semibold mb-3">⚠️ Stok Perlu Perhatian</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">`;

                data.stok_perlu_perhatian.forEach(item => {
                    const statusColor = item.stok?.status_stok === 'Habis' ? 'red' : 'yellow';
                    html += `
                        <div class="bg-${statusColor}-50 border border-${statusColor}-200 p-4 rounded-lg">
                            <div class="font-medium text-${statusColor}-800">${item.nama_barang}</div>
                            <div class="text-sm text-${statusColor}-600">
                                Stok: ${item.stok?.jumlah_stok || 0} | ROP: ${item.reorder_point}
                            </div>
                        </div>`;
                });

                html += '</div></div>';
                document.getElementById('report-content').innerHTML = html;

            } catch (error) {
                console.error('Error loading daily report:', error);
                document.getElementById('report-content').innerHTML = '<div class="text-red-500 text-center py-8">Error loading report</div>';
            }
        }

        async function loadRestockReport() {
            document.getElementById('report-title').textContent = '🛒 Laporan Belanja';
            document.getElementById('report-content').innerHTML = '<div class="text-center py-8">Loading...</div>';
            
            updatePdfButton('restock');

            try {
                const response = await fetch(`{{ route('owner.simple-reports.restock') }}`);
                const data = await response.json();
                
                let html = `
                    <!-- Budget Summary -->
                    <div class="bg-green-50 p-6 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-green-800 mb-2">💰 Estimasi Budget Dibutuhkan</h4>
                        <div class="text-3xl font-bold text-green-900">Rp ${formatNumber(data.estimasi_budget)}</div>
                        <div class="text-sm text-green-600 mt-1">${data.barang_harus_beli.length} item perlu dibeli</div>
                    </div>

                    <!-- Shopping List -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold mb-3">📝 Daftar Belanja</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Kode</th>
                                        <th class="px-4 py-2 text-left">Nama Barang</th>
                                        <th class="px-4 py-2 text-left">Kategori</th>
                                        <th class="px-4 py-2 text-left">Stok Saat Ini</th>
                                        <th class="px-4 py-2 text-left">ROP</th>
                                        <th class="px-4 py-2 text-left">Saran Beli</th>
                                        <th class="px-4 py-2 text-left">Estimasi Harga</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                data.barang_harus_beli.forEach(item => {
                    const saranBeli = item.eoq_qty || (item.reorder_point * 2);
                    const estimasiHarga = saranBeli * item.harga_beli;
                    
                    html += `
                        <tr class="border-b">
                            <td class="px-4 py-2 font-medium">${item.kode_barang}</td>
                            <td class="px-4 py-2">${item.nama_barang}</td>
                            <td class="px-4 py-2">${item.kategori?.nama_kategori || '-'}</td>
                            <td class="px-4 py-2 text-center">${item.stok?.jumlah_stok || 0}</td>
                            <td class="px-4 py-2 text-center">${item.reorder_point}</td>
                            <td class="px-4 py-2 text-center font-medium text-blue-600">${saranBeli}</td>
                            <td class="px-4 py-2 font-medium">Rp ${formatNumber(estimasiHarga)}</td>
                        </tr>`;
                });

                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>`;

                if (data.history_pembelian_bulan_ini && data.history_pembelian_bulan_ini.length > 0) {
                    html += `
                        <!-- Purchase History -->
                        <div>
                            <h4 class="text-lg font-semibold mb-3">📦 History Pembelian Bulan Ini</h4>
                            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                                <div class="text-blue-800 font-medium">Total Pembelian Bulan Ini: Rp ${formatNumber(data.total_pembelian_bulan_ini)}</div>
                            </div>
                            <div class="space-y-3">`;
                    
                    data.history_pembelian_bulan_ini.forEach(purchase => {
                        html += `
                            <div class="bg-white border p-4 rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium">${purchase.nomor_masuk}</div>
                                        <div class="text-sm text-gray-600">${purchase.supplier || 'Supplier tidak tercatat'}</div>
                                        <div class="text-xs text-gray-500">${new Date(purchase.tanggal_masuk).toLocaleDateString('id-ID')}</div>
                                    </div>
                                    <div class="font-bold text-green-600">Rp ${formatNumber(purchase.total_nilai)}</div>
                                </div>
                            </div>`;
                    });
                    
                    html += '</div></div>';
                }

                document.getElementById('report-content').innerHTML = html;

            } catch (error) {
                console.error('Error loading restock report:', error);
                document.getElementById('report-content').innerHTML = '<div class="text-red-500 text-center py-8">Error loading report</div>';
            }
        }

        async function loadStockReport() {
            document.getElementById('report-title').textContent = '📦 Laporan Stok';
            document.getElementById('report-content').innerHTML = '<div class="text-center py-8">Loading...</div>';
            
            updatePdfButton('stock');

            try {
                const response = await fetch(`{{ route('owner.simple-reports.stock') }}`);
                const data = await response.json();
                
                let html = `
                    <!-- Stock Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-blue-600 text-sm font-medium">Total Items</div>
                            <div class="text-2xl font-bold text-blue-900">${data.summary.total_items}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-green-600 text-sm font-medium">Stok Aman</div>
                            <div class="text-2xl font-bold text-green-900">${data.summary.stok_aman}</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-yellow-600 text-sm font-medium">Perlu Restock</div>
                            <div class="text-2xl font-bold text-yellow-900">${data.summary.stok_perlu_restock}</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-red-600 text-sm font-medium">Stok Habis</div>
                            <div class="text-2xl font-bold text-red-900">${data.summary.stok_habis}</div>
                        </div>
                    </div>

                    <!-- Total Value -->
                    <div class="bg-purple-50 p-6 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-purple-800 mb-2">💎 Total Nilai Inventory</h4>
                        <div class="text-3xl font-bold text-purple-900">Rp ${formatNumber(data.summary.total_value)}</div>
                    </div>`;

                if (data.per_kategori && data.per_kategori.length > 0) {
                    html += `
                        <!-- Per Category -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold mb-3">📊 Breakdown per Kategori</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Kategori</th>
                                            <th class="px-4 py-2 text-left">Total Items</th>
                                            <th class="px-4 py-2 text-left">Total Qty</th>
                                            <th class="px-4 py-2 text-left">Nilai</th>
                                            <th class="px-4 py-2 text-left">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                    data.per_kategori.forEach(kategori => {
                        html += `
                            <tr class="border-b">
                                <td class="px-4 py-2 font-medium">${kategori.nama_kategori}</td>
                                <td class="px-4 py-2 text-center">${kategori.total_items}</td>
                                <td class="px-4 py-2 text-center">${formatNumber(kategori.total_qty)}</td>
                                <td class="px-4 py-2 font-medium">Rp ${formatNumber(kategori.total_value)}</td>
                                <td class="px-4 py-2">
                                    <div class="flex space-x-1">
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">${kategori.stok_aman} Aman</span>
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">${kategori.stok_restock} Restock</span>
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">${kategori.stok_habis} Habis</span>
                                    </div>
                                </td>
                            </tr>`;
                    });

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>`;
                }

                document.getElementById('report-content').innerHTML = html;

            } catch (error) {
                console.error('Error loading stock report:', error);
                document.getElementById('report-content').innerHTML = '<div class="text-red-500 text-center py-8">Error loading report</div>';
            }
        }

        async function loadMonthlyReport() {
            const date = document.getElementById('report-date').value;
            const month = date.substring(0, 7);
            
            document.getElementById('report-title').textContent = '📊 Laporan Bulanan - ' + month;
            document.getElementById('report-content').innerHTML = '<div class="text-center py-8">Loading...</div>';
            
            updatePdfButton('monthly');

            try {
                const response = await fetch(`{{ route('owner.simple-reports.monthly') }}?month=${month}`);
                const data = await response.json();
                
                let html = `
                    <!-- Monthly Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-green-50 p-6 rounded-lg">
                            <div class="text-green-600 text-sm font-medium">Total Omzet</div>
                            <div class="text-3xl font-bold text-green-900">Rp ${formatNumber(data.summary.omzet)}</div>
                            <div class="text-sm text-green-600 mt-1">${data.summary.total_transaksi} transaksi</div>
                        </div>
                        <div class="bg-red-50 p-6 rounded-lg">
                            <div class="text-red-600 text-sm font-medium">Pengeluaran Beli Barang</div>
                            <div class="text-3xl font-bold text-red-900">Rp ${formatNumber(data.summary.pengeluaran_beli_barang)}</div>
                        </div>
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="text-blue-600 text-sm font-medium">Profit Estimation</div>
                            <div class="text-3xl font-bold ${data.summary.profit_estimation >= 0 ? 'text-blue-900' : 'text-red-900'}">
                                Rp ${formatNumber(data.summary.profit_estimation)}
                            </div>
                        </div>
                    </div>

                    <!-- Growth Comparison -->
                    <div class="bg-purple-50 p-6 rounded-lg mb-6">
                        <h4 class="text-lg font-semibold text-purple-800 mb-2">📈 Perbandingan Bulan Lalu</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="text-purple-600 text-sm">Omzet Bulan Lalu</div>
                                <div class="text-xl font-bold text-purple-900">Rp ${formatNumber(data.growth.last_month_omzet)}</div>
                            </div>
                            <div>
                                <div class="text-purple-600 text-sm">Growth Rate</div>
                                <div class="text-xl font-bold ${data.growth.omzet_growth_percent >= 0 ? 'text-green-600' : 'text-red-600'}">
                                    ${data.growth.omzet_growth_percent >= 0 ? '+' : ''}${data.growth.omzet_growth_percent}%
                                </div>
                            </div>
                        </div>
                    </div>`;

                if (data.daily_breakdown && data.daily_breakdown.length > 0) {
                    html += `
                        <!-- Daily Breakdown -->
                        <div class="bg-gray-50 p-6 rounded-lg mb-6">
                            <h4 class="text-lg font-semibold mb-3">📅 Breakdown Harian</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Tanggal</th>
                                            <th class="px-4 py-2 text-left">Transaksi</th>
                                            <th class="px-4 py-2 text-left">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                    data.daily_breakdown.forEach(day => {
                        html += `
                            <tr class="border-b">
                                <td class="px-4 py-2">${day.day}</td>
                                <td class="px-4 py-2 text-center">${day.daily_transactions}</td>
                                <td class="px-4 py-2 font-medium">Rp ${formatNumber(day.daily_revenue)}</td>
                            </tr>`;
                    });

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>`;
                }

                if (data.top_customers && data.top_customers.length > 0) {
                    html += `
                        <!-- Top Customers -->
                        <div>
                            <h4 class="text-lg font-semibold mb-3">👥 Top Customers Bulan Ini</h4>
                            <div class="space-y-3">`;
                    
                    data.top_customers.forEach((customer, index) => {
                        html += `
                            <div class="flex justify-between items-center p-4 bg-white border rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                        ${index + 1}
                                    </div>
                                    <div>
                                        <div class="font-medium">${customer.nama_customer}</div>
                                        <div class="text-sm text-gray-600">${customer.total_transaksi} transaksi</div>
                                    </div>
                                </div>
                                <div class="font-bold text-green-600">Rp ${formatNumber(customer.total_spending)}</div>
                            </div>`;
                    });
                    
                    html += '</div></div>';
                } else {
                    html += `
                        <div>
                            <h4 class="text-lg font-semibold mb-3">👥 Top Customers Bulan Ini</h4>
                            <div class="text-gray-500 text-center py-4">Tidak ada data customer dengan nama tercatat</div>
                        </div>`;
                }

                document.getElementById('report-content').innerHTML = html;

            } catch (error) {
                console.error('Error loading monthly report:', error);
                document.getElementById('report-content').innerHTML = '<div class="text-red-500 text-center py-8">Error loading report</div>';
            }
        }

        // Initialize default state
        updatePdfButton('daily');
    </script>

    <style>
        /* Custom animations and transitions */
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .export-notification {
            animation: slideInRight 0.3s ease-out;
        }

        .export-notification.removing {
            animation: slideOutRight 0.3s ease-in;
        }

        /* Enhanced hover effects */
        .group:hover .group-hover\:scale-105 {
            transform: scale(1.05);
        }

        /* Smooth transitions for all interactive elements */
        button, a, input {
            transition: all 0.2s ease-in-out;
        }

        /* Focus states for accessibility */
        button:focus, input:focus {
            outline: 2px solid #3B82F6;
            outline-offset: 2px;
        }

        /* Loading spinner animation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Dropdown animation */
        #excelDropdown {
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
        }

        /* Card hover effects */
        .group:hover {
            transform: translateY(-2px);
        }

        /* Mobile responsive adjustments */
        @media (max-width: 640px) {
            .grid-cols-1.sm\:grid-cols-2.lg\:grid-cols-4 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 1rem;
            }
            
            .flex.flex-col.lg\:flex-row {
                flex-direction: column;
                gap: 1rem;
            }
            
            .px-6 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Table responsive styling */
        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #CBD5E0 #F7FAFC;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #F7FAFC;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #CBD5E0;
            border-radius: 3px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #A0AEC0;
        }

        /* Enhanced status badges */
        .bg-green-100 {
            background-color: #F0FDF4;
        }

        .bg-yellow-100 {
            background-color: #FEFCE8;
        }

        .bg-red-100 {
            background-color: #FEF2F2;
        }

        .bg-blue-100 {
            background-color: #EFF6FF;
        }

        .bg-purple-100 {
            background-color: #F3E8FF;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
            }
            
            .shadow-sm, .shadow-lg, .shadow-xl {
                box-shadow: none !important;
            }
        }
    </style>
</body>
</html>