<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->bigIncrements('id_kategori');
            $table->string('nama_kategori', 100);
            $table->string('kode_kategori', 10)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('icon', 50)->default('?');
            $table->string('warna', 20)->default('#6B7280');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_barang');
    }
};
