<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'restock_request_detail';
    protected $primaryKey = 'id_request_detail';

    protected $fillable = [
        'id_request',
        'id_barang',
        'qty_request',
        'qty_approved',
        'estimasi_harga',
        'alasan_request'
    ];

    protected $casts = [
        'qty_request' => 'integer',
        'qty_approved' => 'integer',
        'estimasi_harga' => 'decimal:2'
    ];

    /**
     * Relationship dengan RestockRequest (Many-to-One)
     */
    public function restockRequest()
    {
        return $this->belongsTo(RestockRequest::class, 'id_request', 'id_request');
    }

    /**
     * Relationship dengan Barang (Many-to-One)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    /**
     * Auto calculate estimasi harga berdasarkan harga beli barang
     */
    public function calculateEstimasiHarga()
    {
        if ($this->barang && $this->qty_approved) {
            $this->estimasi_harga = $this->barang->harga_beli * $this->qty_approved;
        }
        return $this;
    }

    /**
     * Accessor untuk qty yang akan dibeli (approved atau request)
     */
    public function getQtyFinalAttribute()
    {
        return $this->qty_approved ?? $this->qty_request;
    }
}