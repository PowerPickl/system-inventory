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
        Schema::table('barang_masuk_detail', function (Blueprint $table) {
            $table->foreign(['id_barang'])->references(['id_barang'])->on('barang')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_masuk'])->references(['id_masuk'])->on('barang_masuk')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk_detail', function (Blueprint $table) {
            $table->dropForeign('barang_masuk_detail_id_barang_foreign');
            $table->dropForeign('barang_masuk_detail_id_masuk_foreign');
        });
    }
};
