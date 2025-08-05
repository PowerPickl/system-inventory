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
        Schema::table('restock_request', function (Blueprint $table) {
            $table->foreign(['id_user_approved'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['id_user_gudang'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['id_user_ordered'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['id_user_terminated'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restock_request', function (Blueprint $table) {
            $table->dropForeign('restock_request_id_user_approved_foreign');
            $table->dropForeign('restock_request_id_user_gudang_foreign');
            $table->dropForeign('restock_request_id_user_ordered_foreign');
            $table->dropForeign('restock_request_id_user_terminated_foreign');
        });
    }
};
