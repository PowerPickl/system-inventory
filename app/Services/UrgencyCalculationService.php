<?php

namespace App\Services;

use App\Models\Barang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UrgencyCalculationService
{
    /**
     * Calculate comprehensive urgency with demand factor
     */
    public function calculateUrgencyLevel(Barang $barang): array
    {
        // Use your model's accessor and proper field names
        $currentStock = $barang->jumlah_stok; // Using your accessor
        $rop = $barang->rop_calculated ?? $barang->reorder_point ?? 0;
        $safetyStock = $barang->safety_stock ?? 0;
        
        // Calculate monthly demand
        $monthlyDemand = ($barang->annual_demand ?? 0) / 12;
        
        // Classify demand level (relative to system)
        $demandLevel = $this->classifyDemandLevel($monthlyDemand);
        
        // Stock urgency (0-100)
        $stockUrgency = $this->calculateStockUrgency($currentStock, $rop, $safetyStock);
        
        // Combine stock + demand for final urgency
        $finalUrgency = $this->calculateFinalUrgency($stockUrgency, $demandLevel);
        
        return [
            'stock_level' => $this->getStockLevel($currentStock, $rop, $safetyStock),
            'demand_level' => $demandLevel,
            'monthly_demand' => round($monthlyDemand, 1),
            'stock_urgency_score' => $stockUrgency,
            'final_urgency' => $finalUrgency['level'],
            'urgency_score' => $finalUrgency['score'],
            'priority_badge' => $this->getPriorityBadge($finalUrgency['level']),
            'auto_reason' => $this->generateAutoReason($barang, $finalUrgency['level'], $demandLevel),
            'action_needed' => $finalUrgency['score'] >= 60,
            'days_until_stockout' => $this->calculateDaysUntilStockout($barang),
            'current_stock' => $currentStock,
            'rop' => $rop,
            'safety_stock' => $safetyStock
        ];
    }
    
    /**
     * Classify demand level using percentile-based approach
     */
    private function classifyDemandLevel($monthlyDemand): string
    {
        // Use cache for 1 hour to avoid recalculating frequently
        $demandDistribution = Cache::remember('demand_distribution', 3600, function () {
            return Barang::whereNotNull('annual_demand')
                         ->where('annual_demand', '>', 0)
                         ->pluck('annual_demand')
                         ->map(fn($annual) => $annual / 12)
                         ->sort()
                         ->values();
        });
        
        if ($demandDistribution->isEmpty()) {
            return 'Unknown';
        }
        
        // Get percentiles
        $count = $demandDistribution->count();
        $percentile80Index = (int) ceil($count * 0.8) - 1;
        $percentile40Index = (int) ceil($count * 0.4) - 1;
        
        $percentile80 = $demandDistribution[$percentile80Index] ?? 0;
        $percentile40 = $demandDistribution[$percentile40Index] ?? 0;
        
        // Debug logging
        Log::debug("Demand classification for {$monthlyDemand}:", [
            'percentile_80' => $percentile80,
            'percentile_40' => $percentile40,
            'total_items' => $count
        ]);
        
        if ($monthlyDemand >= $percentile80) return 'High';
        if ($monthlyDemand >= $percentile40) return 'Medium';
        return 'Low';
    }
    
    /**
     * Calculate stock urgency score (0-100)
     */
    private function calculateStockUrgency($currentStock, $rop, $safetyStock): int
    {
        if ($currentStock <= 0) return 100; // Critical - out of stock
        if ($currentStock <= $safetyStock) return 85; // Very critical
        if ($currentStock <= $rop * 0.5) return 70; // High urgency
        if ($currentStock <= $rop) return 50; // Medium urgency
        if ($currentStock <= $rop * 1.5) return 30; // Low urgency
        return 10; // Safe level
    }
    
    /**
     * Calculate final urgency combining stock + demand
     */
    private function calculateFinalUrgency($stockUrgency, $demandLevel): array
    {
        // Weight: Stock urgency 70%, Demand level 30%
        $demandWeight = match($demandLevel) {
            'High' => 30,
            'Medium' => 20,
            'Low' => 10,
            default => 0
        };
        
        $finalScore = ($stockUrgency * 0.7) + ($demandWeight * 1.0);
        
        $level = match(true) {
            $finalScore >= 80 => 'URGENT',
            $finalScore >= 60 => 'HIGH', 
            $finalScore >= 40 => 'MEDIUM',
            $finalScore >= 20 => 'LOW',
            default => 'NORMAL'
        };
        
        return ['level' => $level, 'score' => round($finalScore, 1)];
    }
    
    /**
     * Get human-readable stock level
     */
    private function getStockLevel($currentStock, $rop, $safetyStock): string
    {
        if ($currentStock <= 0) return 'Out of Stock';
        if ($currentStock <= $safetyStock) return 'Critical Level';
        if ($currentStock <= $rop) return 'Below ROP';
        return 'Safe Level';
    }
    
    /**
     * Calculate estimated days until stockout
     */
    private function calculateDaysUntilStockout(Barang $barang): ?float
    {
        $currentStock = $barang->jumlah_stok; // Using your accessor
        
        // Use your demand_avg_daily field directly
        $avgDailyDemand = $barang->demand_avg_daily ?? 0;
        
        if ($avgDailyDemand <= 0 || $currentStock <= 0) {
            return null;
        }
        
        return round($currentStock / $avgDailyDemand, 1);
    }
    
    /**
     * Generate auto reason based on urgency and demand
     */
    public function generateAutoReason(Barang $barang, $urgencyLevel, $demandLevel): string
    {
        $monthlyDemand = round(($barang->annual_demand ?? 0) / 12, 1);
        $currentStock = $barang->jumlah_stok; // Using your accessor
        $daysUntilStockout = $this->calculateDaysUntilStockout($barang);
        
        $stockoutText = $daysUntilStockout ? " (~{$daysUntilStockout} days until stockout)" : "";
        $namaBarang = $barang->nama_barang;
        
        return match($urgencyLevel) {
            'URGENT' => "🚨 CRITICAL: {$namaBarang} - High demand item ({$monthlyDemand}/month) with critical stock level ({$currentStock}){$stockoutText}. Immediate restock required to avoid service disruption.",
            
            'HIGH' => $demandLevel === 'High' 
                ? "⚠️ HIGH PRIORITY: {$namaBarang} - High demand item ({$monthlyDemand}/month) approaching critical level{$stockoutText}. Restock needed within 24-48 hours."
                : "⚠️ Important: {$namaBarang} - Stock below reorder point for regular demand item{$stockoutText}. Plan restock within 3-5 days.",
                
            'MEDIUM' => "📋 MEDIUM: {$namaBarang} - Moderate demand item ({$monthlyDemand}/month) needs restock{$stockoutText}. Can be scheduled with next procurement cycle.",
            
            'LOW' => "📅 LOW: {$namaBarang} - Low demand item ({$monthlyDemand}/month){$stockoutText}. Restock when convenient or combine with other orders.",
            
            default => "✅ Monitor: {$namaBarang} - Current stock levels are adequate for demand pattern."
        };
    }
    
    /**
     * Get priority badge styling and content
     * Fix: Added missing closing bracket
     */
    public function getPriorityBadge($urgencyLevel): array
    {
        return match($urgencyLevel) {
            'URGENT' => [
                'text' => 'URGENT',
                'class' => 'bg-red-500 text-white animate-pulse border-2 border-red-600',
                'icon' => '🚨',
                'sort_order' => 1
            ],
            'HIGH' => [
                'text' => 'HIGH',
                'class' => 'bg-orange-500 text-white border-2 border-orange-600',
                'icon' => '⚠️',
                'sort_order' => 2
            ],
            'MEDIUM' => [
                'text' => 'MEDIUM', 
                'class' => 'bg-yellow-500 text-white border-2 border-yellow-600',
                'icon' => '📋',
                'sort_order' => 3
            ],
            'LOW' => [
                'text' => 'LOW',
                'class' => 'bg-blue-500 text-white border-2 border-blue-600',
                'icon' => '📅',
                'sort_order' => 4
            ],
            default => [
                'text' => 'NORMAL',
                'class' => 'bg-green-500 text-white border-2 border-green-600', 
                'icon' => '✅',
                'sort_order' => 5
            ]
        };
    }
    
    /**
     * Calculate urgency for a collection of items (for requests)
     */
    public function calculateBulkUrgency($items): array
    {
        $urgencies = [];
        $totalScore = 0;
        $maxScore = 0;
        $urgentCount = 0;
        $highCount = 0;
        
        foreach ($items as $item) {
            $urgency = $this->calculateUrgencyLevel($item);
            $urgencies[] = $urgency;
            
            $totalScore += $urgency['urgency_score'];
            $maxScore = max($maxScore, $urgency['urgency_score']);
            
            if ($urgency['final_urgency'] === 'URGENT') $urgentCount++;
            if ($urgency['final_urgency'] === 'HIGH') $highCount++;
        }
        
        $count = count($items);
        $avgScore = $count > 0 ? $totalScore / $count : 0;
        
        // Determine primary urgency level
        $primaryUrgency = 'NORMAL';
        if ($urgentCount > 0) {
            $primaryUrgency = 'URGENT';
        } elseif ($highCount > 0) {
            $primaryUrgency = 'HIGH';
        } elseif ($avgScore >= 40) {
            $primaryUrgency = 'MEDIUM';
        } elseif ($avgScore >= 20) {
            $primaryUrgency = 'LOW';
        }
        
        return [
            'item_urgencies' => $urgencies,
            'avg_urgency_score' => round($avgScore, 1),
            'max_urgency_score' => $maxScore,
            'primary_urgency' => $primaryUrgency,
            'urgent_items_count' => $urgentCount,
            'high_items_count' => $highCount,
            'total_items' => $count,
            'summary_text' => $this->generateBulkSummary($urgentCount, $highCount, $count)
        ];
    }
    
    /**
     * Generate summary text for bulk urgency
     */
    private function generateBulkSummary($urgentCount, $highCount, $totalCount): string
    {
        if ($urgentCount > 0) {
            return "{$urgentCount} urgent item" . ($urgentCount > 1 ? 's' : '') . " out of {$totalCount}";
        } elseif ($highCount > 0) {
            return "{$highCount} high priority item" . ($highCount > 1 ? 's' : '') . " out of {$totalCount}";
        } else {
            return "All {$totalCount} items are normal priority";
        }
    }
    
    /**
     * Get items sorted by urgency for dashboard
     */
    public function getItemsSortedByUrgency($items)
    {
        return $items->map(function ($item) {
                $urgency = $this->calculateUrgencyLevel($item);
                $item->urgency_data = $urgency;
                return $item;
            })
            ->sortByDesc('urgency_data.urgency_score')
            ->values();
    }
    
    /**
     * Get items grouped by urgency level
     */
    public function getItemsGroupedByUrgency($items): array
    {
        $sortedItems = $this->getItemsSortedByUrgency($items);
        
        return $sortedItems->groupBy('urgency_data.final_urgency')
                          ->sortBy(function ($group, $key) {
                              $badge = $this->getPriorityBadge($key);
                              return $badge['sort_order'];
                          })
                          ->toArray();
    }
    
    /**
     * Clear demand distribution cache (call when items change significantly)
     */
    public function clearDemandCache(): void
    {
        Cache::forget('demand_distribution');
    }
    
    /**
     * Get demand statistics for debugging
     */
    public function getDemandStatistics(): array
    {
        $demands = Barang::whereNotNull('annual_demand')
                         ->where('annual_demand', '>', 0)
                         ->pluck('annual_demand')
                         ->map(fn($annual) => $annual / 12);
        
        if ($demands->isEmpty()) {
            return ['error' => 'No demand data available'];
        }
        
        $sorted = $demands->sort()->values();
        $count = $sorted->count();
        
        return [
            'total_items' => $count,
            'min_monthly_demand' => $sorted->first(),
            'max_monthly_demand' => $sorted->last(),
            'avg_monthly_demand' => round($sorted->avg(), 2),
            'median_monthly_demand' => $count % 2 === 0 
                ? ($sorted[$count/2 - 1] + $sorted[$count/2]) / 2 
                : $sorted[floor($count/2)],
            'percentile_40' => $sorted[(int) ceil($count * 0.4) - 1] ?? 0,
            'percentile_80' => $sorted[(int) ceil($count * 0.8) - 1] ?? 0,
        ];
    }
}