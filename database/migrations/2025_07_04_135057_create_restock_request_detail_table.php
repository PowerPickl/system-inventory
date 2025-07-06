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
        Schema::create('restock_request_detail', function (Blueprint $table) {
            $table->id('id_request_detail');
            $table->unsignedBigInteger('id_request');
            $table->unsignedBigInteger('id_barang');
            $table->integer('qty_request'); // jumlah yang diminta gudang
            $table->integer('qty_approved')->nullable(); // jumlah yang disetujui owner
            $table->decimal('estimasi_harga', 12, 2)->nullable(); // estimasi cost
            $table->text('alasan_request')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_request')->references('id_request')->on('restock_request')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            
            // Indexes
            $table->index(['id_request', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restock_request_detail');
    }
};