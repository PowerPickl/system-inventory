<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\LogStok;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EOQCalculationService
{
    /**
     * Calculate EOQ for a specific item
     * EOQ = √(2DS/H)
     * Where:
     * - D = Annual demand
     * - S = Ordering cost per order
     * - H = Holding cost per unit per year (in absolute currency, not percentage)
     */
    public function calculateEOQ(Barang $barang): array
    {
        $D = $barang->annual_demand;
        $S = $barang->ordering_cost;
        $H_percentage = $barang->holding_cost; // This is percentage
        
        // Convert holding cost percentage to absolute value
        $H = ($H_percentage / 100) * $barang->harga_beli;

        if (!$D || !$S || !$H || $D <= 0 || $S <= 0 || $H <= 0) {
            throw new \Exception("Invalid EOQ parameters: D={$D}, S={$S}, H={$H} (H%={$H_percentage}), Price={$barang->harga_beli}");
        }

        $eoq = sqrt((2 * $D * $S) / $H);
       
        Log::info("EOQ Calculation Debug:", [
            'barang_id' => $barang->id_barang,
            'D' => $D,
            'S' => $S,
            'H_percentage' => $H_percentage,
            'H_absolute' => $H,
            'harga_beli' => $barang->harga_beli,
            'eoq_result' => $eoq
        ]);
        
        return [
            'eoq' => round($eoq),
            'D' => $D,
            'S' => $S,
            'H' => $H,
            'H_percentage' => $H_percentage,
            'formula' => "√(2×{$D}×{$S}/{$H}) = " . round($eoq)
        ];
    }

    /**
     * Calculate Safety Stock
     * SS = Z × σ × √LT 
     * Simplified: (dmax - davg) × √LT
     */
    public function calculateSafetyStock(Barang $barang): array
    {
        $dmax = $barang->demand_max_daily;
        $davg = $barang->demand_avg_daily; 
        $leadTime = $barang->lead_time;

        if (!$dmax || !$davg || !$leadTime || $dmax <= 0 || $davg <= 0 || $leadTime <= 0) {
            throw new \Exception("Invalid Safety Stock parameters: dmax={$dmax}, davg={$davg}, LT={$leadTime}");
        }

        // Ensure dmax is greater than davg
        if ($dmax <= $davg) {
            $dmax = $davg * 1.5; // Set dmax to 150% of average if not provided correctly
            Log::warning("dmax was <= davg, adjusted to {$dmax} for barang {$barang->id_barang}");
        }

        // FIXED: Safety stock calculation (REMOVED sqrt!)
        $safetyStock = ($dmax - $davg) * $leadTime;
        
        return [
            'safety_stock' => round($safetyStock),
            'dmax' => $dmax,
            'davg' => $davg,
            'lead_time' => $leadTime,
            'formula' => "({$dmax} - {$davg}) × {$leadTime} = " . round($safetyStock)
        ];
    }

    /**
     * Calculate ROP (Reorder Point)
     * ROP = (davg × LT) + SS
     */
    public function calculateROP(Barang $barang): array
    {
        $davg = $barang->demand_avg_daily;
        $leadTime = $barang->lead_time;
        
        if (!$davg || !$leadTime || $davg <= 0 || $leadTime <= 0) {
            throw new \Exception("Invalid ROP parameters: davg={$davg}, LT={$leadTime}");
        }
        
        // Calculate or get safety stock
        $safetyStockResult = $this->calculateSafetyStock($barang);
        $safetyStock = $safetyStockResult['safety_stock'];

        $rop = ($davg * $leadTime) + $safetyStock;

        return [
            'rop' => round($rop),
            'davg' => $davg,
            'lead_time' => $leadTime,
            'safety_stock' => $safetyStock,
            'formula' => "({$davg} × {$leadTime}) + {$safetyStock} = " . round($rop)
        ];
    }

    public function debugCalculation(Barang $barang): array
    {
        // Unit consistency check
        $monthlyFromAnnual = $barang->annual_demand / 12;
        $dailyFromAnnual = $barang->annual_demand / 365;
        
        $unitMismatch = abs($dailyFromAnnual - $barang->demand_avg_daily) > ($barang->demand_avg_daily * 0.3);
        
        if ($unitMismatch) {
            Log::warning("Possible unit mismatch!", [
                'barang_id' => $barang->id_barang,
                'annual_demand' => $barang->annual_demand,
                'daily_from_annual' => round($dailyFromAnnual, 2),
                'stored_daily_avg' => $barang->demand_avg_daily,
                'difference_percent' => round(abs($dailyFromAnnual - $barang->demand_avg_daily) / $barang->demand_avg_daily * 100, 1)
            ]);
        }
        
        return [
            'inputs' => [
                'annual_demand' => $barang->annual_demand,
                'monthly_from_annual' => round($monthlyFromAnnual, 2),
                'daily_from_annual' => round($dailyFromAnnual, 2),
                'daily_avg_stored' => $barang->demand_avg_daily,
                'daily_max_stored' => $barang->demand_max_daily,
                'ordering_cost' => $barang->ordering_cost,
                'holding_cost_percent' => $barang->holding_cost,
                'holding_cost_absolute' => round(($barang->holding_cost/100) * $barang->harga_beli, 2),
                'lead_time' => $barang->lead_time,
                'unit_mismatch_detected' => $unitMismatch
            ],
            'calculations' => $this->calculateAll($barang)
        ];
    }



    /**
     * Calculate all metrics for an item with better error handling
     */
    public function calculateAll(Barang $barang): array
    {
        try {
            // Validate all required parameters first
            $this->validateEOQParameters($barang);

            $eoqResult = $this->calculateEOQ($barang);
            $ssResult = $this->calculateSafetyStock($barang);
            $ropResult = $this->calculateROP($barang);

            // Update barang dengan hasil calculation
            $barang->update([
                'eoq_calculated' => $eoqResult['eoq'],
                'safety_stock' => $ssResult['safety_stock'],
                'rop_calculated' => $ropResult['rop'],
                'reorder_point' => $ropResult['rop'], // For backward compatibility
                'last_eoq_calculation' => now()
            ]);

            return [
                'success' => true,
                'eoq' => $eoqResult,
                'safety_stock' => $ssResult,
                'rop' => $ropResult,
                'summary' => [
                    'item' => $barang->nama_barang,
                    'eoq' => $eoqResult['eoq'],
                    'safety_stock' => $ssResult['safety_stock'],
                    'rop' => $ropResult['rop'],
                    'current_stock' => $barang->stok->jumlah_stok ?? 0
                ]
            ];

        } catch (\Exception $e) {
            Log::error("EOQ calculation failed for barang {$barang->id_barang}: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate EOQ parameters before calculation
     */
    private function validateEOQParameters(Barang $barang): void
    {
        $required = [
            'annual_demand' => $barang->annual_demand,
            'ordering_cost' => $barang->ordering_cost,
            'holding_cost' => $barang->holding_cost,
            'lead_time' => $barang->lead_time,
            'demand_avg_daily' => $barang->demand_avg_daily,
            'demand_max_daily' => $barang->demand_max_daily,
            'harga_beli' => $barang->harga_beli
        ];

        $missing = [];
        $invalid = [];

        foreach ($required as $field => $value) {
            if (is_null($value)) {
                $missing[] = $field;
            } elseif ($value <= 0) {
                $invalid[] = "{$field} (value: {$value})";
            }
        }

        if (!empty($missing)) {
            throw new \Exception("Missing EOQ parameters: " . implode(', ', $missing));
        }

        if (!empty($invalid)) {
            throw new \Exception("Invalid EOQ parameters (must be > 0): " . implode(', ', $invalid));
        }
    }

    /**
     * Auto-calculate demand berdasarkan transaksi history dengan analisis yang lebih baik
     */
    public function calculateDemandFromHistory(Barang $barang, int $days = 365): array
    {
        $startDate = Carbon::now()->subDays($days);
        
        // Total usage dari log stok (keluar)
        $totalUsage = LogStok::where('id_barang', $barang->id_barang)
            ->where('jenis_perubahan', 'Keluar')
            ->where('tanggal_log', '>=', $startDate)
            ->sum('qty_perubahan');

        $totalUsage = abs($totalUsage); // Make positive

        // FIXED: Better annual conversion
        $avgDailyDemand = $totalUsage > 0 ? $totalUsage / $days : 0;
        $annualDemand = $avgDailyDemand * 365; // More accurate
        
        // Get daily usage patterns for better max calculation
        $dailyUsages = LogStok::where('id_barang', $barang->id_barang)
            ->where('jenis_perubahan', 'Keluar')
            ->where('tanggal_log', '>=', $startDate)
            ->selectRaw('DATE(tanggal_log) as date, SUM(ABS(qty_perubahan)) as daily_usage')
            ->groupBy('date')
            ->orderBy('daily_usage', 'desc')
            ->get();

        // Calculate statistics for better demand estimation
        $maxDailyUsage = $dailyUsages->first();
        $maxDailyDemand = $maxDailyUsage ? $maxDailyUsage->daily_usage : $avgDailyDemand * 2;
        
        // If we have enough data points, use statistical approach
        if ($dailyUsages->count() >= 30) {
            $usageValues = $dailyUsages->pluck('daily_usage')->toArray();
            $mean = array_sum($usageValues) / count($usageValues);
            $variance = array_sum(array_map(function($x) use ($mean) { return pow($x - $mean, 2); }, $usageValues)) / count($usageValues);
            $stdDev = sqrt($variance);
            
            // Use mean + 2 standard deviations as max demand (covers ~95% of cases)
            $statisticalMax = $mean + (2 * $stdDev);
            $maxDailyDemand = max($maxDailyDemand, $statisticalMax);
        }

        // Ensure max is reasonable compared to average
        if ($avgDailyDemand > 0) {
            $maxDailyDemand = max($maxDailyDemand, $avgDailyDemand * 1.5); // At least 150% of average
            $maxDailyDemand = min($maxDailyDemand, $avgDailyDemand * 5);   // Not more than 500% of average
        }

        // ADDED: Debug logging
        Log::info("Demand calculation from history:", [
            'barang_id' => $barang->id_barang,
            'days_analyzed' => $days,
            'total_usage' => $totalUsage,
            'avg_daily_calculated' => round($avgDailyDemand, 2),
            'annual_demand_calculated' => round($annualDemand, 2),
            'max_daily_calculated' => round($maxDailyDemand, 2)
        ]);

        return [
            'period_days' => $days,
            'total_usage' => $totalUsage,
            'annual_demand' => round($annualDemand, 2),
            'avg_daily_demand' => round($avgDailyDemand, 2),
            'max_daily_demand' => round($maxDailyDemand, 2),
            'data_points' => $dailyUsages->count(),
            'has_sufficient_data' => $dailyUsages->count() >= 30
        ];
    }
    /**
     * Bulk calculate untuk semua barang dengan progress tracking
     */
    public function calculateAllItems(callable $progressCallback = null): array
    {
        $results = [];
        $barangs = Barang::whereNotNull('annual_demand')
                         ->whereNotNull('ordering_cost')
                         ->whereNotNull('holding_cost')
                         ->get();

        $total = $barangs->count();
        $processed = 0;

        foreach ($barangs as $barang) {
            try {
                $results[$barang->id_barang] = $this->calculateAll($barang);
                $processed++;
                
                if ($progressCallback) {
                    $progressCallback($processed, $total, $barang);
                }
            } catch (\Exception $e) {
                $results[$barang->id_barang] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get restock recommendation berdasarkan EOQ dengan urgency levels
     */
    public function getRestockRecommendation(Barang $barang): array
    {
        $currentStock = $barang->stok->jumlah_stok ?? 0;
        $rop = $barang->rop_calculated ?? $barang->reorder_point ?? 0;
        $eoq = $barang->eoq_calculated ?? $barang->eoq_qty ?? 0;
        $safetyStock = $barang->safety_stock ?? 0;

        $needRestock = $currentStock <= $rop;
        $recommendedQty = $needRestock ? $eoq : 0;

        // Calculate urgency level
        $urgency = 'Normal';
        $urgencyScore = 0;

        if ($currentStock <= 0) {
            $urgency = 'Critical';
            $urgencyScore = 4;
        } elseif ($currentStock <= $safetyStock) {
            $urgency = 'High';
            $urgencyScore = 3;
        } elseif ($currentStock <= $rop * 0.5) {
            $urgency = 'High';
            $urgencyScore = 3;
        } elseif ($currentStock <= $rop) {
            $urgency = 'Medium';
            $urgencyScore = 2;
        } elseif ($currentStock <= $rop * 1.2) {
            $urgency = 'Low';
            $urgencyScore = 1;
        }

        // Calculate days until stockout (estimated)
        $avgDailyDemand = $barang->demand_avg_daily ?? 0;
        $daysUntilStockout = $avgDailyDemand > 0 ? $currentStock / $avgDailyDemand : null;

        return [
            'need_restock' => $needRestock,
            'current_stock' => $currentStock,
            'reorder_point' => $rop,
            'safety_stock' => $safetyStock,
            'recommended_qty' => $recommendedQty,
            'eoq' => $eoq,
            'urgency' => $urgency,
            'urgency_score' => $urgencyScore,
            'days_until_stockout' => $daysUntilStockout ? round($daysUntilStockout, 1) : null,
            'stock_coverage_days' => $daysUntilStockout,
            'overstocked' => $currentStock > ($rop * 2), // Flag if stock is too high
        ];
    }

    /**
     * Calculate optimal order timing
     */
    public function calculateOrderTiming(Barang $barang): array
    {
        $eoq = $barang->eoq_calculated ?? 0;
        $avgDailyDemand = $barang->demand_avg_daily ?? 0;
        $leadTime = $barang->lead_time ?? 0;
        $orderingCost = $barang->ordering_cost ?? 0;
        $holdingCost = ($barang->holding_cost / 100) * $barang->harga_beli ?? 0;

        if ($eoq <= 0 || $avgDailyDemand <= 0) {
            return [
                'error' => 'Insufficient data for timing calculation'
            ];
        }

        // Calculate order frequency
        $ordersPerYear = ($barang->annual_demand ?? 0) / $eoq;
        $daysBetweenOrders = $ordersPerYear > 0 ? 365 / $ordersPerYear : 0;

        // Calculate total costs
        $totalOrderingCost = $ordersPerYear * $orderingCost;
        $totalHoldingCost = ($eoq / 2) * $holdingCost;
        $totalCost = $totalOrderingCost + $totalHoldingCost;

        return [
            'eoq' => $eoq,
            'orders_per_year' => round($ordersPerYear, 2),
            'days_between_orders' => round($daysBetweenOrders, 1),
            'total_ordering_cost' => round($totalOrderingCost, 2),
            'total_holding_cost' => round($totalHoldingCost, 2),
            'total_annual_cost' => round($totalCost, 2),
            'cost_per_unit' => $barang->annual_demand > 0 ? round($totalCost / $barang->annual_demand, 2) : 0
        ];
    }

    /**
     * Validate and suggest EOQ parameter improvements
     */
    public function validateAndSuggestImprovements(Barang $barang): array
    {
        $suggestions = [];
        $warnings = [];

        // Check if parameters seem reasonable
        if ($barang->holding_cost && ($barang->holding_cost < 5 || $barang->holding_cost > 50)) {
            $warnings[] = "Holding cost ({$barang->holding_cost}%) seems unusual. Typical range: 5-50%";
        }

        if ($barang->lead_time && ($barang->lead_time < 1 || $barang->lead_time > 90)) {
            $warnings[] = "Lead time ({$barang->lead_time} days) seems unusual. Typical range: 1-90 days";
        }

        if ($barang->demand_max_daily && $barang->demand_avg_daily) {
            $ratio = $barang->demand_max_daily / $barang->demand_avg_daily;
            if ($ratio < 1.2) {
                $suggestions[] = "Max daily demand should be at least 20% higher than average";
            } elseif ($ratio > 5) {
                $warnings[] = "Max daily demand is very high compared to average - consider reviewing data";
            }
        }

        // Check for missing historical data
        $demandData = $this->calculateDemandFromHistory($barang, 90);
        if (!$demandData['has_sufficient_data']) {
            $suggestions[] = "Limited historical data ({$demandData['data_points']} days). Consider collecting more transaction history";
        }

        return [
            'suggestions' => $suggestions,
            'warnings' => $warnings,
            'data_quality_score' => $this->calculateDataQualityScore($barang, $demandData)
        ];
    }

    /**
     * Calculate data quality score for EOQ parameters
     */
    private function calculateDataQualityScore(Barang $barang, array $demandData): int
    {
        $score = 0;

        // Check completeness (40 points)
        $requiredFields = ['annual_demand', 'ordering_cost', 'holding_cost', 'lead_time', 'demand_avg_daily', 'demand_max_daily'];
        $completedFields = 0;
        foreach ($requiredFields as $field) {
            if (!is_null($barang->$field) && $barang->$field > 0) {
                $completedFields++;
            }
        }
        $score += ($completedFields / count($requiredFields)) * 40;

        // Check historical data availability (30 points)
        if ($demandData['has_sufficient_data']) {
            $score += 30;
        } elseif ($demandData['data_points'] > 10) {
            $score += 15;
        } elseif ($demandData['data_points'] > 0) {
            $score += 5;
        }

        // Check parameter reasonableness (30 points)
        $reasonableParams = 0;
        if ($barang->holding_cost >= 5 && $barang->holding_cost <= 50) $reasonableParams++;
        if ($barang->lead_time >= 1 && $barang->lead_time <= 90) $reasonableParams++;
        if ($barang->demand_max_daily && $barang->demand_avg_daily && 
            ($barang->demand_max_daily / $barang->demand_avg_daily) >= 1.2 && 
            ($barang->demand_max_daily / $barang->demand_avg_daily) <= 5) $reasonableParams++;
        
        $score += ($reasonableParams / 3) * 30;

        return min(100, round($score));
    }

}