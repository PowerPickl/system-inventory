<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100);
            $table->string('kode_kategori', 10)->unique(); // FLD, FLT, ENG, etc.
            $table->text('deskripsi')->nullable();
            $table->string('icon', 50)->default('📦'); // Emoji icon
            $table->string('warna', 20)->default('#6B7280'); // Hex color
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Insert default categories for bengkel mobil
        DB::table('kategori_barang')->insert([
            [
                'nama_kategori' => 'Fluids',
                'kode_kategori' => 'FLD',
                'deskripsi' => 'Oli mesin, coolant, brake fluid, power steering',
                'icon' => '🛢️',
                'warna' => '#DC2626',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kategori' => 'Filters',
                'kode_kategori' => 'FLT',
                'deskripsi' => 'Filter oli, udara, fuel, AC, transmisi',
                'icon' => '🔍',
                'warna' => '#059669',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kategori' => 'Engine Parts',
                'kode_kategori' => 'ENG',
                'deskripsi' => 'Busi, timing belt, gasket, water pump',
                'icon' => '⚙️',
                'warna' => '#2563EB',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kategori' => 'Electrical',
                'kode_kategori' => 'ELC',
                'deskripsi' => 'Aki, alternator, starter, lampu, fuse, relay',
                'icon' => '🔌',
                'warna' => '#7C3AED',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kategori' => 'Body Parts',
                'kode_kategori' => 'BDY',
                'deskripsi' => 'Spion, bumper, kaca, wiper, trim',
                'icon' => '🚗',
                'warna' => '#EA580C',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kategori' => 'Consumables',
                'kode_kategori' => 'CSM',
                'deskripsi' => 'Lap, pembersih, grease, seal, gasket kecil',
                'icon' => '📄',
                'warna' => '#65A30D',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_kategori' => 'Tools',
                'kode_kategori' => 'TOL',
                'deskripsi' => 'Diagnostic tools, kompressor, peralatan bengkel',
                'icon' => '🔧',
                'warna' => '#0891B2',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('kategori_barang');
    }
};