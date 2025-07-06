<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OwnerController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\KasirController;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Owner Dashboard
Route::middleware(['auth', RoleMiddleware::class . ':Owner'])
    ->group(function () {
        Route::get('/dashboard-owner', [OwnerController::class, 'index'])
            ->name('dashboard.owner');
    });

// Gudang Dashboard
Route::middleware(['auth', RoleMiddleware::class . ':Gudang'])
    ->group(function () {
        Route::get('/dashboard-gudang', [GudangController::class, 'index'])
            ->name('dashboard.gudang');
    });

// Kasir Dashboard
Route::middleware(['auth', RoleMiddleware::class . ':Kasir'])
    ->group(function () {
        Route::get('/dashboard-kasir', [KasirController::class, 'index'])
            ->name('dashboard.kasir');
    });

Route::middleware(['auth', RoleMiddleware::class . ':Gudang'])
    ->group(function () {
        Route::get('/dashboard-gudang', [GudangController::class, 'index']);
        
        // Monitoring Stock routes
        Route::get('/gudang/monitoring-stock', [App\Http\Controllers\Gudang\MonitoringStockController::class, 'index'])->name('gudang.monitoring-stock');
        Route::post('/gudang/monitoring-stock/update-eoq/{id}', [App\Http\Controllers\Gudang\MonitoringStockController::class, 'updateEOQ']);
        Route::post('/gudang/monitoring-stock/update-all-eoq', [App\Http\Controllers\Gudang\MonitoringStockController::class, 'updateAllEOQ']);
        Route::get('/gudang/monitoring-stock/realtime-data', [App\Http\Controllers\Gudang\MonitoringStockController::class, 'getRealTimeData']);
        Route::get('/gudang/monitoring-stock/eoq-details/{id}', [App\Http\Controllers\Gudang\MonitoringStockController::class, 'getEOQDetails']);
        Route::get('/gudang/monitoring-stock/trends/{id}', [App\Http\Controllers\Gudang\MonitoringStockController::class, 'getStockTrends']);
        Route::post('/gudang/monitoring-stock/quick-restock', [App\Http\Controllers\Gudang\MonitoringStockController::class, 'quickRestockRequest']);

    });

require __DIR__.'/auth.php';
