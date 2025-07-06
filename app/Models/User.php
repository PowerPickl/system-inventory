<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'address',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean'
        ];
    }

    /**
     * Relationship dengan Role (Many-to-One)
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Relationship dengan Transaksi sebagai Kasir (One-to-Many)
     */
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_user', 'id');
    }

    /**
     * Relationship dengan RestockRequest sebagai Gudang (One-to-Many)
     */
    public function restockRequestGudang()
    {
        return $this->hasMany(RestockRequest::class, 'id_user_gudang', 'id');
    }

    /**
     * Relationship dengan RestockRequest sebagai Approver (One-to-Many)
     */
    public function restockRequestApproved()
    {
        return $this->hasMany(RestockRequest::class, 'id_user_approved', 'id');
    }

    /**
     * Relationship dengan BarangMasuk (One-to-Many)
     */
    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_user_gudang', 'id');
    }

    /**
     * Relationship dengan LogStok (One-to-Many)
     */
    public function logStok()
    {
        return $this->hasMany(LogStok::class, 'id_user', 'id');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->nama_role === $roleName;
    }

    /**
     * Check if user is Owner
     */
    public function isOwner()
    {
        return $this->hasRole('Owner');
    }

    /**
     * Check if user is Gudang
     */
    public function isGudang()
    {
        return $this->hasRole('Gudang');
    }

    /**
     * Check if user is Kasir
     */
    public function isKasir()
    {
        return $this->hasRole('Kasir');
    }

    /**
     * Scope untuk user yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk user dengan role tertentu
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('nama_role', $roleName);
        });
    }

    /**
     * Accessor untuk nama role
     */
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->nama_role : 'No Role';
    }

    /**
     * Accessor untuk initials (untuk avatar)
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        
        return $initials ?: strtoupper(substr($this->name, 0, 2));
    }
}