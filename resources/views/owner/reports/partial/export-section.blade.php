<!-- Export & Actions Section -->
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">📤 Export & Advanced Reports</h3>
    
    <!-- Quick Export Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <button onclick="exportSalesData('excel')" class="export-button flex items-center justify-center space-x-2 py-3">
            <span>📊</span>
            <span>Export Excel</span>
        </button>
        
        <button onclick="exportSalesData('csv')" class="export-button flex items-center justify-center space-x-2 py-3">
            <span>📋</span>
            <span>Export CSV</span>
        </button>
        
        <button onclick="showAdvancedReports()" class="export-button flex items-center justify-center space-x-2 py-3">
            <span>🔍</span>
            <span>Advanced Analysis</span>
        </button>
        
        <button onclick="scheduledReports()" class="export-button flex items-center justify-center space-x-2 py-3">
            <span>⏰</span>
            <span>Schedule Reports</span>
        </button>
    </div>
    
    <!-- Advanced Export Options -->
    <div class="border-t border-gray-200 pt-4">
        <h4 class="text-md font-medium text-gray-800 mb-3">🎯 Detailed Reports</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            
            <!-- Sales Analysis Reports -->
            <div class="p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer"
                 onclick="exportSpecificReport('sales')">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-lg">📈</span>
                    <span class="font-medium text-sm">Sales Analysis</span>
                </div>
                <div class="text-xs text-gray-600 mb-2">Revenue trends, top products, transaction summaries</div>
                <div class="flex space-x-1">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                </div>
            </div>
            
            <!-- Profit Analysis Reports -->
            <div class="p-3 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-all cursor-pointer"
                 onclick="exportSpecificReport('profit')">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-lg">💰</span>
                    <span class="font-medium text-sm">Profit Analysis</span>
                </div>
                <div class="text-xs text-gray-600 mb-2">Margin trends, category profits, cost analysis</div>
                <div class="flex space-x-1">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                </div>
            </div>
            
            <!-- Restock Intelligence Reports -->
            <div class="p-3 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-all cursor-pointer"
                 onclick="exportSpecificReport('restock')">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-lg">🔄</span>
                    <span class="font-medium text-sm">Restock Intelligence</span>
                </div>
                <div class="text-xs text-gray-600 mb-2">Pattern analysis, lead times, demand forecasting</div>
                <div class="flex space-x-1">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                </div>
            </div>
            
            <!-- Inventory Status Reports -->
            <div class="p-3 border border-gray-200 rounded-lg hover:border-yellow-300 hover:bg-yellow-50 transition-all cursor-pointer"
                 onclick="exportSpecificReport('inventory')">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-lg">📦</span>
                    <span class="font-medium text-sm">Inventory Status</span>
                </div>
                <div class="text-xs text-gray-600 mb-2">Stock levels, EOQ analysis, reorder alerts</div>
                <div class="flex space-x-1">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                </div>
            </div>
            
            <!-- Performance Dashboard -->
            <div class="p-3 border border-gray-200 rounded-lg hover:border-red-300 hover:bg-red-50 transition-all cursor-pointer"
                 onclick="exportSpecificReport('performance')">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-lg">⚡</span>
                    <span class="font-medium text-sm">Performance KPIs</span>
                </div>
                <div class="text-xs text-gray-600 mb-2">Key metrics, growth rates, efficiency scores</div>
                <div class="flex space-x-1">
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Excel</span>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">PDF</span>
                </div>
            </div>
            
            <!-- Custom Reports -->
            <div class="p-3 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-all cursor-pointer"
                 onclick="openCustomReportBuilder()">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-lg">🎨</span>
                    <span class="font-medium text-sm">Custom Report</span>
                </div>
                <div class="text-xs text-gray-600 mb-2">Build your own custom analysis reports</div>
                <div class="flex space-x-1">
                    <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded">Builder</span>
                    <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Custom</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Export Status -->
    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-600">Export system ready</span>
            </div>
            <div class="text-xs text-gray-500" id="exportStatus">All export formats available</div>
        </div>
    </div>
</div>

<script>
function exportSpecificReport(reportType) {
    const reportTypes = {
        'sales': 'Sales Analysis Report',
        'profit': 'Profit Analysis Report', 
        'restock': 'Restock Intelligence Report',
        'inventory': 'Inventory Status Report',
        'performance': 'Performance KPI Report'
    };
    
    const reportName = reportTypes[reportType] || 'Custom Report';
    
    // Show export options modal
    if (confirm(`Export ${reportName}?\n\nThis will generate a comprehensive ${reportName.toLowerCase()} with current data.`)) {
        updateExportStatus(`Generating ${reportName}...`);
        
        // Simulate export process
        setTimeout(() => {
            updateExportStatus(`${reportName} ready for download`);
            alert(`${reportName} has been generated successfully!`);
        }, 2000);
    }
}

function openCustomReportBuilder() {
    alert('Opening Custom Report Builder...\n\nThis feature allows you to create custom reports with:\n- Custom date ranges\n- Specific metrics\n- Custom layouts\n- Multiple export formats');
}

function updateExportStatus(message) {
    const statusElement = document.getElementById('exportStatus');
    if (statusElement) {
        statusElement.textContent = message;
    }
}
</script>