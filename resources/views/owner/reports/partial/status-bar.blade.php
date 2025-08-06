<!-- Real-time Status Bar -->
<div class="bg-gray-100 rounded-lg p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <!-- System Status -->
            <div class="flex items-center space-x-2">
                <div class="status-indicator status-online"></div>
                <span class="text-sm text-gray-600">
                    Real-time data - Last updated: 
                    <span id="lastUpdated" class="font-medium">Loading...</span>
                </span>
            </div>
            
            <!-- Data Status -->
            <div class="flex items-center space-x-2">
                <div class="status-indicator status-online"></div>
                <span class="text-xs text-gray-500" id="dataStatus">All systems operational</span>
            </div>
        </div>
        
        <!-- Performance Metrics -->
        <div class="flex items-center space-x-6">
            <!-- API Response Time -->
            <div class="text-center">
                <div class="text-xs text-gray-500">API Response</div>
                <div class="text-sm font-medium text-green-600" id="apiResponseTime">Fast</div>
            </div>
            
            <!-- Data Freshness -->
            <div class="text-center">
                <div class="text-xs text-gray-500">Data Freshness</div>
                <div class="text-sm font-medium text-blue-600" id="dataFreshness">Real-time</div>
            </div>
            
            <!-- Export Status -->
            <div class="text-center">
                <div class="text-xs text-gray-500">Export Status</div>
                <div class="text-sm font-medium text-purple-600" id="exportSystemStatus">Ready</div>
            </div>
        </div>
    </div>
    
    <!-- Additional Status Information -->
    <div class="mt-3 pt-3 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
            <!-- Active Sessions -->
            <div class="flex items-center justify-center space-x-2">
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                <span class="text-xs text-gray-600">
                    <span id="activeSessions">1</span> active session
                </span>
            </div>
            
            <!-- Cache Status -->
            <div class="flex items-center justify-center space-x-2">
                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                <span class="text-xs text-gray-600">
                    Cache: <span id="cacheStatus" class="font-medium">Optimized</span>
                </span>
            </div>
            
            <!-- Database Performance -->
            <div class="flex items-center justify-center space-x-2">
                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                <span class="text-xs text-gray-600">
                    DB: <span id="dbPerformance" class="font-medium">Excellent</span>
                </span>
            </div>
            
            <!-- Sync Status -->
            <div class="flex items-center justify-center space-x-2">
                <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                <span class="text-xs text-gray-600">
                    Sync: <span id="syncStatus" class="font-medium">Active</span>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="mt-3 pt-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button onclick="forceRefreshData()" 
                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition-colors">
                    🔄 Force Refresh
                </button>
                <button onclick="clearCache()" 
                        class="text-xs bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700 transition-colors">
                    🧹 Clear Cache
                </button>
                <button onclick="exportDiagnostics()" 
                        class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition-colors">
                    📋 Diagnostics
                </button>
            </div>
            
            <div class="text-xs text-gray-500">
                Version 2.1.0 | Build {{ config('app.env') === 'production' ? 'PROD' : 'DEV' }}
            </div>
        </div>
    </div>
</div>

<script>
// Status monitoring functions
function updateSystemStatus() {
    // Simulate system monitoring
    const statuses = ['Excellent', 'Good', 'Fair'];
    const cacheStatuses = ['Optimized', 'Active', 'Refreshing'];
    const syncStatuses = ['Active', 'Syncing', 'Complete'];
    
    // Randomly update status indicators for demo
    document.getElementById('dbPerformance').textContent = 
        statuses[Math.floor(Math.random() * statuses.length)];
    document.getElementById('cacheStatus').textContent = 
        cacheStatuses[Math.floor(Math.random() * cacheStatuses.length)];
    document.getElementById('syncStatus').textContent = 
        syncStatuses[Math.floor(Math.random() * syncStatuses.length)];
    
    // Update API response time
    const responseTime = Math.floor(Math.random() * 200) + 50;
    document.getElementById('apiResponseTime').textContent = 
        responseTime < 100 ? 'Fast' : responseTime < 200 ? 'Good' : 'Slow';
}

function forceRefreshData() {
    updateDataStatus('Forcing data refresh...');
    
    // Simulate refresh process
    setTimeout(() => {
        loadDashboard();
        updateDataStatus('Data refreshed successfully');
    }, 1000);
}

function clearCache() {
    updateDataStatus('Clearing cache...');
    
    // Simulate cache clearing
    setTimeout(() => {
        updateDataStatus('Cache cleared successfully');
        document.getElementById('cacheStatus').textContent = 'Cleared';
    }, 500);
}

function exportDiagnostics() {
    const diagnostics = {
        timestamp: new Date().toISOString(),
        system_status: 'Operational',
        active_sessions: document.getElementById('activeSessions').textContent,
        db_performance: document.getElementById('dbPerformance').textContent,
        cache_status: document.getElementById('cacheStatus').textContent,
        api_response: document.getElementById('apiResponseTime').textContent,
        data_freshness: document.getElementById('dataFreshness').textContent
    };
    
    // In production, this would generate an actual diagnostics file
    console.log('Diagnostics Report:', diagnostics);
    alert('Diagnostics exported to console (in production, this would download a file)');
}

function updateDataStatus(message) {
    const statusElement = document.getElementById('dataStatus');
    if (statusElement) {
        statusElement.textContent = message;
    }
}

// Auto-update system status every 30 seconds
setInterval(updateSystemStatus, 30000);

// Initialize status on load
document.addEventListener('DOMContentLoaded', function() {
    updateSystemStatus();
});
</script>