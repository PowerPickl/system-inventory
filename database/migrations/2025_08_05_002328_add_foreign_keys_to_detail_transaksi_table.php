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
        Schema::table('detail_transaksi', function (Blueprint $table) {
            $table->foreign(['id_barang'])->references(['id_barang'])->on('barang')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_transaksi'])->references(['id_transaksi'])->on('transaksi')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_transaksi', function (Blueprint $table) {
            $table->dropForeign('detail_transaksi_id_barang_foreign');
            $table->dropForeign('detail_transaksi_id_transaksi_foreign');
        });
    }
};
