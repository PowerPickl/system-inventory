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
        Schema::create('log_stok', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_barang');
            $table->datetime('tanggal_log');
            $table->enum('jenis_perubahan', ['Masuk', 'Keluar', 'Adjustment', 'Koreksi']);
            $table->integer('qty_sebelum');
            $table->integer('qty_perubahan'); // bisa + atau -
            $table->integer('qty_sesudah');
            $table->unsignedBigInteger('id_user'); // user yang melakukan perubahan
            $table->string('referensi_tipe')->nullable(); // transaksi, barang_masuk, dll
            $table->unsignedBigInteger('referensi_id')->nullable(); // id dari referensi
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for audit trail queries
            $table->index('id_barang');
            $table->index('tanggal_log');
            $table->index('jenis_perubahan');
            $table->index(['referensi_tipe', 'referensi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_stok');
    }
};