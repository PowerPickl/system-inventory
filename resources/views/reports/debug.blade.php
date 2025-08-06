<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports API Debug Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .json-viewer {
            background: #1a1a1a;
            border-radius: 8px;
            padding: 16px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .json-key { color: #79c0ff; }
        .json-string { color: #a5d6ff; }
        .json-number { color: #79c0ff; }
        .json-boolean { color: #ffab70; }
        .json-null { color: #ff7b72; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-900">📊 Reports API Debug Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <select id="dateRange" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month" selected>This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                    <button id="refreshAll" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                        🔄 Refresh All
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Date Range Inputs (Hidden by default) -->
        <div id="customDateInputs" class="hidden mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="startDate" class="mt-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="endDate" class="mt-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
                </div>
                <div class="pt-6">
                    <button id="applyCustomDate" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                        Apply Custom Range
                    </button>
                </div>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            
            <!-- 📈 Sales Analysis Reports -->
            <div class="col-span-full mb-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">📈 Sales Analysis Reports</h2>
            </div>
            
            <!-- Revenue Analysis -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Revenue Analysis</h3>
                    <div class="flex space-x-2">
                        <select id="revenuePeriod" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                        </select>
                        <button onclick="testEndpoint('revenue-analysis')" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Test</button>
                    </div>
                </div>
                <div id="revenue-analysis-result" class="min-h-32"></div>
            </div>

            <!-- Top Selling Items -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Top Selling Items</h3>
                    <div class="flex space-x-2">
                        <select id="topItemsLimit" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="5">Top 5</option>
                            <option value="10" selected>Top 10</option>
                            <option value="20">Top 20</option>
                        </select>
                        <button onclick="testEndpoint('top-selling-items')" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Test</button>
                    </div>
                </div>
                <div id="top-selling-items-result" class="min-h-32"></div>
            </div>

            <!-- Transaction Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Transaction Summary</h3>
                    <button onclick="testEndpoint('transaction-summary')" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Test</button>
                </div>
                <div id="transaction-summary-result" class="min-h-32"></div>
            </div>

            <!-- 💰 Profit Analysis Reports -->
            <div class="col-span-full mb-4 mt-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">💰 Profit Analysis Reports</h2>
            </div>

            <!-- Profit by Category -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Profit by Category</h3>
                    <button onclick="testEndpoint('profit-by-category')" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Test</button>
                </div>
                <div id="profit-by-category-result" class="min-h-32"></div>
            </div>

            <!-- Profit Margin Trends -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Profit Margin Trends</h3>
                    <div class="flex space-x-2">
                        <select id="profitTrendsPeriod" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                        </select>
                        <button onclick="testEndpoint('profit-margin-trends')" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Test</button>
                    </div>
                </div>
                <div id="profit-margin-trends-result" class="min-h-32"></div>
            </div>

            <!-- 🔄 Advanced Restock Intelligence -->
            <div class="col-span-full mb-4 mt-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🔄 Advanced Restock Intelligence</h2>
            </div>

            <!-- Restock Pattern Analysis -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Restock Pattern Analysis</h3>
                    <button onclick="testEndpoint('restock-pattern-analysis')" class="bg-purple-600 text-white px-3 py-1 rounded text-sm">Test</button>
                </div>
                <div id="restock-pattern-analysis-result" class="min-h-32"></div>
            </div>

            <!-- Lead Time Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Lead Time Performance</h3>
                    <button onclick="testEndpoint('lead-time-performance')" class="bg-purple-600 text-white px-3 py-1 rounded text-sm">Test</button>
                </div>
                <div id="lead-time-performance-result" class="min-h-32"></div>
            </div>

            <!-- Restock Cost Efficiency -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Restock Cost Efficiency</h3>
                    <button onclick="testEndpoint('restock-cost-efficiency')" class="bg-purple-600 text-white px-3 py-1 rounded text-sm">Test</button>
                </div>
                <div id="restock-cost-efficiency-result" class="min-h-32"></div>
            </div>

            <!-- Demand Forecasting -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Demand Forecasting</h3>
                    <div class="flex space-x-2">
                        <select id="forecastLookback" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="3">3 Months</option>
                            <option value="6" selected>6 Months</option>
                            <option value="12">12 Months</option>
                        </select>
                        <button onclick="testEndpoint('demand-forecasting')" class="bg-purple-600 text-white px-3 py-1 rounded text-sm">Test</button>
                    </div>
                </div>
                <div id="demand-forecasting-result" class="min-h-32"></div>
            </div>

            <!-- Export Test -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Export Sales Data</h3>
                    <div class="flex space-x-2">
                        <select id="exportFormat" class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                        <button onclick="testEndpoint('export-sales-data')" class="bg-orange-600 text-white px-3 py-1 rounded text-sm">Test</button>
                    </div>
                </div>
                <div id="export-sales-data-result" class="min-h-32"></div>
            </div>
        </div>

        <!-- Global Status -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🚀 Quick Actions & Status</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="testAllSalesReports()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                    Test All Sales Reports
                </button>
                <button onclick="testAllProfitReports()" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700">
                    Test All Profit Reports
                </button>
                <button onclick="testAllRestockReports()" class="bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700">
                    Test All Restock Reports
                </button>
                <button onclick="clearAllResults()" class="bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700">
                    Clear All Results
                </button>
            </div>
            
            <div id="globalStatus" class="mt-4 p-4 bg-gray-100 rounded-lg">
                <span class="text-gray-600 text-sm">Ready to test endpoints...</span>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        const BASE_URL = '/owner/reports';
        let currentDateRange = 'this_month';
        
        // Initialize
        $(document).ready(function() {
            setupEventHandlers();
            setDefaultDates();
        });

        function setupEventHandlers() {
            $('#dateRange').on('change', function() {
                currentDateRange = $(this).val();
                if (currentDateRange === 'custom') {
                    $('#customDateInputs').removeClass('hidden');
                } else {
                    $('#customDateInputs').addClass('hidden');
                }
            });

            $('#refreshAll').on('click', function() {
                testAllReports();
            });

            $('#applyCustomDate').on('click', function() {
                const startDate = $('#startDate').val();
                const endDate = $('#endDate').val();
                if (startDate && endDate) {
                    updateGlobalStatus(`Custom date range applied: ${startDate} to ${endDate}`);
                } else {
                    alert('Please select both start and end dates');
                }
            });
        }

        function setDefaultDates() {
            const today = new Date();
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            
            $('#startDate').val(formatDate(lastMonth));
            $('#endDate').val(formatDate(endOfLastMonth));
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function getDateParams() {
            let params = {};
            
            switch(currentDateRange) {
                case 'today':
                    const today = new Date();
                    params.start_date = formatDate(today);
                    params.end_date = formatDate(today);
                    break;
                case 'this_week':
                    const now = new Date();
                    const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
                    const endOfWeek = new Date(now.setDate(now.getDate() - now.getDay() + 6));
                    params.start_date = formatDate(startOfWeek);
                    params.end_date = formatDate(endOfWeek);
                    break;
                case 'this_month':
                    const thisMonth = new Date();
                    params.start_date = formatDate(new Date(thisMonth.getFullYear(), thisMonth.getMonth(), 1));
                    params.end_date = formatDate(new Date(thisMonth.getFullYear(), thisMonth.getMonth() + 1, 0));
                    break;
                case 'last_month':
                    const lastMonth = new Date();
                    params.start_date = formatDate(new Date(lastMonth.getFullYear(), lastMonth.getMonth() - 1, 1));
                    params.end_date = formatDate(new Date(lastMonth.getFullYear(), lastMonth.getMonth(), 0));
                    break;
                case 'custom':
                    params.start_date = $('#startDate').val();
                    params.end_date = $('#endDate').val();
                    break;
            }
            
            return params;
        }

        function testEndpoint(endpoint) {
            const resultDiv = $(`#${endpoint}-result`);
            resultDiv.html('<div class="loading h-8 rounded"></div>');
            
            let params = getDateParams();
            
            // Add specific parameters for each endpoint
            switch(endpoint) {
                case 'revenue-analysis':
                    params.period = $('#revenuePeriod').val();
                    break;
                case 'top-selling-items':
                    params.limit = $('#topItemsLimit').val();
                    break;
                case 'profit-margin-trends':
                    params.period = $('#profitTrendsPeriod').val();
                    break;
                case 'demand-forecasting':
                    params.lookback_months = $('#forecastLookback').val();
                    params.forecast_months = 3;
                    break;
                case 'export-sales-data':
                    params.format = $('#exportFormat').val();
                    break;
            }

            updateGlobalStatus(`🔄 Testing ${endpoint}...`);

            $.get(`${BASE_URL}/${endpoint}`, params)
                .done(function(data) {
                    displayResult(endpoint, data, 'success');
                    updateGlobalStatus(`✅ ${endpoint} completed successfully`);
                })
                .fail(function(xhr) {
                    const error = {
                        status: xhr.status,
                        message: xhr.responseJSON?.message || 'Unknown error',
                        errors: xhr.responseJSON?.errors || null
                    };
                    displayResult(endpoint, error, 'error');
                    updateGlobalStatus(`❌ ${endpoint} failed: ${error.message}`);
                });
        }

        function displayResult(endpoint, data, type) {
            const resultDiv = $(`#${endpoint}-result`);
            
            if (type === 'success') {
                const summary = generateSummary(endpoint, data);
                const jsonView = formatJSON(data);
                
                resultDiv.html(`
                    <div class="mb-3">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✅ Success
                            </span>
                            <span class="text-sm text-gray-500">${new Date().toLocaleTimeString()}</span>
                        </div>
                        ${summary}
                    </div>
                    <details class="cursor-pointer">
                        <summary class="text-sm text-blue-600 hover:text-blue-800">View Raw JSON</summary>
                        <div class="json-viewer mt-2">${jsonView}</div>
                    </details>
                `);
            } else {
                resultDiv.html(`
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            ❌ Error
                        </span>
                        <span class="text-sm text-gray-500">${new Date().toLocaleTimeString()}</span>
                    </div>
                    <div class="json-viewer">${formatJSON(data)}</div>
                `);
            }
        }

        function generateSummary(endpoint, data) {
            switch(endpoint) {
                case 'revenue-analysis':
                    const totalRevenue = data.summary?.total_revenue || 0;
                    const totalTransactions = data.summary?.total_transactions || 0;
                    const avgTransaction = data.summary?.average_transaction || 0;
                    const dataPoints = data.data?.length || 0;
                    
                    return `
                        <div class="text-sm space-y-1">
                            <div><strong>Total Revenue:</strong> ${Number(totalRevenue).toLocaleString()}</div>
                            <div><strong>Transactions:</strong> ${totalTransactions}</div>
                            <div><strong>Avg Transaction:</strong> ${Number(avgTransaction).toFixed(2)}</div>
                            <div><strong>Data Points:</strong> ${dataPoints}</div>
                        </div>
                    `;
                case 'top-selling-items':
                    return `
                        <div class="text-sm space-y-1">
                            <div><strong>Items Found:</strong> ${data.top_items?.length || '0'}</div>
                            <div><strong>Top Item:</strong> ${data.top_items?.[0]?.nama_barang || 'None'}</div>
                            <div><strong>Top Qty Sold:</strong> ${data.top_items?.[0]?.total_qty_sold || '0'}</div>
                        </div>
                    `;
                case 'profit-by-category':
                    const totalProfit = data.total_gross_profit || 0;
                    return `
                        <div class="text-sm space-y-1">
                            <div><strong>Categories:</strong> ${data.category_profits?.length || '0'}</div>
                            <div><strong>Total Profit:</strong> ${Number(totalProfit).toLocaleString()}</div>
                            <div><strong>Top Category:</strong> ${data.category_profits?.[0]?.nama_kategori || 'None'}</div>
                        </div>
                    `;
                case 'restock-pattern-analysis':
                    return `
                        <div class="text-sm space-y-1">
                            <div><strong>Items Analyzed:</strong> ${data.frequency_analysis?.length || '0'}</div>
                            <div><strong>Seasonal Data Points:</strong> ${data.seasonal_pattern?.length || '0'}</div>
                        </div>
                    `;
                default:
                    return `<div class="text-sm text-gray-600">Data loaded successfully</div>`;
            }
        }

        function formatJSON(obj) {
            return JSON.stringify(obj, null, 2)
                .replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    var cls = 'json-number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'json-key';
                        } else {
                            cls = 'json-string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'json-boolean';
                    } else if (/null/.test(match)) {
                        cls = 'json-null';
                    }
                    return '<span class="' + cls + '">' + match + '</span>';
                });
        }

        function updateGlobalStatus(message) {
            $('#globalStatus').html(`
                <div class="flex items-center space-x-2">
                    <span class="text-sm">${message}</span>
                    <span class="text-xs text-gray-400">${new Date().toLocaleTimeString()}</span>
                </div>
            `);
        }

        function testAllSalesReports() {
            testEndpoint('revenue-analysis');
            setTimeout(() => testEndpoint('top-selling-items'), 500);
            setTimeout(() => testEndpoint('transaction-summary'), 1000);
        }

        function testAllProfitReports() {
            testEndpoint('profit-by-category');
            setTimeout(() => testEndpoint('profit-margin-trends'), 500);
        }

        function testAllRestockReports() {
            testEndpoint('restock-pattern-analysis');
            setTimeout(() => testEndpoint('lead-time-performance'), 500);
            setTimeout(() => testEndpoint('restock-cost-efficiency'), 1000);
            setTimeout(() => testEndpoint('demand-forecasting'), 1500);
        }

        function testAllReports() {
            updateGlobalStatus('🚀 Testing all reports...');
            testAllSalesReports();
            setTimeout(() => testAllProfitReports(), 2000);
            setTimeout(() => testAllRestockReports(), 4000);
        }

        function clearAllResults() {
            $('[id$="-result"]').html('<div class="text-gray-400 text-sm">Ready to test...</div>');
            updateGlobalStatus('🧹 All results cleared');
        }
    </script>
</body>
</html>