<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogStok extends Model
{
    use HasFactory;

    protected $table = 'log_stok';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_barang',
        'tanggal_log',
        'jenis_perubahan',
        'qty_sebelum',
        'qty_perubahan',
        'qty_sesudah',
        'id_user',
        'referensi_tipe',
        'referensi_id',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_log' => 'datetime',
        'qty_sebelum' => 'integer',
        'qty_perubahan' => 'integer',
        'qty_sesudah' => 'integer',
        'referensi_id' => 'integer'
    ];

    /**
     * Relationship dengan Barang (Many-to-One)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    /**
     * Relationship dengan User (Many-to-One)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Get referensi object berdasarkan tipe
     */
    public function getReferensiAttribute()
    {
        if (!$this->referensi_tipe || !$this->referensi_id) {
            return null;
        }

        return match($this->referensi_tipe) {
            'transaksi' => Transaksi::find($this->referensi_id),
            'barang_masuk' => BarangMasuk::find($this->referensi_id),
            'restock_request' => RestockRequest::find($this->referensi_id),
            default => null
        };
    }

    /**
     * Scope untuk log hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_log', today());
    }

    /**
     * Scope untuk log minggu ini
     */
    public function scopeMingguIni($query)
    {
        return $query->whereBetween('tanggal_log', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope untuk log bulan ini
     */
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_log', now()->month)
                    ->whereYear('tanggal_log', now()->year);
    }

    /**
     * Scope untuk jenis perubahan masuk
     */
    public function scopeMasuk($query)
    {
        return $query->where('jenis_perubahan', 'Masuk');
    }

    /**
     * Scope untuk jenis perubahan keluar
     */
    public function scopeKeluar($query)
    {
        return $query->where('jenis_perubahan', 'Keluar');
    }

    /**
     * Scope untuk barang tertentu
     */
    public function scopeForBarang($query, $idBarang)
    {
        return $query->where('id_barang', $idBarang);
    }

    /**
     * Accessor untuk icon berdasarkan jenis perubahan
     */
    public function getJenisIconAttribute()
    {
        return match($this->jenis_perubahan) {
            'Masuk' => '↗️',
            'Keluar' => '↘️',
            'Adjustment' => '🔄',
            'Koreksi' => '✏️',
            default => '📦'
        };
    }

    /**
     * Accessor untuk color berdasarkan jenis perubahan
     */
    public function getJenisColorAttribute()
    {
        return match($this->jenis_perubahan) {
            'Masuk' => 'green',
            'Keluar' => 'red',
            'Adjustment' => 'blue',
            'Koreksi' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Accessor untuk format qty perubahan dengan tanda
     */
    public function getFormattedQtyPerubahanAttribute()
    {
        $prefix = $this->qty_perubahan > 0 ? '+' : '';
        return $prefix . $this->qty_perubahan;
    }

    /**
     * Static method untuk create log dengan validasi
     */
    public static function createLog($data)
    {
        // Validasi data required
        $required = ['id_barang', 'jenis_perubahan', 'qty_sebelum', 'qty_perubahan', 'qty_sesudah', 'id_user'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \Exception("Field {$field} is required for log creation");
            }
        }

        // Set tanggal_log jika tidak ada
        if (!isset($data['tanggal_log'])) {
            $data['tanggal_log'] = now();
        }

        return self::create($data);
    }

    /**
     * Get summary log untuk periode tertentu
     */
    public static function getSummary($startDate = null, $endDate = null, $idBarang = null)
    {
        $query = self::query();

        if ($startDate) {
            $query->whereDate('tanggal_log', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('tanggal_log', '<=', $endDate);
        }

        if ($idBarang) {
            $query->where('id_barang', $idBarang);
        }

        return [
            'total_masuk' => $query->clone()->where('jenis_perubahan', 'Masuk')->sum('qty_perubahan'),
            'total_keluar' => abs($query->clone()->where('jenis_perubahan', 'Keluar')->sum('qty_perubahan')),
            'total_adjustment' => $query->clone()->where('jenis_perubahan', 'Adjustment')->sum('qty_perubahan'),
            'total_transaksi' => $query->clone()->count()
        ];
    }
}