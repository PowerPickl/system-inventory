<?php

namespace App\Jobs;

use App\Models\Barang;
use App\Services\EOQCalculationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateEOQCalculations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $itemId;
    protected $forceUpdate;

    /**
     * Create a new job instance.
     */
    public function __construct($itemId = null, $forceUpdate = false)
    {
        $this->itemId = $itemId;
        $this->forceUpdate = $forceUpdate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new EOQCalculationService();
        
        try {
            if ($this->itemId) {
                // Update specific item
                $barang = Barang::findOrFail($this->itemId);
                $this->updateSingleItem($barang, $service);
            } else {
                // Update all items
                $this->updateAllItems($service);
            }
        } catch (\Exception $e) {
            Log::error('EOQ Calculation Job Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update single item EOQ
     */
    protected function updateSingleItem(Barang $barang, EOQCalculationService $service)
    {
        try {
            // Auto-update demand from transaction history
            $demandData = $service->calculateDemandFromHistory($barang, 365);
            
            // Update demand fields
            $barang->update([
                'annual_demand' => $demandData['annual_demand'],
                'demand_avg_daily' => $demandData['avg_daily_demand'],
                'demand_max_daily' => $demandData['max_daily_demand']
            ]);

            // Calculate EOQ with updated demand
            $result = $service->calculateAll($barang);
            
            if ($result['success']) {
                Log::info("EOQ updated for {$barang->nama_barang}: EOQ={$result['summary']['eoq']}, ROP={$result['summary']['rop']}");
            } else {
                Log::warning("EOQ calculation failed for {$barang->nama_barang}: {$result['error']}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to update EOQ for item {$barang->id_barang}: " . $e->getMessage());
        }
    }

    /**
     * Update all items EOQ
     */
    protected function updateAllItems(EOQCalculationService $service)
    {
        $items = Barang::whereNotNull('annual_demand')
                      ->whereNotNull('holding_cost')
                      ->get();

        $successCount = 0;
        $errorCount = 0;

        foreach ($items as $barang) {
            try {
                // Skip if recently updated (unless forced)
                if (!$this->forceUpdate && $barang->last_eoq_calculation && 
                    $barang->last_eoq_calculation->diffInHours(now()) < 6) {
                    continue;
                }

                $this->updateSingleItem($barang, $service);
                $successCount++;

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Batch EOQ update failed for {$barang->nama_barang}: " . $e->getMessage());
            }
        }

        Log::info("EOQ Batch Update Completed: {$successCount} success, {$errorCount} errors");
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('EOQ Calculation Job Failed Completely: ' . $exception->getMessage());
    }
}