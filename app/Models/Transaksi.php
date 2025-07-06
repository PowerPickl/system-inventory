<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'nomor_transaksi',
        'tanggal_transaksi',
        'id_user',
        'nama_customer',
        'kendaraan',
        'total_harga',
        'jenis_transaksi',
        'status_transaksi',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'total_harga' => 'decimal:2'
    ];

    /**
     * Relationship dengan User (Kasir) - Many-to-One
     */
    public function kasir()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Relationship dengan DetailTransaksi (One-to-Many)
     */
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    /**
     * Generate nomor transaksi otomatis
     */
    public static function generateNomorTransaksi()
    {
        $prefix = 'TRX';
        $today = now()->format('Ymd');
        
        // Cari transaksi terakhir hari ini
        $lastTransaction = self::where('nomor_transaksi', 'like', $prefix . $today . '%')
            ->orderBy('nomor_transaksi', 'desc')
            ->first();

        if ($lastTransaction) {
            // Ambil 3 digit terakhir dan tambah 1
            $lastNumber = intval(substr($lastTransaction->nomor_transaksi, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $today . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung ulang total harga berdasarkan detail
     */
    public function hitungTotalHarga()
    {
        $this->total_harga = $this->detailTransaksi()->sum('subtotal');
        $this->save();
        return $this;
    }

    /**
     * Proses persetujuan semua item dan kurangi stok
     */
    public function prosesPengeluaranStok()
    {
        if ($this->status_transaksi !== 'Progress') {
            throw new \Exception('Transaksi harus dalam status Progress');
        }

        foreach ($this->detailTransaksi as $detail) {
            if ($detail->status_permintaan === 'Approved') {
                // Kurangi stok
                $stok = $detail->barang->stok;
                if ($stok) {
                    $stok->kurangiStok(
                        $detail->qty,
                        $this->id_user,
                        "Transaksi Service: {$this->nomor_transaksi}",
                        'transaksi',
                        $this->id_transaksi
                    );
                }
            }
        }

        // Update status transaksi
        $this->status_transaksi = 'Selesai';
        $this->save();

        return $this;
    }

    /**
     * Scope untuk transaksi hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_transaksi', today());
    }

    /**
     * Scope untuk transaksi dalam progress
     */
    public function scopeProgress($query)
    {
        return $query->where('status_transaksi', 'Progress');
    }

    /**
     * Scope untuk transaksi selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status_transaksi', 'Selesai');
    }

    /**
     * Accessor untuk format nomor transaksi yang readable
     */
    public function getFormattedNomorAttribute()
    {
        return '#' . $this->nomor_transaksi;
    }

    /**
     * Boot method untuk auto-generate nomor transaksi
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaksi) {
            if (empty($transaksi->nomor_transaksi)) {
                $transaksi->nomor_transaksi = self::generateNomorTransaksi();
            }
        });
    }
}