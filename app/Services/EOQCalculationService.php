<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\LogStok;
use Carbon\Carbon;

class EOQCalculationService
{
    /**
     * Calculate EOQ for a specific item
     * EOQ = √(2DS/H)
     */
    public function calculateEOQ(Barang $barang): array
    {
        $D = $barang->annual_demand;
        $S = $barang->ordering_cost;
        $H = $barang->holding_cost;

        if (!$D || !$S || !$H) {
            throw new \Exception("Missing required data for EOQ calculation: D={$D}, S={$S}, H={$H}");
        }

        $eoq = sqrt((2 * $D * $S) / $H);
        
        return [
            'eoq' => round($eoq),
            'D' => $D,
            'S' => $S,
            'H' => $H,
            'formula' => "√(2×{$D}×{$S}/{$H}) = " . round($eoq)
        ];
    }

    /**
     * Calculate Safety Stock
     * SS = Z × σ × √LT (simplified: (dmax - davg) × √LT)
     */
    public function calculateSafetyStock(Barang $barang): array
    {
        $dmax = $barang->demand_max_daily;
        $davg = $barang->demand_avg_daily; 
        $leadTime = $barang->lead_time;

        if (!$dmax || !$davg || !$leadTime) {
            throw new \Exception("Missing required data for Safety Stock: dmax={$dmax}, davg={$davg}, LT={$leadTime}");
        }

        // Simplified safety stock calculation
        $safetyStock = ($dmax - $davg) * sqrt($leadTime);
        
        return [
            'safety_stock' => round($safetyStock),
            'dmax' => $dmax,
            'davg' => $davg,
            'lead_time' => $leadTime,
            'formula' => "({$dmax} - {$davg}) × √{$leadTime} = " . round($safetyStock)
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

    /**
     * Calculate all metrics for an item
     */
    public function calculateAll(Barang $barang): array
    {
        try {
            $eoqResult = $this->calculateEOQ($barang);
            $ssResult = $this->calculateSafetyStock($barang);
            $ropResult = $this->calculateROP($barang);

            // Update barang dengan hasil calculation
            $barang->update([
                'eoq_calculated' => $eoqResult['eoq'],
                'safety_stock' => $ssResult['safety_stock'],
                'rop_calculated' => $ropResult['rop'],
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
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Auto-calculate demand berdasarkan transaksi history
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

        // Calculate metrics
        $annualDemand = ($totalUsage / $days) * 365;
        $avgDailyDemand = $totalUsage / $days;
        
        // Get max daily usage
        $maxDailyUsage = LogStok::where('id_barang', $barang->id_barang)
            ->where('jenis_perubahan', 'Keluar')
            ->where('tanggal_log', '>=', $startDate)
            ->selectRaw('DATE(tanggal_log) as date, SUM(ABS(qty_perubahan)) as daily_usage')
            ->groupBy('date')
            ->orderBy('daily_usage', 'desc')
            ->first();

        $maxDailyDemand = $maxDailyUsage ? $maxDailyUsage->daily_usage : $avgDailyDemand * 1.5;

        return [
            'period_days' => $days,
            'total_usage' => $totalUsage,
            'annual_demand' => round($annualDemand, 2),
            'avg_daily_demand' => round($avgDailyDemand, 2),
            'max_daily_demand' => round($maxDailyDemand, 2)
        ];
    }

    /**
     * Bulk calculate untuk semua barang
     */
    public function calculateAllItems(): array
    {
        $results = [];
        $barangs = Barang::all();

        foreach ($barangs as $barang) {
            $results[$barang->id_barang] = $this->calculateAll($barang);
        }

        return $results;
    }

    /**
     * Get restock recommendation berdasarkan EOQ
     */
    public function getRestockRecommendation(Barang $barang): array
    {
        $currentStock = $barang->stok->jumlah_stok ?? 0;
        $rop = $barang->rop_calculated ?? $barang->reorder_point;
        $eoq = $barang->eoq_calculated ?? $barang->eoq_qty;

        $needRestock = $currentStock <= $rop;
        $recommendedQty = $needRestock ? $eoq : 0;

        return [
            'need_restock' => $needRestock,
            'current_stock' => $currentStock,
            'reorder_point' => $rop,
            'recommended_qty' => $recommendedQty,
            'eoq' => $eoq,
            'urgency' => $currentStock <= 0 ? 'Critical' : ($currentStock <= $rop * 0.5 ? 'High' : 'Normal')
        ];
    }
}