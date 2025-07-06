<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasukDetail extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk_detail';
    protected $primaryKey = 'id_masuk_detail';

    protected $fillable = [
        'id_masuk',
        'id_barang',
        'qty_masuk',
        'harga_beli_satuan',
        'subtotal',
        'tanggal_expired',
        'batch_number',
        'keterangan_detail'
    ];

    protected $casts = [
        'qty_masuk' => 'integer',
        'harga_beli_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tanggal_expired' => 'date'
    ];

    /**
     * Relationship dengan BarangMasuk (Many-to-One)
     */
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'id_masuk', 'id_masuk');
    }

    /**
     * Relationship dengan Barang (Many-to-One)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    /**
     * Hitung subtotal otomatis
     */
    public function hitungSubtotal()
    {
        $this->subtotal = $this->qty_masuk * $this->harga_beli_satuan;
        return $this;
    }

    /**
     * Boot method untuk auto-calculate subtotal
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            $detail->hitungSubtotal();
        });

        static::saved(function ($detail) {
            // Update total nilai barang masuk
            $barangMasuk = $detail->barangMasuk;
            if ($barangMasuk) {
                $barangMasuk->total_nilai = $barangMasuk->details()->sum('subtotal');
                $barangMasuk->save();
            }
        });
    }
}
