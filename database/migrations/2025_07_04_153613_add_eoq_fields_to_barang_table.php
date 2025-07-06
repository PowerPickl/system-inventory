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
        Schema::table('barang', function (Blueprint $table) {
            // EOQ Variables
            $table->decimal('annual_demand', 10, 2)->nullable()->after('reorder_point'); // D
            $table->decimal('ordering_cost', 10, 2)->default(5000)->after('annual_demand'); // S  
            $table->decimal('holding_cost', 10, 2)->nullable()->after('ordering_cost'); // H
            
            // Safety Stock Variables
            $table->decimal('demand_avg_daily', 8, 2)->nullable()->after('holding_cost'); // davg
            $table->decimal('demand_max_daily', 8, 2)->nullable()->after('demand_avg_daily'); // dmax
            
            // Calculated Results (auto-computed)
            $table->integer('eoq_calculated')->nullable()->after('eoq_qty'); // Hasil EOQ
            $table->integer('safety_stock')->nullable()->after('eoq_calculated'); // SS
            $table->integer('rop_calculated')->nullable()->after('safety_stock'); // ROP
            
            // Metadata
            $table->timestamp('last_eoq_calculation')->nullable()->after('rop_calculated');
            $table->text('eoq_notes')->nullable()->after('last_eoq_calculation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn([
                'annual_demand',
                'ordering_cost', 
                'holding_cost',
                'demand_avg_daily',
                'demand_max_daily',
                'eoq_calculated',
                'safety_stock',
                'rop_calculated',
                'last_eoq_calculation',
                'eoq_notes'
            ]);
        });
    }
};