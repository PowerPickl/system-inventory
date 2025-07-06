<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_masuk';

    protected $fillable = [
        'nomor_masuk',
        'id_request',
        'tanggal_masuk',
        'id_user_gudang',
        'supplier',
        'nomor_invoice',
        'total_nilai',
        'jenis_masuk',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'total_nilai' => 'decimal:2'
    ];

    /**
     * Relationship dengan RestockRequest (Many-to-One)
     */
    public function restockRequest()
    {
        return $this->belongsTo(RestockRequest::class, 'id_request', 'id_request');
    }

    /**
     * Relationship dengan User Gudang (Many-to-One)
     */
    public function userGudang()
    {
        return $this->belongsTo(User::class, 'id_user_gudang', 'id');
    }

    /**
     * Relationship dengan BarangMasukDetail (One-to-Many)
     */
    public function details()
    {
        return $this->hasMany(BarangMasukDetail::class, 'id_masuk', 'id_masuk');
    }

    /**
     * Generate nomor barang masuk otomatis
     */
    public static function generateNomorMasuk()
    {
        $prefix = 'BM';
        $today = now()->format('Ymd');
        
        $lastMasuk = self::where('nomor_masuk', 'like', $prefix . $today . '%')
            ->orderBy('nomor_masuk', 'desc')
            ->first();

        if ($lastMasuk) {
            $lastNumber = intval(substr($lastMasuk->nomor_masuk, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $today . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Proses barang masuk dan update stok
     */
    public function prosesBarangMasuk()
    {
        foreach ($this->details as $detail) {
            $stok = $detail->barang->stok;
            if ($stok) {
                $stok->tambahStok(
                    $detail->qty_masuk,
                    $this->id_user_gudang,
                    "Barang Masuk: {$this->nomor_masuk}",
                    'barang_masuk',
                    $this->id_masuk
                );
            }
        }

        // Update total nilai
        $this->total_nilai = $this->details()->sum('subtotal');
        $this->save();

        // Complete restock request jika ada
        if ($this->restockRequest) {
            $this->restockRequest->complete();
        }

        return $this;
    }

    /**
     * Boot method untuk auto-generate nomor
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barangMasuk) {
            if (empty($barangMasuk->nomor_masuk)) {
                $barangMasuk->nomor_masuk = self::generateNomorMasuk();
            }
        });
    }
}
