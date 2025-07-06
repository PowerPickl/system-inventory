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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('nomor_transaksi')->unique(); // TRX001, TRX002, etc
            $table->datetime('tanggal_transaksi');
            $table->unsignedBigInteger('id_user'); // kasir yang handle
            $table->string('nama_customer')->nullable();
            $table->string('kendaraan')->nullable(); // Honda Beat, Yamaha Mio, etc
            $table->decimal('total_harga', 12, 2);
            $table->enum('jenis_transaksi', ['Service', 'Penjualan Sparepart'])->default('Service');
            $table->enum('status_transaksi', ['Progress', 'Selesai', 'Dibatalkan'])->default('Progress');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('nomor_transaksi');
            $table->index('tanggal_transaksi');
            $table->index('status_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};