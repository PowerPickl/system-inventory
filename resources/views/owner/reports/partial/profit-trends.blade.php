<!-- Profit Margin Trends -->
<div class="lg:col-span-2 bg-white rounded-xl shadow-sm card-hover p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">📊 Profit Margin Trends</h3>
        <div class="flex space-x-2">
            <select id="profitTrendsPeriod" class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
            </select>
            <button class="export-button text-xs">📊 Export</button>
        </div>
    </div>
    
    <div class="chart-container">
        <canvas id="profitTrendsChart"></canvas>
    </div>
    
    <!-- Trend Analysis Summary -->
    <div class="mt-4 grid grid-cols-4 gap-4 text-center">
        <div class="p-3 bg-blue-50 rounded">
            <div class="text-sm font-medium text-blue-600">Current Margin</div>
            <div class="text-xl font-bold text-blue-800" id="currentMargin">-</div>
        </div>
        <div class="p-3 bg-green-50 rounded">
            <div class="text-sm font-medium text-green-600">Best Month</div>
            <div class="text-xl font-bold text-green-800" id="bestMargin">-</div>
        </div>
        <div class="p-3 bg-yellow-50 rounded">
            <div class="text-sm font-medium text-yellow-600">Average</div>
            <div class="text-xl font-bold text-yellow-800" id="avgMargin">-</div>
        </div>
        <div class="p-3 bg-purple-50 rounded">
            <div class="text-sm font-medium text-purple-600">Trend</div>
            <div class="text-xl font-bold text-purple-800" id="marginTrend">-</div>
        </div>
    </div>
    
    <!-- Insights -->
    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
        <div class="text-sm font-medium text-gray-700 mb-2">💡 Margin Insights:</div>
        <div id="marginInsights" class="text-sm text-gray-600">
            Analyzing profit margin trends and performance patterns...
        </div>
    </div>
</div>