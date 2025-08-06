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
        'status_permintaan',
        'keterangan' // TAMBAH INI buat notes cancel/reject
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // TAMBAH CONSTANTS buat status
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_CANCELLED = 'Cancelled'; // NEW

    /**
     * Get available status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Menunggu Validasi',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_CANCELLED => 'Dibatalkan', // NEW
        ];
    }

    // ... existing relationships (transaksi, barang) tetap sama ...

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

    // ... existing methods (hitungSubtotal, approve, reject) tetap sama ...

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

        $this->status_permintaan = self::STATUS_APPROVED;
        $this->save();

        return $this;
    }

    /**
     * Reject permintaan barang
     */
    public function reject($keterangan = null)
    {
        $this->status_permintaan = self::STATUS_REJECTED;
        if ($keterangan) {
            $this->keterangan = $keterangan;
        }
        $this->save();

        return $this;
    }

    /**
     * Cancel permintaan barang - NEW METHOD
     */
    public function cancel($keterangan = null)
    {
        $this->status_permintaan = self::STATUS_CANCELLED;
        $this->keterangan = $keterangan ?: 'Dibatalkan oleh Service Advisor';
        $this->save();

        return $this;
    }

    // ... existing scopes tetap sama + tambah scope baru ...

    /**
     * Scope untuk detail yang pending
     */
    public function scopePending($query)
    {
        return $query->where('status_permintaan', self::STATUS_PENDING);
    }

    /**
     * Scope untuk detail yang approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_permintaan', self::STATUS_APPROVED);
    }

    /**
     * Scope untuk detail yang rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status_permintaan', self::STATUS_REJECTED);
    }

    /**
     * Scope untuk detail yang cancelled - NEW SCOPE
     */
    public function scopeCancelled($query)
    {
        return $query->where('status_permintaan', self::STATUS_CANCELLED);
    }

    /**
     * Scope untuk detail yang masih bisa diproses (pending only)
     */
    public function scopeProcessable($query)
    {
        return $query->where('status_permintaan', self::STATUS_PENDING);
    }

    /**
     * Accessor untuk status badge color - UPDATE buat include cancelled
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status_permintaan) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_CANCELLED => 'gray', // NEW
            default => 'gray'
        };
    }

    /**
     * Check if detail can be cancelled
     */
    public function canBeCancelled()
    {
        return $this->status_permintaan === self::STATUS_PENDING;
    }

    /**
     * Check if detail is in final state (approved/rejected/cancelled)
     */
    public function isFinalStatus()
    {
        return in_array($this->status_permintaan, [
            self::STATUS_APPROVED, 
            self::STATUS_REJECTED, 
            self::STATUS_CANCELLED
        ]);
    }

    // ... existing boot method tetap sama ...

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
            if ($detail->transaksi) {
                $detail->transaksi->hitungTotalHarga();
            }
        });
    }
}