<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Services\EOQCalculationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateEOQ extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'eoq:calculate 
                           {--item= : Calculate EOQ for specific item ID}
                           {--all : Calculate EOQ for all items}
                           {--force : Force recalculation even if recently calculated}
                           {--auto-params : Auto-calculate missing parameters from history}
                           {--dry-run : Show what would be calculated without saving}';

    /**
     * The console command description.
     */
    protected $description = 'Calculate EOQ (Economic Order Quantity) for inventory items';

    protected $eoqService;

    public function __construct(EOQCalculationService $eoqService)
    {
        parent::__construct();
        $this->eoqService = $eoqService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧮 EOQ Calculation Started');
        $this->info('Time: ' . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        try {
            if ($this->option('item')) {
                return $this->calculateForItem($this->option('item'));
            } elseif ($this->option('all')) {
                return $this->calculateForAllItems();
            } else {
                $this->error('Please specify --item=ID or --all option');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('EOQ Calculation failed: ' . $e->getMessage());
            Log::error('EOQ Command failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Calculate EOQ for specific item
     */
    protected function calculateForItem($itemId)
    {
        $this->info("📦 Calculating EOQ for item ID: {$itemId}");

        try {
            $barang = Barang::findOrFail($itemId);
            $this->info("Item: {$barang->nama_barang} ({$barang->kode_barang})");

            // Auto-calculate parameters if requested
            if ($this->option('auto-params')) {
                $this->autoCalculateParameters($barang);
            }

            // Check if parameters are complete
            if (!$this->hasCompleteParameters($barang)) {
                $this->warn('⚠️  Missing EOQ parameters:');
                $this->showMissingParameters($barang);
                
                if (!$this->option('auto-params')) {
                    $this->info('💡 Use --auto-params to calculate missing parameters from history');
                }
                return Command::FAILURE;
            }

            // Show current parameters
            $this->showParameters($barang);

            // Calculate EOQ
            if ($this->option('dry-run')) {
                $this->info('🔍 DRY RUN - No changes will be saved');
            }

            $result = $this->calculateEOQ($barang);

            if ($result['success']) {
                $this->info('✅ EOQ calculation successful!');
                $this->showResults($result);
                return Command::SUCCESS;
            } else {
                $this->error('❌ EOQ calculation failed: ' . $result['error']);
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("Error processing item {$itemId}: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Calculate EOQ for all items
     */
    protected function calculateForAllItems()
    {
        $this->info('📊 Calculating EOQ for all items');

        $query = Barang::query();
        
        if (!$this->option('force')) {
            // Only items not calculated recently (within 6 hours)
            $query->where(function($q) {
                $q->whereNull('last_eoq_calculation')
                  ->orWhere('last_eoq_calculation', '<', now()->subHours(6));
            });
        }

        $items = $query->get();
        $this->info("Found {$items->count()} items to process");

        if ($items->isEmpty()) {
            $this->info('🎉 All items are up to date!');
            return Command::SUCCESS;
        }

        $this->newLine();
        $bar = $this->output->createProgressBar($items->count());
        $bar->setFormat('verbose');

        $successCount = 0;
        $failedCount = 0;
        $skippedCount = 0;
        $failedItems = [];

        foreach ($items as $barang) {
            $bar->advance();

            try {
                // Auto-calculate parameters if requested
                if ($this->option('auto-params')) {
                    $this->autoCalculateParameters($barang, false); // Silent mode
                }

                // Check if parameters are complete
                if (!$this->hasCompleteParameters($barang)) {
                    $skippedCount++;
                    continue;
                }

                $result = $this->calculateEOQ($barang);

                if ($result['success']) {
                    $successCount++;
                } else {
                    $failedCount++;
                    $failedItems[] = "{$barang->nama_barang}: {$result['error']}";
                }

            } catch (\Exception $e) {
                $failedCount++;
                $failedItems[] = "{$barang->nama_barang}: {$e->getMessage()}";
            }
        }

        $bar->finish();
        $this->newLine(2);

        // Show summary
        $this->info('📈 EOQ Calculation Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Successful', $successCount],
                ['❌ Failed', $failedCount],
                ['⏭️ Skipped (missing params)', $skippedCount],
                ['📊 Total Processed', $items->count()]
            ]
        );

        // Show failed items
        if (!empty($failedItems)) {
            $this->newLine();
            $this->warn('❌ Failed Items:');
            foreach (array_slice($failedItems, 0, 10) as $failed) {
                $this->line("  • {$failed}");
            }
            if (count($failedItems) > 10) {
                $this->line("  ... and " . (count($failedItems) - 10) . " more");
            }
        }

        return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Auto-calculate missing parameters from history
     */
    protected function autoCalculateParameters(Barang $barang, $verbose = true)
    {
        if ($verbose) {
            $this->info('🤖 Auto-calculating parameters from history...');
        }

        try {
            $demandData = $this->eoqService->calculateDemandFromHistory($barang, 365);
            
            if ($demandData['total_usage'] <= 0) {
                if ($verbose) {
                    $this->warn('⚠️  No historical transaction data found');
                }
                return false;
            }

            // Estimate costs
            $estimatedOrderingCost = max($barang->harga_beli * 0.1, 25000);
            $estimatedHoldingCost = 15; // 15% annual

            $updates = [];
            if (!$barang->annual_demand) $updates['annual_demand'] = $demandData['annual_demand'];
            if (!$barang->demand_avg_daily) $updates['demand_avg_daily'] = $demandData['avg_daily_demand'];
            if (!$barang->demand_max_daily) $updates['demand_max_daily'] = $demandData['max_daily_demand'];
            if (!$barang->ordering_cost) $updates['ordering_cost'] = $estimatedOrderingCost;
            if (!$barang->holding_cost) $updates['holding_cost'] = $estimatedHoldingCost;
            if (!$barang->lead_time) $updates['lead_time'] = 7;

            if (!empty($updates) && !$this->option('dry-run')) {
                $barang->update($updates);
            }

            if ($verbose) {
                $this->info('✅ Parameters updated from ' . $demandData['period_days'] . ' days of history');
                foreach ($updates as $field => $value) {
                    $this->line("  • {$field}: {$value}");
                }
            }

            return true;

        } catch (\Exception $e) {
            if ($verbose) {
                $this->warn("Failed to auto-calculate parameters: {$e->getMessage()}");
            }
            return false;
        }
    }

    /**
     * Check if item has complete EOQ parameters
     */
    protected function hasCompleteParameters(Barang $barang): bool
    {
        return !is_null($barang->annual_demand) && $barang->annual_demand > 0 &&
               !is_null($barang->ordering_cost) && $barang->ordering_cost > 0 &&
               !is_null($barang->holding_cost) && $barang->holding_cost > 0 &&
               !is_null($barang->lead_time) && $barang->lead_time > 0 &&
               !is_null($barang->demand_avg_daily) && $barang->demand_avg_daily > 0 &&
               !is_null($barang->demand_max_daily) && $barang->demand_max_daily > 0;
    }

    /**
     * Show missing parameters
     */
    protected function showMissingParameters(Barang $barang)
    {
        $missing = [];
        if (!$barang->annual_demand || $barang->annual_demand <= 0) $missing[] = 'annual_demand';
        if (!$barang->ordering_cost || $barang->ordering_cost <= 0) $missing[] = 'ordering_cost';
        if (!$barang->holding_cost || $barang->holding_cost <= 0) $missing[] = 'holding_cost';
        if (!$barang->lead_time || $barang->lead_time <= 0) $missing[] = 'lead_time';
        if (!$barang->demand_avg_daily || $barang->demand_avg_daily <= 0) $missing[] = 'demand_avg_daily';
        if (!$barang->demand_max_daily || $barang->demand_max_daily <= 0) $missing[] = 'demand_max_daily';

        foreach ($missing as $param) {
            $this->line("  • {$param}");
        }
    }

    /**
     * Show current parameters
     */
    protected function showParameters(Barang $barang)
    {
        $this->info('📋 Current Parameters:');
        
        // Get raw holding cost percentage from database
        $holdingCostPct = $barang->holding_cost ?? 0;  // This should be 10.00
        $holdingCostAbs = ($holdingCostPct / 100) * $barang->harga_beli;
        
        $this->table(
            ['Parameter', 'Value'],
            [
                ['Annual Demand', number_format($barang->annual_demand ?? 0)],
                ['Ordering Cost', 'Rp ' . number_format($barang->ordering_cost ?? 0)],
                ['Holding Cost %', $holdingCostPct . '%'],  // Should show 10.00%
                ['Holding Cost Absolute', 'Rp ' . number_format($holdingCostAbs)],
                ['Lead Time', ($barang->lead_time ?? 0) . ' days'],
                ['Avg Daily Demand', number_format($barang->demand_avg_daily ?? 0, 2)],
                ['Max Daily Demand', number_format($barang->demand_max_daily ?? 0, 2)],
                ['Item Price', 'Rp ' . number_format($barang->harga_beli ?? 0)]
            ]
        );
    }

    /**
     * Calculate EOQ with proper service integration
     */
    protected function calculateEOQ(Barang $barang): array
    {
        if ($this->option('dry-run')) {
            // Manual calculation for dry-run (no DB save)
            try {
                $D = $barang->annual_demand;
                $S = $barang->ordering_cost;
                $H_percentage = $barang->holding_cost;
                $H = ($H_percentage / 100) * $barang->harga_beli;
                
                if (!$D || !$S || !$H || $D <= 0 || $S <= 0 || $H <= 0) {
                    return [
                        'success' => false,
                        'error' => "Invalid parameters: D={$D}, S={$S}, H%={$H_percentage}"
                    ];
                }
                
                $eoq = sqrt((2 * $D * $S) / $H);
                
                // Safety stock
                $dmax = $barang->demand_max_daily ?? $barang->demand_avg_daily * 1.5;
                $davg = $barang->demand_avg_daily;
                $leadTime = $barang->lead_time;
                $safetyStock = ($dmax - $davg) * sqrt($leadTime);
                
                // ROP
                $rop = ($davg * $leadTime) + $safetyStock;
                
                return [
                    'success' => true,
                    'eoq' => [
                        'eoq' => round($eoq),
                        'formula' => "√(2×{$D}×{$S}/{$H}) = " . round($eoq)
                    ],
                    'safety_stock' => [
                        'safety_stock' => round($safetyStock),
                        'formula' => "({$dmax} - {$davg}) × √{$leadTime} = " . round($safetyStock)
                    ],
                    'rop' => [
                        'rop' => round($rop),
                        'formula' => "({$davg} × {$leadTime}) + {$safetyStock} = " . round($rop)
                    ]
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        } else {
            // Normal calculation with service
            return $this->eoqService->calculateAll($barang);
        }
    }

    /**
     * Show calculation results
     */
    protected function showResults(array $result)
    {
        if (!$result['success']) {
            return;
        }

        $this->newLine();
        $this->info('📊 EOQ Calculation Results:');
        
        $this->table(
            ['Metric', 'Value', 'Formula'],
            [
                [
                    'EOQ (Economic Order Quantity)', 
                    $result['eoq']['eoq'], 
                    $result['eoq']['formula']
                ],
                [
                    'Safety Stock', 
                    $result['safety_stock']['safety_stock'], 
                    $result['safety_stock']['formula']
                ],
                [
                    'ROP (Reorder Point)', 
                    $result['rop']['rop'], 
                    $result['rop']['formula']
                ]
            ]
        );

        // Fix cost analysis - calculate H here
        $barang = Barang::find($this->getItemId()); // Add this helper method
        $holdingCostPct = $barang->holding_cost ?? 0;
        $H = ($holdingCostPct / 100) * $barang->harga_beli;
        
        $this->info('💰 Cost Analysis:');
        $this->line("  • Holding Cost %: {$holdingCostPct}%");
        $this->line("  • Holding Cost (absolute): Rp " . number_format($H));
        $this->line("  • Current Stock: " . ($barang->stok->jumlah_stok ?? 0));
        
        if (!$this->option('dry-run')) {
            $this->info('💾 Results saved to database');
        }
    }

    // Add helper method
    private function getItemId()
    {
        return $this->argument('item') ?? null;
    }
}