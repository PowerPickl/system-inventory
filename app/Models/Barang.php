<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'harga_beli',
        'harga_jual',
        'reorder_point',
        'eoq_qty',
        'lead_time',
        'deskripsi'
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'reorder_point' => 'integer',
        'eoq_qty' => 'integer',
        'lead_time' => 'integer'
    ];

    /**
     * Relationship dengan Stok (One-to-One)
     */
    public function stok()
    {
        return $this->hasOne(Stok::class, 'id_barang', 'id_barang');
    }

    /**
     * Relationship dengan DetailTransaksi (One-to-Many)
     */
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_barang', 'id_barang');
    }

    /**
     * Relationship dengan RestockRequestDetail (One-to-Many)
     */
    public function restockRequestDetail()
    {
        return $this->hasMany(RestockRequestDetail::class, 'id_barang', 'id_barang');
    }

    /**
     * Relationship dengan BarangMasukDetail (One-to-Many)
     */
    public function barangMasukDetail()
    {
        return $this->hasMany(BarangMasukDetail::class, 'id_barang', 'id_barang');
    }

    /**
     * Relationship dengan LogStok (One-to-Many)
     */
    public function logStok()
    {
        return $this->hasMany(LogStok::class, 'id_barang', 'id_barang');
    }

    /**
     * Accessor untuk mendapatkan status stok
     */
    public function getStatusStokAttribute()
    {
        if (!$this->stok) {
            return 'Tidak Ada Data';
        }

        $jumlahStok = $this->stok->jumlah_stok;
        
        if ($jumlahStok <= 0) {
            return 'Habis';
        } elseif ($jumlahStok <= $this->reorder_point) {
            return 'Perlu Restock';
        } else {
            return 'Aman';
        }
    }

    /**
     * Accessor untuk mendapatkan jumlah stok saat ini
     */
    public function getJumlahStokAttribute()
    {
        return $this->stok ? $this->stok->jumlah_stok : 0;
    }

    /**
     * Scope untuk barang yang perlu restock
     */
    public function scopePerluRestock($query)
    {
        return $query->whereHas('stok', function ($q) {
            $q->whereRaw('jumlah_stok <= reorder_point');
        });
    }

    /**
     * Scope untuk barang yang habis
     */
    public function scopeHabis($query)
    {
        return $query->whereHas('stok', function ($q) {
            $q->where('jumlah_stok', '<=', 0);
        });
    }

    // Add these methods to existing Barang model

    /**
     * EOQ Calculation methods (add to existing Barang model)
     */
    
    /**
     * Calculate EOQ for this item
     */
    public function calculateEOQ()
    {
        $service = new \App\Services\EOQCalculationService();
        return $service->calculateAll($this);
    }

    /**
     * Get restock recommendation
     */
    public function getRestockRecommendation()
    {
        $service = new \App\Services\EOQCalculationService();
        return $service->getRestockRecommendation($this);
    }

    /**
     * Auto-update demand berdasarkan history
     */
    public function updateDemandFromHistory($days = 365)
    {
        $service = new \App\Services\EOQCalculationService();
        $demandData = $service->calculateDemandFromHistory($this, $days);
        
        $this->update([
            'annual_demand' => $demandData['annual_demand'],
            'demand_avg_daily' => $demandData['avg_daily_demand'],
            'demand_max_daily' => $demandData['max_daily_demand']
        ]);

        return $demandData;
    }

    /**
     * Accessor untuk EOQ status
     */
    public function getEoqStatusAttribute()
    {
        if (!$this->eoq_calculated) {
            return 'Not Calculated';
        }

        $currentStock = $this->jumlah_stok;
        $rop = $this->rop_calculated ?? $this->reorder_point;

        if ($currentStock <= 0) {
            return 'Critical - Out of Stock';
        } elseif ($currentStock <= $rop) {
            return 'Reorder Required';
        } elseif ($currentStock <= $rop * 1.5) {
            return 'Monitor Closely';
        } else {
            return 'Optimal Level';
        }
    }

    /**
     * Scope untuk barang yang perlu restock berdasarkan ROP
     */
    public function scopeNeedRestock($query)
    {
        return $query->whereHas('stok', function ($q) {
            $q->whereRaw('jumlah_stok <= COALESCE(rop_calculated, reorder_point)');
        });
    }

    /**
     * Scope untuk barang dengan EOQ calculation
     */
    public function scopeWithEOQData($query)
    {
        return $query->whereNotNull('eoq_calculated')
                    ->whereNotNull('safety_stock')
                    ->whereNotNull('rop_calculated');
    }
}