<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\KategoriBarang;

class BarangCodeService
{
    /**
     * Generate kode barang otomatis
     */
    public static function generateKodeBarang($kategoriId)
    {
        $kategori = KategoriBarang::find($kategoriId);
        
        if (!$kategori) {
            throw new \Exception('Kategori tidak ditemukan');
        }
        
        return $kategori->generateKodeBarang();
    }
    
    /**
     * Validate kode barang unique
     */
    public static function validateKodeUnique($kode, $excludeId = null)
    {
        $query = Barang::where('kode_barang', $kode);  // ← UBAH INI: kode_internal jadi kode_barang
        
        if ($excludeId) {
            $query->where('id_barang', '!=', $excludeId);
        }
        
        return !$query->exists();
    }
    
    /**
     * Get available categories for dropdown
     */
    public static function getKategoriOptions()
    {
        return KategoriBarang::aktif()
                            ->select('id_kategori', 'nama_kategori', 'kode_kategori', 'icon', 'warna')
                            ->orderBy('nama_kategori')
                            ->get()
                            ->map(function($kategori) {
                                return [
                                    'value' => $kategori->id_kategori,
                                    'text' => $kategori->icon . ' ' . $kategori->nama_kategori,
                                    'kode' => $kategori->kode_kategori,
                                    'color' => $kategori->warna
                                ];
                            });
    }
    
    /**
     * Parse kode barang untuk mendapatkan info kategori
     */
    public static function parseKodeBarang($kode)
    {
        if (preg_match('/^([A-Z]{3})-(\d{4})$/', $kode, $matches)) {
            $kodeKategori = $matches[1];
            $sequence = (int)$matches[2];
            
            $kategori = KategoriBarang::where('kode_kategori', $kodeKategori)->first();
            
            return [
                'valid' => true,
                'kategori_kode' => $kodeKategori,
                'sequence' => $sequence,
                'kategori' => $kategori
            ];
        }
        
        return ['valid' => false];
    }
}