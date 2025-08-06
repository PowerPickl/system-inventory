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
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->bigIncrements('id_detail');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_barang')->index('detail_transaksi_id_barang_foreign');
            $table->integer('qty');
            $table->decimal('harga_satuan', 10);
            $table->decimal('subtotal', 12);
            $table->enum('status_permintaan', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['id_transaksi', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
