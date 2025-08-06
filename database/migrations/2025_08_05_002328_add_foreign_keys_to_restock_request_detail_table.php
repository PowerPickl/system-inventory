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
        Schema::table('restock_request_detail', function (Blueprint $table) {
            $table->foreign(['id_barang'])->references(['id_barang'])->on('barang')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_request'])->references(['id_request'])->on('restock_request')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restock_request_detail', function (Blueprint $table) {
            $table->dropForeign('restock_request_detail_id_barang_foreign');
            $table->dropForeign('restock_request_detail_id_request_foreign');
        });
    }
};
