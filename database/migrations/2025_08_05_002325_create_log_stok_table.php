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
            $table->bigIncrements('id_log');
            $table->unsignedBigInteger('id_barang')->index();
            $table->dateTime('tanggal_log')->index();
            $table->enum('jenis_perubahan', ['Masuk', 'Keluar', 'Adjustment', 'Koreksi'])->index();
            $table->integer('qty_sebelum');
            $table->integer('qty_perubahan');
            $table->integer('qty_sesudah');
            $table->unsignedBigInteger('id_user')->index('log_stok_id_user_foreign');
            $table->string('referensi_tipe')->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

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
