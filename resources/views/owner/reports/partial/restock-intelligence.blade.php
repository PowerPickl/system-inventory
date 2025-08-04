<!-- Restock Intelligence -->
<div class="bg-white rounded-xl shadow-sm card-hover p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">🔄 Restock Intelligence</h3>
        <div class="flex space-x-2">
            <button onclick="window.location.href='{{ route('owner.restock-approval.index') }}'" class="export-button text-xs">📊 Manage</button>
        </div>
    </div>
    
    <div id="restockInsights" class="space-y-3 min-h-[180px]">
        <!-- Loading state -->
        <div class="space-y-3">
            @for($i = 1; $i <= 3; $i++)
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg animate-pulse">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 bg-gray-300 rounded"></div>
                    <div>
                        <div class="h-4 bg-gray-300 rounded w-20 mb-1"></div>
                        <div class="h-3 bg-gray-200 rounded w-16"></div>
                    </div>
                </div>
                <div class="h-6 bg-gray-300 rounded w-12"></div>
            </div>
            @endfor
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="grid grid-cols-2 gap-4 text-center">
            <div class="p-2 bg-red-50 rounded">
                <div class="text-xs text-red-600 font-medium">Urgent Items</div>
                <div class="text-lg font-bold text-red-700" id="urgentItems">-</div>
            </div>
            <div class="p-2 bg-yellow-50 rounded">
                <div class="text-xs text-yellow-600 font-medium">Monitoring</div>
                <div class="text-lg font-bold text-yellow-700" id="monitoringItems">-</div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="mt-4 space-y-2">
        <button onclick="window.location.href='{{ route('owner.restock-approval.index') }}'" 
                class="w-full text-sm bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
            📋 Review Restock Requests
        </button>
        <button onclick="generateRestockReport()" 
                class="w-full text-sm bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
            📊 Generate Restock Report
        </button>
    </div>
</div>

<script>
function generateRestockReport() {
    // This would integrate with the restock reporting system
    alert('Generating comprehensive restock analysis report...');
}
</script>