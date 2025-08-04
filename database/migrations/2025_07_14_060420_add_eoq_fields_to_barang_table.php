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
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('barang', 'annual_demand')) {
                $table->decimal('annual_demand', 12, 2)->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('barang', 'ordering_cost')) {
                $table->decimal('ordering_cost', 12, 2)->nullable()->after('annual_demand');
            }
            if (!Schema::hasColumn('barang', 'holding_cost')) {
                $table->decimal('holding_cost', 5, 2)->nullable()->comment('Percentage per year')->after('ordering_cost');
            }
            if (!Schema::hasColumn('barang', 'demand_avg_daily')) {
                $table->decimal('demand_avg_daily', 10, 2)->nullable()->after('holding_cost');
            }
            if (!Schema::hasColumn('barang', 'demand_max_daily')) {
                $table->decimal('demand_max_daily', 10, 2)->nullable()->after('demand_avg_daily');
            }
            if (!Schema::hasColumn('barang', 'eoq_calculated')) {
                $table->integer('eoq_calculated')->nullable()->after('demand_max_daily');
            }
            if (!Schema::hasColumn('barang', 'rop_calculated')) {
                $table->integer('rop_calculated')->nullable()->after('eoq_calculated');
            }
            if (!Schema::hasColumn('barang', 'safety_stock')) {
                $table->integer('safety_stock')->nullable()->after('rop_calculated');
            }
            if (!Schema::hasColumn('barang', 'last_eoq_calculation')) {
                $table->timestamp('last_eoq_calculation')->nullable()->after('safety_stock');
            }
            
            // Add indexes for better performance
            $table->index(['annual_demand', 'ordering_cost', 'holding_cost'], 'idx_eoq_params');
            $table->index(['eoq_calculated', 'rop_calculated'], 'idx_eoq_results');
            $table->index('last_eoq_calculation', 'idx_last_calculation');
        });

        // Update existing records to have reasonable default values where missing
        // This is optional - you can comment out if you prefer to set manually
        DB::statement("
            UPDATE barang 
            SET 
                holding_cost = CASE 
                    WHEN holding_cost IS NULL THEN 20.00 
                    ELSE holding_cost 
                END,
                lead_time = CASE 
                    WHEN lead_time IS NULL THEN 7 
                    ELSE lead_time 
                END,
                ordering_cost = CASE 
                    WHEN ordering_cost IS NULL THEN GREATEST(harga_beli * 0.1, 50000) 
                    ELSE ordering_cost 
                END
            WHERE holding_cost IS NULL OR lead_time IS NULL OR ordering_cost IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_eoq_params');
            $table->dropIndex('idx_eoq_results');
            $table->dropIndex('idx_last_calculation');
            
            // Drop columns
            $table->dropColumn([
                'annual_demand',
                'ordering_cost', 
                'holding_cost',
                'demand_avg_daily',
                'demand_max_daily',
                'eoq_calculated',
                'rop_calculated',
                'safety_stock',
                'last_eoq_calculation'
            ]);
        });
    }
};