<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockRequest extends Model
{
    use HasFactory;

    protected $table = 'restock_request';
    protected $primaryKey = 'id_request';

    protected $fillable = [
        'nomor_request',
        'id_user_gudang',
        'tanggal_request',
        'status_request',
        'id_user_approved',
        'tanggal_approved',
        'catatan_request',
        'catatan_approval'
    ];

    protected $casts = [
        'tanggal_request' => 'datetime',
        'tanggal_approved' => 'datetime'
    ];

    /**
     * Relationship dengan User Gudang (Many-to-One)
     */
    public function userGudang()
    {
        return $this->belongsTo(User::class, 'id_user_gudang', 'id');
    }

    /**
     * Relationship dengan User yang Approve (Many-to-One)
     */
    public function userApproved()
    {
        return $this->belongsTo(User::class, 'id_user_approved', 'id');
    }

    /**
     * Relationship dengan RestockRequestDetail (One-to-Many)
     */
    public function details()
    {
        return $this->hasMany(RestockRequestDetail::class, 'id_request', 'id_request');
    }

    /**
     * Relationship dengan BarangMasuk (One-to-Many)
     */
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_request', 'id_request');
    }

    /**
     * Generate nomor request otomatis
     */
    public static function generateNomorRequest()
    {
        $prefix = 'REQ';
        $today = now()->format('Ymd');
        
        // Cari request terakhir hari ini
        $lastRequest = self::where('nomor_request', 'like', $prefix . $today . '%')
            ->orderBy('nomor_request', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = intval(substr($lastRequest->nomor_request, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $today . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Approve request oleh Owner
     */
    public function approve($userId, $catatan = null)
    {
        $this->status_request = 'Approved';
        $this->id_user_approved = $userId;
        $this->tanggal_approved = now();
        $this->catatan_approval = $catatan;
        $this->save();

        return $this;
    }

    /**
     * Reject request oleh Owner
     */
    public function reject($userId, $catatan = null)
    {
        $this->status_request = 'Rejected';
        $this->id_user_approved = $userId;
        $this->tanggal_approved = now();
        $this->catatan_approval = $catatan;
        $this->save();

        return $this;
    }

    /**
     * Complete request setelah barang masuk
     */
    public function complete()
    {
        $this->status_request = 'Completed';
        $this->save();

        return $this;
    }

    /**
     * Hitung total estimasi biaya
     */
    public function getTotalEstimasiBiayaAttribute()
    {
        return $this->details()->sum('estimasi_harga');
    }

    /**
     * Hitung total item yang direquest
     */
    public function getTotalItemsAttribute()
    {
        return $this->details()->count();
    }

    /**
     * Scope untuk request pending
     */
    public function scopePending($query)
    {
        return $query->where('status_request', 'Pending');
    }

    /**
     * Scope untuk request approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_request', 'Approved');
    }

    /**
     * Scope untuk request completed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status_request', 'Completed');
    }

    /**
     * Accessor untuk status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status_request) {
            'Pending' => 'yellow',
            'Approved' => 'blue',
            'Completed' => 'green',
            'Rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Boot method untuk auto-generate nomor request
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->nomor_request)) {
                $request->nomor_request = self::generateNomorRequest();
            }
        });
    }
}