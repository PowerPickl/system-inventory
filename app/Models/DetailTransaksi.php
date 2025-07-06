<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_transaksi',
        'id_barang',
        'qty',
        'harga_satuan',
        'subtotal',
        'status_permintaan'
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    /**
     * Relationship dengan Transaksi (Many-to-One)
     */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi', 'id_transaksi');
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
        $this->subtotal = $this->qty * $this->harga_satuan;
        return $this;
    }

    /**
     * Approve permintaan barang
     */
    public function approve($userId = null)
    {
        // Cek ketersediaan stok
        $stok = $this->barang->stok;
        if (!$stok || $stok->jumlah_stok < $this->qty) {
            throw new \Exception("Stok tidak mencukupi untuk {$this->barang->nama_barang}. Stok tersedia: " . ($stok ? $stok->jumlah_stok : 0));
        }

        $this->status_permintaan = 'Approved';
        $this->save();

        return $this;
    }

    /**
     * Reject permintaan barang
     */
    public function reject()
    {
        $this->status_permintaan = 'Rejected';
        $this->save();

        return $this;
    }

    /**
     * Scope untuk detail yang pending
     */
    public function scopePending($query)
    {
        return $query->where('status_permintaan', 'Pending');
    }

    /**
     * Scope untuk detail yang approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_permintaan', 'Approved');
    }

    /**
     * Scope untuk detail yang rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status_permintaan', 'Rejected');
    }

    /**
     * Accessor untuk status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status_permintaan) {
            'Pending' => 'yellow',
            'Approved' => 'green',
            'Rejected' => 'red',
            default => 'gray'
        };
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
            // Update total transaksi setelah detail disimpan
            $detail->transaksi->hitungTotalHarga();
        });
    }
}