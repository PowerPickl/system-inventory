<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;

    protected $table = 'stok';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
        'id_barang',
        'jumlah_stok',
        'status_stok'
    ];

    protected $casts = [
        'jumlah_stok' => 'integer'
    ];

    /**
     * Relationship dengan Barang (Many-to-One)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    /**
     * Update status stok berdasarkan jumlah dan reorder point
     */
    public function updateStatusStok()
    {
        if (!$this->barang) {
            return;
        }

        if ($this->jumlah_stok <= 0) {
            $this->status_stok = 'Habis';
        } elseif ($this->jumlah_stok <= $this->barang->reorder_point) {
            $this->status_stok = 'Perlu Restock';
        } else {
            $this->status_stok = 'Aman';
        }

        $this->save();
    }

    /**
     * Tambah stok
     */
    public function tambahStok($jumlah, $userId, $keterangan = null, $referensiTipe = null, $referensiId = null)
    {
        $stokSebelum = $this->jumlah_stok;
        $this->jumlah_stok += $jumlah;
        $this->updateStatusStok();

        // Log perubahan stok
        LogStok::create([
            'id_barang' => $this->id_barang,
            'tanggal_log' => now(),
            'jenis_perubahan' => 'Masuk',
            'qty_sebelum' => $stokSebelum,
            'qty_perubahan' => $jumlah,
            'qty_sesudah' => $this->jumlah_stok,
            'id_user' => $userId,
            'referensi_tipe' => $referensiTipe,
            'referensi_id' => $referensiId,
            'keterangan' => $keterangan
        ]);

        return $this;
    }

    /**
     * Kurangi stok
     */
    public function kurangiStok($jumlah, $userId, $keterangan = null, $referensiTipe = null, $referensiId = null)
    {
        if ($this->jumlah_stok < $jumlah) {
            throw new \Exception('Stok tidak mencukupi. Stok tersedia: ' . $this->jumlah_stok);
        }

        $stokSebelum = $this->jumlah_stok;
        $this->jumlah_stok -= $jumlah;
        $this->updateStatusStok();

        // Log perubahan stok
        LogStok::create([
            'id_barang' => $this->id_barang,
            'tanggal_log' => now(),
            'jenis_perubahan' => 'Keluar',
            'qty_sebelum' => $stokSebelum,
            'qty_perubahan' => -$jumlah,
            'qty_sesudah' => $this->jumlah_stok,
            'id_user' => $userId,
            'referensi_tipe' => $referensiTipe,
            'referensi_id' => $referensiId,
            'keterangan' => $keterangan
        ]);

        return $this;
    }

    /**
     * Scope untuk stok yang aman
     */
    public function scopeAman($query)
    {
        return $query->where('status_stok', 'Aman');
    }

    /**
     * Scope untuk stok yang perlu restock
     */
    public function scopePerluRestock($query)
    {
        return $query->where('status_stok', 'Perlu Restock');
    }

    /**
     * Scope untuk stok yang habis
     */
    public function scopeHabis($query)
    {
        return $query->where('status_stok', 'Habis');
    }
}