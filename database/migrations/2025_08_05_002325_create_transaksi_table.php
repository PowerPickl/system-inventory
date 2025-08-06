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
            $table->bigIncrements('id_transaksi');
            $table->string('nomor_transaksi')->index();
            $table->dateTime('tanggal_transaksi')->index();
            $table->unsignedBigInteger('id_user')->index('transaksi_id_user_foreign');
            $table->string('nama_customer')->nullable();
            $table->string('kendaraan')->nullable();
            $table->decimal('total_harga', 12);
            $table->string('jenis_transaksi', 100);
            $table->enum('status_transaksi', ['Progress', 'Selesai', 'Dibatalkan'])->default('Progress')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['nomor_transaksi']);
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
