<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Stok;
use App\Services\EOQCalculationService;

class EOQDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data manual dari spreadsheet lu
        $eoqData = [
            [
                'kode_barang' => 'RAD001',
                'nama_barang' => 'Radiator Coolant Texal',
                'satuan' => '5 LITER',
                'harga_beli' => 40100,
                'harga_jual' => 45000,
                'lead_time' => 2,
                'demand_harian' => 1,
                'dmax' => 2,
                // EOQ Variables
                'annual_demand' => 365,  // D = 1 × 365
                'ordering_cost' => 5000, // S
                'holding_cost' => 4010,  // H
                'demand_avg_daily' => 1,
                'demand_max_daily' => 2,
                // Manual calculated results (for verification)
                'eoq_manual' => 30,
                'rop_manual' => 2,
                'ss_manual' => 2
            ],
            [
                'kode_barang' => 'OIL001',
                'nama_barang' => 'Minyak Rem Prestone',
                'satuan' => '300ML',
                'harga_beli' => 28000,
                'harga_jual' => 32000,
                'lead_time' => 2,
                'demand_harian' => 3,
                'dmax' => 4,
                'annual_demand' => 1095, // 3 × 365
                'ordering_cost' => 5000,
                'holding_cost' => 2800,
                'demand_avg_daily' => 3,
                'demand_max_daily' => 4,
                'eoq_manual' => 63,
                'rop_manual' => 6,
                'ss_manual' => 2
            ],
            [
                'kode_barang' => 'OIL002',
                'nama_barang' => 'Minyak Rem Jumbo',
                'satuan' => '300ML',
                'harga_beli' => 26000,
                'harga_jual' => 30000,
                'lead_time' => 2,
                'demand_harian' => 3,
                'dmax' => 4,
                'annual_demand' => 1095,
                'ordering_cost' => 5000,
                'holding_cost' => 2600,
                'demand_avg_daily' => 3,
                'demand_max_daily' => 4,
                'eoq_manual' => 65,
                'rop_manual' => 6,
                'ss_manual' => 2
            ],
            [
                'kode_barang' => 'CLN001',
                'nama_barang' => 'Carbon Cleaner',
                'satuan' => '500ML',
                'harga_beli' => 32500,
                'harga_jual' => 37000,
                'lead_time' => 3,
                'demand_harian' => 4,
                'dmax' => 5,
                'annual_demand' => 1460, // 4 × 365
                'ordering_cost' => 5000,
                'holding_cost' => 3250,
                'demand_avg_daily' => 4,
                'demand_max_daily' => 5,
                'eoq_manual' => 67,
                'rop_manual' => 12,
                'ss_manual' => 3
            ],
            [
                'kode_barang' => 'CLN002',
                'nama_barang' => 'Injector Cleaner',
                'satuan' => '300ML',
                'harga_beli' => 28000,
                'harga_jual' => 32000,
                'lead_time' => 3,
                'demand_harian' => 4,
                'dmax' => 5,
                'annual_demand' => 1460,
                'ordering_cost' => 5000,
                'holding_cost' => 2800,
                'demand_avg_daily' => 4,
                'demand_max_daily' => 5,
                'eoq_manual' => 72,
                'rop_manual' => 12,
                'ss_manual' => 3
            ],
            [
                'kode_barang' => 'OIL003',
                'nama_barang' => 'WD-40',
                'satuan' => '191ML',
                'harga_beli' => 55000,
                'harga_jual' => 62000,
                'lead_time' => 3,
                'demand_harian' => 0.33,
                'dmax' => 1,
                'annual_demand' => 120, // 0.33 × 365
                'ordering_cost' => 5000,
                'holding_cost' => 5500,
                'demand_avg_daily' => 0.33,
                'demand_max_daily' => 1,
                'eoq_manual' => 15,
                'rop_manual' => 1,
                'ss_manual' => 2
            ]
        ];

        echo "🌱 Seeding EOQ data...\n";

        foreach ($eoqData as $data) {
            // Create barang
            $barang = Barang::create([
                'kode_barang' => $data['kode_barang'],
                'nama_barang' => $data['nama_barang'],
                'satuan' => $data['satuan'],
                'harga_beli' => $data['harga_beli'],
                'harga_jual' => $data['harga_jual'],
                'reorder_point' => $data['rop_manual'], // Use manual ROP as baseline
                'lead_time' => $data['lead_time'],
                
                // EOQ Variables
                'annual_demand' => $data['annual_demand'],
                'ordering_cost' => $data['ordering_cost'],
                'holding_cost' => $data['holding_cost'],
                'demand_avg_daily' => $data['demand_avg_daily'],
                'demand_max_daily' => $data['demand_max_daily'],
                
                // Manual results for comparison
                'eoq_qty' => $data['eoq_manual'],
                'deskripsi' => "Manual EOQ: {$data['eoq_manual']}, ROP: {$data['rop_manual']}, SS: {$data['ss_manual']}"
            ]);

            // Create initial stock (random between 10-50)
            $initialStock = rand(10, 50);
            Stok::create([
                'id_barang' => $barang->id_barang,
                'jumlah_stok' => $initialStock,
                'status_stok' => $initialStock <= $data['rop_manual'] ? 'Perlu Restock' : 'Aman'
            ]);

            echo "✅ Created: {$data['nama_barang']} (Stock: {$initialStock})\n";
        }

        echo "\n🧮 Running EOQ calculations...\n";

        // Calculate EOQ untuk semua barang yang baru dibuat
        $service = new EOQCalculationService();
        
        foreach (Barang::all() as $barang) {
            try {
                $result = $service->calculateAll($barang);
                
                if ($result['success']) {
                    $summary = $result['summary'];
                    echo "📊 {$summary['item']}: EOQ={$summary['eoq']}, SS={$summary['safety_stock']}, ROP={$summary['rop']}\n";
                } else {
                    echo "❌ Error calculating {$barang->nama_barang}: {$result['error']}\n";
                }
            } catch (\Exception $e) {
                echo "❌ Exception for {$barang->nama_barang}: {$e->getMessage()}\n";
            }
        }

        echo "\n🎉 EOQ data seeding completed!\n";
    }
}