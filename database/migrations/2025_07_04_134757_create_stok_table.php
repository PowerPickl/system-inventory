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
        Schema::create('stok', function (Blueprint $table) {
            $table->id('id_stok');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah_stok')->default(0);
            $table->enum('status_stok', ['Aman', 'Perlu Restock', 'Habis'])->default('Aman');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            
            // Index for better performance
            $table->index('id_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};