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
        Schema::create('stok', function (Blueprint $table) {
            $table->bigIncrements('id_stok');
            $table->unsignedBigInteger('id_barang')->index();
            $table->integer('jumlah_stok')->default(0);
            $table->enum('status_stok', ['Aman', 'Perlu Restock', 'Habis'])->default('Aman');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};
