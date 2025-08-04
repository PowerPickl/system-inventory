<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Ubah enum jadi string biar bisa nerima semua jenis service
            $table->string('jenis_transaksi', 100)->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Rollback ke enum kalau perlu
            $table->enum('jenis_transaksi', ['Service', 'Penjualan Sparepart'])->default('Service')->change();
        });
    }
};