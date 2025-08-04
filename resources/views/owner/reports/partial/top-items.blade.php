<!-- Top Selling Items -->
<div class="bg-white rounded-xl shadow-sm card-hover p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">🏆 Top Selling Items</h3>
        <div class="flex space-x-2">
            <select id="topItemsLimit" class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="5" selected>Top 5</option>
                <option value="10">Top 10</option>
                <option value="20">Top 20</option>
            </select>
            <button class="export-button text-xs">📊 View All</button>
        </div>
    </div>
    
    <div id="topItemsList" class="space-y-3 min-h-[200px]">
        <!-- Loading state -->
        <div class="space-y-3">
            @for($i = 1; $i <= 5; $i++)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg animate-pulse">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gray-300 rounded-full"></div>
                    <div>
                        <div class="h-4 bg-gray-300 rounded w-24 mb-1"></div>
                        <div class="h-3 bg-gray-200 rounded w-16"></div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="h-4 bg-gray-300 rounded w-8 mb-1"></div>
                    <div class="h-3 bg-gray-200 rounded w-10"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
    
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="text-sm text-gray-600 text-center">
            <span class="font-medium">Performance insights:</span> 
            <span id="topItemsInsight">Loading performance metrics...</span>
        </div>
    </div>
</div>