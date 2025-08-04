<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    use HasFactory;

    protected $table = 'kategori_barang';
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'nama_kategori',
        'kode_kategori', 
        'deskripsi',
        'icon',
        'warna',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean'
    ];

    /**
     * Relationship dengan Barang (One-to-Many)
     */
    public function barang()
    {
        return $this->hasMany(Barang::class, 'id_kategori', 'id_kategori');
    }

    /**
     * Get jumlah barang dalam kategori
     */
    public function getJumlahBarangAttribute()
    {
        return $this->barang()->count();
    }

    /**
     * Get next sequence number for this category
     */
    public function getNextSequenceNumber()
    {
        $lastSequence = $this->barang()
                            ->where('id_kategori', $this->id_kategori)
                            ->max('sequence_number');
        
        return ($lastSequence ?? 0) + 1;
    }

    /**
     * Generate kode barang otomatis untuk kategori ini
     * Format: FLD-0001, ENG-0002, etc.
     */
    public function generateKodeBarang()
    {
        $sequence = $this->getNextSequenceNumber();
        return $this->kode_kategori . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope dengan statistik barang
     */
    public function scopeWithStats($query)
    {
        return $query->withCount([
            'barang as total_barang',
            'barang as barang_aman' => function($q) {
                $q->whereHas('stok', function($stokQuery) {
                    $stokQuery->where('status_stok', 'Aman');
                });
            },
            'barang as barang_restock' => function($q) {
                $q->whereHas('stok', function($stokQuery) {
                    $stokQuery->where('status_stok', 'Perlu Restock');
                });
            },
            'barang as barang_habis' => function($q) {
                $q->whereHas('stok', function($stokQuery) {
                    $stokQuery->where('status_stok', 'Habis');
                });
            }
        ]);
    }

    /**
     * Get kategori with color and icon for display
     */
    public function getDisplayBadgeAttribute()
    {
        return [
            'nama' => $this->nama_kategori,
            'kode' => $this->kode_kategori,
            'icon' => $this->icon,
            'warna' => $this->warna,
            'class' => 'px-2 py-1 text-xs font-semibold rounded-full',
            'style' => "background-color: {$this->warna}20; color: {$this->warna};"
        ];
    }
}