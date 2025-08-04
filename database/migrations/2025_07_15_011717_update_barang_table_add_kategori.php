<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('barang', function (Blueprint $table) {
            // Add kategori relationship
            $table->unsignedBigInteger('id_kategori')->nullable()->after('id_barang');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori_barang')->onDelete('set null');
            
            // Auto-generated internal code
            $table->string('kode_internal', 20)->nullable()->after('kode_barang')->unique();
            $table->integer('sequence_number')->default(1)->after('kode_internal');
            
            // Additional product info (simplified)
            $table->string('merk', 100)->nullable()->after('nama_barang');
            $table->string('model_tipe', 100)->nullable()->after('merk');
            $table->text('keterangan_detail')->nullable()->after('deskripsi'); // Detailed specs
        });
    }

    public function down()
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['id_kategori']);
            $table->dropColumn([
                'id_kategori', 
                'kode_internal', 
                'sequence_number',
                'merk', 
                'model_tipe', 
                'keterangan_detail'
            ]);
        });
    }
};