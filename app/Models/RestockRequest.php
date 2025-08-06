<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockRequest extends Model
{
    use HasFactory;

    protected $table = 'restock_request';
    protected $primaryKey = 'id_request';

    // ========= STATUS CONSTANTS =========
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_ORDERED = 'Ordered';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_TERMINATED = 'Terminated';
    const STATUS_CANCELLED = 'Cancelled';

    protected $fillable = [
        'nomor_request',
        'id_user_gudang',
        'tanggal_request',
        'status_request',
        'id_user_approved',
        'tanggal_approved',
        'catatan_request',
        'catatan_approval',
        // NEW FIELDS
        'tanggal_terminated',
        'id_user_terminated',
        'tanggal_ordered',
        'id_user_ordered'
    ];

    protected $casts = [
        'tanggal_request' => 'datetime',
        'tanggal_approved' => 'datetime',
        // NEW CASTS
        'tanggal_terminated' => 'datetime',
        'tanggal_ordered' => 'datetime'
    ];

    // ========= VALIDATION METHODS =========
    
    /**
     * Get all valid status values
     */
    public static function getValidStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_ORDERED,
            self::STATUS_COMPLETED,
            self::STATUS_REJECTED,
            self::STATUS_TERMINATED,
            self::STATUS_CANCELLED
        ];
    }

    /**
     * Mutator untuk validate status_request
     */
    public function setStatusRequestAttribute($value)
    {
        if (!in_array($value, self::getValidStatuses())) {
            throw new \InvalidArgumentException("Invalid status: {$value}. Valid statuses: " . implode(', ', self::getValidStatuses()));
        }
        
        $this->attributes['status_request'] = $value;
    }

    /**
     * Check if status is valid
     */
    public static function isValidStatus($status)
    {
        return in_array($status, self::getValidStatuses());
    }

    // ========= RELATIONSHIPS =========

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
     * NEW: Relationship dengan User yang Terminate (Many-to-One)
     */
    public function userTerminated()
    {
        return $this->belongsTo(User::class, 'id_user_terminated', 'id');
    }

    /**
     * NEW: Relationship dengan User yang Order (Many-to-One)
     */
    public function userOrdered()
    {
        return $this->belongsTo(User::class, 'id_user_ordered', 'id');
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

    // ========= STATIC METHODS =========

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

    // ========= STATUS METHODS (Updated with Constants) =========

    /**
     * Approve request oleh Owner
     */
    public function approve($userId, $catatan = null)
    {
        $this->status_request = self::STATUS_APPROVED;
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
        $this->status_request = self::STATUS_REJECTED;
        $this->id_user_approved = $userId;
        $this->tanggal_approved = now();
        $this->catatan_approval = $catatan;
        $this->save();

        return $this;
    }

    /**
     * Cancel request oleh Gudang Staff
     */
    public function cancel($catatan = null)
    {
        if ($this->status_request !== self::STATUS_PENDING) {
            throw new \Exception('Only pending requests can be cancelled');
        }

        $this->status_request = self::STATUS_CANCELLED;
        $this->catatan_approval = $catatan ?? 'Cancelled by warehouse staff at ' . now()->format('Y-m-d H:i:s');
        $this->save();

        return $this;
    }

    /**
     * NEW: Force terminate request oleh Owner (Emergency use)
     */
    public function terminate($userId, $reason)
    {
        $terminationNote = 'FORCE TERMINATED by ' . auth()->user()->name . 
                          ' on ' . now()->format('Y-m-d H:i:s') . 
                          ' | Reason: ' . $reason;
        
        $this->status_request = self::STATUS_TERMINATED;
        $this->id_user_terminated = $userId;
        $this->tanggal_terminated = now();
        $this->catatan_approval = ($this->catatan_approval ? $this->catatan_approval . ' | ' : '') . $terminationNote;
        $this->save();

        return $this;
    }

    /**
     * ENHANCED: Complete request setelah barang masuk - with user tracking
     */
    public function complete($userId = null)
    {
        $this->status_request = self::STATUS_COMPLETED;
        // Don't update ordered fields here - they should already be set
        if ($userId) {
            $this->id_user_completed = $userId; // Add this field to migration if needed
            $this->tanggal_completed = now(); // Add this field to migration if needed
        }
        $this->save();

        return $this;
    }

    /**
     * NEW: Mark as ordered (intermediate step before actual completion)
     */
    public function markAsOrdered($userId)
    {
        $this->status_request = self::STATUS_ORDERED;
        $this->id_user_ordered = $userId;
        $this->tanggal_ordered = now();
        $this->save();

        return $this;
    }

    // ========= SCOPES =========

    /**
     * Scope untuk request pending
     */
    public function scopePending($query)
    {
        return $query->where('status_request', self::STATUS_PENDING);
    }

    /**
     * Scope untuk request approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_request', self::STATUS_APPROVED);
    }

    /**
     * Scope untuk request ordered
     */
    public function scopeOrdered($query)
    {
        return $query->where('status_request', self::STATUS_ORDERED);
    }

    /**
     * Scope untuk request completed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status_request', self::STATUS_COMPLETED);
    }

    /**
     * NEW: Scope untuk request terminated
     */
    public function scopeTerminated($query)
    {
        return $query->where('status_request', self::STATUS_TERMINATED);
    }

    /**
     * NEW: Scope untuk request rejected
     */
    public function scopeRejected($query)
    {
        return $query->where('status_request', self::STATUS_REJECTED);
    }

    /**
     * NEW: Scope untuk request cancelled
     */
    public function scopeCancelled($query)
    {
        return $query->where('status_request', self::STATUS_CANCELLED);
    }

    // ========= VALIDATION METHODS =========

    /**
     * Check if request can be completed
     */
    public function canBeCompleted()
    {
        return $this->status_request === self::STATUS_ORDERED;
    }

    /**
     * NEW: Check if request can be terminated
     */
    public function canBeTerminated()
    {
        return $this->status_request === self::STATUS_APPROVED;
    }

    /**
     * NEW: Check if request can be marked as ordered
     */
    public function canBeMarkedAsOrdered()
    {
        return $this->status_request === self::STATUS_APPROVED;
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled()
    {
        return $this->status_request === self::STATUS_PENDING;
    }

    /**
     * Check if request can be edited
     */
    public function canBeEdited()
    {
        return $this->status_request === self::STATUS_PENDING;
    }

    // ========= ACCESSORS =========

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
     * NEW: Get count of additional items added by owner
     */
    public function getAdditionalItemsCountAttribute()
    {
        return $this->details()
            ->where('alasan_request', 'Additional item added by Owner during approval')
            ->count();
    }

    /**
     * NEW: Check if request has urgent items (zero stock)
     */
    public function getHasUrgentItemsAttribute()
    {
        return $this->details()
            ->whereHas('barang.stok', function($query) {
                $query->where('jumlah_stok', '<=', 0);
            })
            ->exists();
    }

    /**
     * UPDATED: Accessor untuk status badge color - tambah Terminated & Cancelled
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status_request) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'blue',
            self::STATUS_ORDERED => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_TERMINATED => 'gray',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray'
        };
    }

    /**
     * NEW: Get status display text with icons
     */
    public function getStatusDisplayAttribute()
    {
        return match($this->status_request) {
            self::STATUS_PENDING => '⏳ Pending',
            self::STATUS_APPROVED => '✅ Approved',
            self::STATUS_ORDERED => '📦 Ordered',
            self::STATUS_COMPLETED => '🎉 Completed',
            self::STATUS_REJECTED => '❌ Rejected',
            self::STATUS_TERMINATED => '🛑 Terminated',
            self::STATUS_CANCELLED => '⚪ Cancelled',
            default => '❓ Unknown'
        };
    }

    /**
     * NEW: Get workflow status for display
     */
    public function getWorkflowStatusAttribute()
    {
        $steps = [
            'requested' => $this->tanggal_request ? '✅' : '⏳',
            'approved' => $this->tanggal_approved ? '✅' : ($this->status_request === self::STATUS_REJECTED ? '❌' : '⏳'),
            'ordered' => $this->tanggal_ordered ? '✅' : '⏳',
            'completed' => $this->status_request === self::STATUS_COMPLETED ? '✅' : '⏳'
        ];

        if ($this->status_request === self::STATUS_TERMINATED) {
            $steps['terminated'] = '🛑';
        }

        if ($this->status_request === self::STATUS_CANCELLED) {
            $steps['cancelled'] = '⚪';
        }

        return $steps;
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