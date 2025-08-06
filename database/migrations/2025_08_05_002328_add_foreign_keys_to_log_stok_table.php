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
        Schema::table('log_stok', function (Blueprint $table) {
            $table->foreign(['id_barang'])->references(['id_barang'])->on('barang')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_user'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_stok', function (Blueprint $table) {
            $table->dropForeign('log_stok_id_barang_foreign');
            $table->dropForeign('log_stok_id_user_foreign');
        });
    }
};
