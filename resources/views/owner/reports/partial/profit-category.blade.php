<!-- Profit by Category -->
<div class="bg-white rounded-xl shadow-sm card-hover p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">💰 Profit by Category</h3>
        <div class="flex space-x-2">
            <button class="export-button text-xs">📊 Details</button>
        </div>
    </div>
    
    <div class="chart-container">
        <canvas id="profitCategoryChart"></canvas>
    </div>
    
    <!-- Category Performance Summary -->
    <div class="mt-4 space-y-2">
        <div class="text-sm font-medium text-gray-700">Category Performance:</div>
        <div id="categoryPerformance" class="space-y-1">
            <!-- Will be populated by JavaScript -->
            <div class="flex justify-between items-center text-xs">
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    <span class="animate-pulse bg-gray-200 h-3 w-16 rounded"></span>
                </span>
                <span class="animate-pulse bg-gray-200 h-3 w-12 rounded"></span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span class="animate-pulse bg-gray-200 h-3 w-20 rounded"></span>
                </span>
                <span class="animate-pulse bg-gray-200 h-3 w-12 rounded"></span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                    <span class="animate-pulse bg-gray-200 h-3 w-14 rounded"></span>
                </span>
                <span class="animate-pulse bg-gray-200 h-3 w-12 rounded"></span>
            </div>
        </div>
    </div>
    
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600">Overall Margin:</span>
            <span class="font-semibold text-green-600" id="overallMargin">Loading...</span>
        </div>
    </div>
</div>