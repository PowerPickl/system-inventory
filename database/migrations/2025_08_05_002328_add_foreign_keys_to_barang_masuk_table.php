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
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign(['id_request'])->references(['id_request'])->on('restock_request')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['id_user_gudang'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign('barang_masuk_id_request_foreign');
            $table->dropForeign('barang_masuk_id_user_gudang_foreign');
        });
    }
};
