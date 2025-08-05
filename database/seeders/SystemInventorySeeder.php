<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemInventorySeeder extends Seeder
{
    public function run()
    {
        $sqlFile = database_path('system_inventory.sql');
        
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            
            // Split berdasarkan semicolon
            $statements = array_filter(
                array_map('trim', explode(';', $sql))
            );
            
            $this->command->info('Found ' . count($statements) . ' SQL statements');
            
            DB::unprepared('SET FOREIGN_KEY_CHECKS=0;');
            
            // Progress bar
            $bar = $this->command->getOutput()->createProgressBar(count($statements));
            $bar->start();
            
            foreach ($statements as $index => $statement) {
                if (!empty($statement)) {
                    try {
                        DB::unprepared($statement . ';');
                        $bar->advance();
                    } catch (\Exception $e) {
                        $this->command->error("Error at statement " . ($index + 1) . ": " . $e->getMessage());
                        break;
                    }
                }
            }
            
            $bar->finish();
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1;');
            
            $this->command->info("\nDatabase seeded successfully!");
        } else {
            $this->command->error('SQL file not found at: ' . $sqlFile);
        }
    }
}