<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OwnerController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\KasirController;
use App\Http\Middleware\RoleMiddleware;

// ADD THESE MISSING IMPORTS
use App\Http\Controllers\Owner\RestockApprovalController;
use App\Http\Controllers\Gudang\MonitoringStockController;
use App\Http\Controllers\Gudang\RestockRequestController;
use App\Http\Controllers\Gudang\BarangMasukController;
use App\Http\Controllers\Gudang\VerifikasiPermintaanController;
use App\Http\Controllers\Kasir\TransaksiServiceController;
use App\Http\Controllers\Gudang\KelolaDataBarangController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\SimpleReportController;
use App\Http\Controllers\Kasir\SearchBarangController;
use App\Http\Controllers\Kasir\HistoryTransaksiController;
use App\Http\Controllers\Owner\KelolaUserController;
use App\Http\Controllers\Auth\CustomAuthController;

// ============================================================================
// 🔥 CUSTOM AUTH ROUTES - NEW ROBUST AUTH SYSTEM
// ============================================================================

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes (not authenticated)
Route::group(['middleware' => 'guest'], function () {
    // Login routes
    Route::get('/login', [CustomAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomAuthController::class, 'login'])->name('custom.login');
});

// Authenticated routes (logged in users)
Route::group(['middleware' => 'auth'], function () {
    // Logout route
    Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');
    
    // User info routes for AJAX
    Route::get('/auth/user-info', [CustomAuthController::class, 'getUserInfo'])->name('auth.user-info');
    Route::get('/auth/check-status', [CustomAuthController::class, 'checkStatus'])->name('auth.check-status');
    Route::get('/auth/heartbeat', [CustomAuthController::class, 'checkStatus'])->name('auth.heartbeat');
});

// ============================================================================
// 🛡️ PROTECTED ROUTES WITH ENHANCED MIDDLEWARE
// ============================================================================

// Apply check.active middleware to all authenticated routes
Route::middleware(['auth', 'check.active'])->group(function () {
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Default dashboard (fallback with smart redirect)
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return redirect(match($user->role_name) {
            'Owner' => route('owner.dashboard'),
            'Gudang' => route('dashboard.gudang'),
            'Kasir' => route('dashboard.kasir'),
            default => '/login'
        });
    })->name('dashboard');

    // ============================================================================
    // 👑 OWNER ROUTES - FULL ACCESS
    // ============================================================================
    
    Route::middleware([RoleMiddleware::class . ':Owner'])->group(function () {
        // Owner Dashboard
        Route::get('/dashboard-owner', [OwnerController::class, 'index'])->name('owner.dashboard');
        
        // Owner specific routes with prefix
        Route::prefix('owner')->name('owner.')->group(function () {
            
            // === KELOLA USER ROUTES ===
            Route::prefix('kelola-user')->name('kelola-user.')->group(function () {
                // Main page
                Route::get('/', [KelolaUserController::class, 'index'])->name('index');
                
                // Data operations
                Route::get('/data', [KelolaUserController::class, 'getData'])->name('data');
                Route::get('/roles', [KelolaUserController::class, 'getRoles'])->name('roles');
                Route::get('/stats', [KelolaUserController::class, 'getStats'])->name('stats');
                
                // CRUD operations
                Route::post('/', [KelolaUserController::class, 'store'])->name('store');
                Route::get('/{id}', [KelolaUserController::class, 'show'])->name('show');
                Route::put('/{id}', [KelolaUserController::class, 'update'])->name('update');
                Route::delete('/{id}', [KelolaUserController::class, 'destroy'])->name('destroy');
                
                // Special actions
                Route::post('/{id}/toggle-status', [KelolaUserController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/{id}/reset-password', [KelolaUserController::class, 'resetPassword'])->name('reset-password');
            });

            // === RESTOCK APPROVAL ROUTES ===
            // Search routes - MUST come before {id} routes
            Route::get('/restock-approval/search/items', [RestockApprovalController::class, 'searchItems'])->name('restock-approval.search-items');
            Route::get('/restock-approval/available-items', [RestockApprovalController::class, 'getAvailableItems'])->name('restock-approval.available-items');
            Route::get('/restock-approval/stats', [RestockApprovalController::class, 'getApprovalStats'])->name('restock-approval.stats');
            
            // Main restock approval routes
            Route::get('/restock-approval', [RestockApprovalController::class, 'index'])->name('restock-approval.index');
            Route::get('/restock-approval/{id}/details', [RestockApprovalController::class, 'getRequestDetails'])->name('restock-approval.details');
            Route::get('/restock-approval/{id}/export-order-list', [RestockApprovalController::class, 'exportOrderList'])->name('restock-approval.export-order-list');
            
            // Action routes
            Route::post('/restock-approval/{id}/approve', [RestockApprovalController::class, 'approve'])->name('restock-approval.approve');
            Route::post('/restock-approval/{id}/reject', [RestockApprovalController::class, 'reject'])->name('restock-approval.reject');
            Route::post('/restock-approval/{id}/quick-approve', [RestockApprovalController::class, 'quickApprove'])->name('restock-approval.quick-approve');
            Route::post('/restock-approval/{id}/mark-ordered', [RestockApprovalController::class, 'markAsOrdered'])->name('restock-approval.mark-ordered');
            Route::post('/restock-approval/{id}/force-terminate', [RestockApprovalController::class, 'forceTerminate'])->name('restock-approval.force-terminate');
            
            // Show route - keep this last among {id} routes
            Route::get('/restock-approval/{id}', [RestockApprovalController::class, 'show'])->name('restock-approval.show');

            // === SIMPLE REPORTS ROUTES ===
            Route::prefix('simple-reports')->name('simple-reports.')->group(function () {
                // Main dashboard
                Route::get('/', [SimpleReportController::class, 'index'])->name('index');
                Route::get('/export-simple-excel', [SimpleReportController::class, 'exportSimpleExcel'])->name('export-simple-excel');
                
                // Daily & Weekly Reports
                Route::get('/daily', [SimpleReportController::class, 'dailyReport'])->name('daily');
                Route::get('/weekly', [SimpleReportController::class, 'weeklyReport'])->name('weekly');
                
                // Restock & Shopping Reports  
                Route::get('/restock', [SimpleReportController::class, 'restockReport'])->name('restock');
                
                // Stock Reports
                Route::get('/stock', [SimpleReportController::class, 'stockReport'])->name('stock');
                
                // Monthly Reports
                Route::get('/monthly', [SimpleReportController::class, 'monthlyReport'])->name('monthly');
                
                // Export Functions
                Route::get('/export-pdf-summary', [SimpleReportController::class, 'exportPdfSummary'])->name('export-pdf-summary');
                Route::get('/export-excel-detail', [SimpleReportController::class, 'exportExcelDetail'])->name('export-excel-detail');
            });
            
            // Backward compatibility
            Route::get('/simple-excel', [SimpleReportController::class, 'exportSimpleExcel'])->name('simple-excel');
        });
    });

    // ============================================================================
    // 📦 GUDANG ROUTES - WAREHOUSE MANAGEMENT
    // ============================================================================
    
    Route::middleware([RoleMiddleware::class . ':Gudang'])->group(function () {
        // Gudang Dashboard
        Route::get('/dashboard-gudang', [GudangController::class, 'index'])->name('dashboard.gudang');
        
        // Gudang specific routes with prefix
        Route::prefix('gudang')->name('gudang.')->group(function () {
            
            // === VERIFIKASI PERMINTAAN ROUTES ===
            Route::get('/verifikasi-permintaan', [VerifikasiPermintaanController::class, 'index'])->name('verifikasi-permintaan');
            
            // Main data endpoints
            Route::get('/verifikasi-permintaan/data', [VerifikasiPermintaanController::class, 'getData'])->name('verifikasi-permintaan.data');
            Route::get('/verifikasi-permintaan/{id}/detail', [VerifikasiPermintaanController::class, 'getTransactionDetail'])->name('verifikasi-permintaan.detail');
            
            // Validation actions
            Route::post('/verifikasi-permintaan/validate-item', [VerifikasiPermintaanController::class, 'validateItem'])->name('verifikasi-permintaan.validate-item');
            Route::post('/verifikasi-permintaan/bulk-approve', [VerifikasiPermintaanController::class, 'bulkApprove'])->name('verifikasi-permintaan.bulk-approve');
            Route::post('/verifikasi-permintaan/bulk-approve-transaction', [VerifikasiPermintaanController::class, 'bulkApproveTransaction'])->name('verifikasi-permintaan.bulk-approve-transaction');
            Route::post('/verifikasi-permintaan/bulk-reject-transaction', [VerifikasiPermintaanController::class, 'bulkRejectTransaction'])->name('verifikasi-permintaan.bulk-reject-transaction');
            
            // Analytics & monitoring endpoints
            Route::get('/verifikasi-permintaan/stats', [VerifikasiPermintaanController::class, 'getValidationStats'])->name('verifikasi-permintaan.stats');
            Route::get('/verifikasi-permintaan/recent-activities', [VerifikasiPermintaanController::class, 'getRecentActivities'])->name('verifikasi-permintaan.recent-activities');
            Route::get('/verifikasi-permintaan/export-report', [VerifikasiPermintaanController::class, 'exportValidationReport'])->name('verifikasi-permintaan.export-report');
            
            // === MONITORING STOCK ROUTES ===
            Route::get('/monitoring-stock', [MonitoringStockController::class, 'index'])->name('monitoring-stock');
            Route::post('/monitoring-stock/update-eoq/{id}', [MonitoringStockController::class, 'updateEOQ']);
            Route::post('/monitoring-stock/update-all-eoq', [MonitoringStockController::class, 'updateAllEOQ']);
            Route::get('/monitoring-stock/realtime-data', [MonitoringStockController::class, 'getRealTimeData']);
            Route::get('/monitoring-stock/eoq-details/{id}', [MonitoringStockController::class, 'getEOQDetails']);
            Route::get('/monitoring-stock/trends/{id}', [MonitoringStockController::class, 'getStockTrends']);
            
            // Restock Request routes
            Route::post('/monitoring-stock/restock-recommendations', [MonitoringStockController::class, 'getRestockRecommendations']);
            Route::post('/monitoring-stock/create-restock-request', [MonitoringStockController::class, 'createRestockRequest']);
            Route::post('/monitoring-stock/quick-restock', [MonitoringStockController::class, 'quickRestockRequest']);
            
            // === RESTOCK REQUEST MANAGEMENT ROUTES ===
            Route::get('/restock-requests', [RestockRequestController::class, 'index'])->name('restock-requests');
            Route::get('/restock-requests/{id}', [RestockRequestController::class, 'show'])->name('restock-requests.show');
            Route::get('/restock-requests/{id}/status', [RestockRequestController::class, 'getRequestStatus'])->name('restock-requests.status');
            Route::post('/restock-requests/{id}/cancel', [RestockRequestController::class, 'cancel'])->name('restock-requests.cancel');

            // === BARANG MASUK ROUTES ===
            Route::get('/barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk');

            // API routes untuk barang masuk
            Route::get('/barang-masuk/pending-requests', [BarangMasukController::class, 'getPendingRequests']);
            Route::get('/barang-masuk/search-request/{requestNumber}', [BarangMasukController::class, 'searchRequest']);
            Route::post('/barang-masuk/process-request', [BarangMasukController::class, 'processRequest']);
            Route::get('/barang-masuk/search-items', [BarangMasukController::class, 'searchItems']);
            Route::post('/barang-masuk/process-direct', [BarangMasukController::class, 'processDirect']);
            Route::get('/barang-masuk/recent-entries', [BarangMasukController::class, 'getRecentEntries']);
            Route::get('/barang-masuk/statistics', [BarangMasukController::class, 'getStatistics']);
            Route::get('/barang-masuk/{id}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
            Route::get('/barang-masuk/{id}/export', [BarangMasukController::class, 'exportEntry']);

            // === KELOLA DATA BARANG ROUTES ===
            Route::prefix('kelola-data-barang')->name('kelola-data-barang.')->group(function () {
                // Main page
                Route::get('/', [KelolaDataBarangController::class, 'index'])->name('index');
                
                // Data operations
                Route::get('/data', [KelolaDataBarangController::class, 'getData'])->name('data');
                Route::get('/activity-log', [KelolaDataBarangController::class, 'getActivityLog'])->name('activity-log');
                Route::get('/kategori-options', [KelolaDataBarangController::class, 'getKategoriOptions'])->name('kategori-options');
                Route::post('/preview-kode', [KelolaDataBarangController::class, 'previewKodeBarang'])->name('preview-kode');
                
                Route::post('/bulk-calculate-eoq', [KelolaDataBarangController::class, 'bulkCalculateEOQ'])->name('bulk-calculate-eoq');
                
                // Activity and reporting
                Route::get('/export', [KelolaDataBarangController::class, 'exportData'])->name('export');
                Route::get('/stats', [KelolaDataBarangController::class, 'getStats'])->name('stats');
                
                // Utility endpoints
                Route::get('/search', [KelolaDataBarangController::class, 'searchBarang'])->name('search');
                Route::post('/validate-kode', [KelolaDataBarangController::class, 'validateKodeBarang'])->name('validate-kode');
                Route::get('/recent-activities/{limit?}', [KelolaDataBarangController::class, 'getRecentActivities'])->name('recent-activities');
                
                // Stock operations
                Route::post('/', [KelolaDataBarangController::class, 'store'])->name('store');
                Route::post('/{id}/adjust-stock', [KelolaDataBarangController::class, 'adjustStock'])->name('adjust-stock');
                
                // EOQ operations
                Route::post('/{id}/calculate-eoq', [KelolaDataBarangController::class, 'calculateEOQ'])->name('calculate-eoq');
                Route::post('/{id}/auto-calculate-eoq-params', [KelolaDataBarangController::class, 'autoCalculateEOQParams'])->name('auto-calculate-eoq-params');
                Route::get('/{id}', [KelolaDataBarangController::class, 'show'])->name('show');
                Route::put('/{id}', [KelolaDataBarangController::class, 'update'])->name('update');
                Route::delete('/{id}', [KelolaDataBarangController::class, 'destroy'])->name('destroy');
            });

            // Add backward compatibility route
            Route::get('/kelola-data-barang', [KelolaDataBarangController::class, 'index'])->name('kelola-data-barang');

            // === API ENDPOINTS FOR AJAX CALLS ===
            Route::prefix('api/barang')->name('api.barang.')->group(function () {
                Route::get('/check-eoq-params/{id}', function($id) {
                    $barang = \App\Models\Barang::findOrFail($id);
                    $hasComplete = !is_null($barang->annual_demand) && 
                                  !is_null($barang->ordering_cost) && 
                                  !is_null($barang->holding_cost) && 
                                  !is_null($barang->lead_time) &&
                                  !is_null($barang->demand_avg_daily) && 
                                  !is_null($barang->demand_max_daily);
                    
                    return response()->json([
                        'has_complete_params' => $hasComplete,
                        'missing_params' => array_filter([
                            'annual_demand' => is_null($barang->annual_demand),
                            'ordering_cost' => is_null($barang->ordering_cost),
                            'holding_cost' => is_null($barang->holding_cost),
                            'lead_time' => is_null($barang->lead_time),
                            'demand_avg_daily' => is_null($barang->demand_avg_daily),
                            'demand_max_daily' => is_null($barang->demand_max_daily),
                        ])
                    ]);
                })->name('check-eoq-params');
                
                Route::get('/eoq-status/{id}', function($id) {
                    $barang = \App\Models\Barang::with('stok')->findOrFail($id);
                    $service = new \App\Services\EOQCalculationService();
                    
                    try {
                        $recommendation = $service->getRestockRecommendation($barang);
                        return response()->json([
                            'success' => true,
                            'data' => $recommendation
                        ]);
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => $e->getMessage()
                        ]);
                    }
                })->name('eoq-status');
            });
        });
    });

    // ============================================================================
    // 💰 KASIR ROUTES - CASHIER & TRANSACTION MANAGEMENT  
    // ============================================================================
    
    Route::middleware([RoleMiddleware::class . ':Service Advisor'])->group(function () {
        // Kasir Dashboard
        Route::get('/dashboard-kasir', [KasirController::class, 'index'])->name('dashboard.kasir');
        
        // Kasir specific routes with prefix
        Route::prefix('kasir')->name('kasir.')->group(function () {
            
            // === TRANSAKSI SERVICE ROUTES ===
            Route::get('/transaksi-service', [TransaksiServiceController::class, 'index'])->name('transaksi-service');
            
            // API routes for transaksi service
            Route::get('/transaksi-service/search-barang', [TransaksiServiceController::class, 'searchBarang'])->name('transaksi-service.search-barang');
            Route::post('/transaksi-service/create', [TransaksiServiceController::class, 'createTransaksi'])->name('transaksi-service.create');
            Route::get('/transaksi-service/{id}/detail', [TransaksiServiceController::class, 'getTransaksiDetail'])->name('transaksi-service.detail');
            Route::post('/transaksi-service/{id}/add-items', [TransaksiServiceController::class, 'addItemsToTransaksi'])->name('transaksi-service.add-items');
            Route::post('/transaksi-service/{id}/complete', [TransaksiServiceController::class, 'completeTransaksi'])->name('transaksi-service.complete');
            Route::get('/transaksi-service/validation-status', [TransaksiServiceController::class, 'getValidationStatus'])->name('transaksi-service.validation-status');
            Route::post('/transaksi-service/{id}/cancel', [TransaksiServiceController::class, 'cancelTransaksi'])->name('transaksi-service.cancel');
            
            Route::get('/transaksi-service/recent-transactions', [TransaksiServiceController::class, 'getRecentTransactions'])->name('transaksi-service.recent');

            Route::post('/transaksi-service/search-nota', [TransaksiServiceController::class, 'searchNota'])->name('transaksi-service.search-nota');
            Route::post('/transaksi-service/add-to-existing', [TransaksiServiceController::class, 'addItemsToExistingNota'])->name('transaksi-service.add-to-existing');
            Route::get('/transaksi-service/{id}/print', [TransaksiServiceController::class, 'printNota'])->name('transaksi-service.print');

            // === SEARCH BARANG ROUTES ===
            Route::get('/search-barang', [SearchBarangController::class, 'index'])->name('search-barang');
            Route::get('/search-barang/search', [SearchBarangController::class, 'searchBarang'])->name('search-barang.search');
            Route::get('/search-barang/{id}/detail', [SearchBarangController::class, 'getBarangDetail'])->name('search-barang.detail');
            Route::get('/search-barang/kategori-options', [SearchBarangController::class, 'getKategoriOptions'])->name('search-barang.kategori-options');
            Route::get('/search-barang/suggestions', [SearchBarangController::class, 'getQuickSuggestions'])->name('search-barang.suggestions');
            Route::get('/search-barang/stats', [SearchBarangController::class, 'getSearchStats'])->name('search-barang.stats');
            
            // === HISTORY TRANSAKSI ROUTES ===
            Route::get('/history-transaksi', [HistoryTransaksiController::class, 'index'])->name('history-transaksi');
            Route::get('/history-transaksi/data', [HistoryTransaksiController::class, 'getHistory'])->name('history-transaksi.data');
            Route::get('/history-transaksi/{id}/detail', [HistoryTransaksiController::class, 'getDetailTransaksi'])->name('history-transaksi.detail');
            Route::get('/history-transaksi/stats', [HistoryTransaksiController::class, 'getTransaksiStats'])->name('history-transaksi.stats');
            Route::get('/history-transaksi/export', [HistoryTransaksiController::class, 'exportHistory'])->name('history-transaksi.export');
        });
    });
});

// ============================================================================
// 🔧 ADMIN BACKGROUND JOB ROUTES (OPTIONAL)
// ============================================================================

Route::prefix('admin/jobs')->name('admin.jobs.')->middleware(['auth', 'check.active', 'role:Owner'])->group(function () {
    Route::post('/update-eoq-all', function() {
        \App\Jobs\UpdateEOQCalculations::dispatch(null, true);
        return response()->json([
            'success' => true,
            'message' => 'EOQ update job queued for all items'
        ]);
    })->name('update-eoq-all');
    
    Route::post('/update-eoq/{id}', function($id) {
        \App\Jobs\UpdateEOQCalculations::dispatch($id, true);
        return response()->json([
            'success' => true,
            'message' => "EOQ update job queued for item {$id}"
        ]);
    })->name('update-eoq-item');
});

// ============================================================================
// 📋 ADDITIONAL HELPER ROUTES
// ============================================================================

// Quick role check route for frontend
Route::middleware(['auth', 'check.active'])->get('/auth/role', function () {
    return response()->json([
        'role' => Auth::user()->role_name,
        'permissions' => [
            'is_owner' => Auth::user()->isOwner(),
            'is_gudang' => Auth::user()->isGudang(), 
            'is_kasir' => Auth::user()->isServiceAdvisor()
        ]
    ]);
})->name('auth.role');

// ============================================================================
// ❌ DISABLE OLD AUTH SYSTEM - COMMENT OUT THIS LINE
// ============================================================================

// require __DIR__.'/auth.php'; // ← COMMENT OUT OR REMOVE THIS LINE