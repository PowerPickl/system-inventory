<!-- Revenue Trends Chart -->
<div class="lg:col-span-2 bg-white rounded-xl shadow-sm card-hover p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">📈 Revenue Trends</h3>
        <div class="flex space-x-2">
            <select id="revenuePeriod" class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly" selected>Monthly</option>
            </select>
            <button class="export-button text-xs">📊 Export Chart</button>
        </div>
    </div>
    <div class="chart-container">
        <canvas id="revenueChart"></canvas>
    </div>
    <div class="mt-4 grid grid-cols-3 gap-4 text-center">
        <div class="p-2 bg-blue-50 rounded">
            <div class="text-sm font-medium text-blue-600">This Period</div>
            <div class="text-lg font-bold text-blue-800" id="currentPeriodRevenue">-</div>
        </div>
        <div class="p-2 bg-green-50 rounded">
            <div class="text-sm font-medium text-green-600">Growth</div>
            <div class="text-lg font-bold text-green-800" id="revenueGrowthPercent">-</div>
        </div>
        <div class="p-2 bg-purple-50 rounded">
            <div class="text-sm font-medium text-purple-600">Avg Daily</div>
            <div class="text-lg font-bold text-purple-800" id="avgDailyRevenue">-</div>
        </div>
    </div>
</div>