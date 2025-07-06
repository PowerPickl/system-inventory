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
        Schema::create('barang_masuk_detail', function (Blueprint $table) {
            $table->id('id_masuk_detail');
            $table->unsignedBigInteger('id_masuk');
            $table->unsignedBigInteger('id_barang');
            $table->integer('qty_masuk');
            $table->decimal('harga_beli_satuan', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->date('tanggal_expired')->nullable();
            $table->string('batch_number')->nullable();
            $table->text('keterangan_detail')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_masuk')->references('id_masuk')->on('barang_masuk')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            
            // Indexes
            $table->index(['id_masuk', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk_detail');
    }
};