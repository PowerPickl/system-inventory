<?php

namespace App\Console\Commands;

use App\Jobs\UpdateEOQCalculations;
use Illuminate\Console\Command;

class UpdateEOQCommand extends Command
{
    protected $signature = 'eoq:update {--force : Force update all items}';
    protected $description = 'Update EOQ calculations for all items';

    public function handle()
    {
        $force = $this->option('force');
        
        $this->info('Starting EOQ calculations...');
        
        UpdateEOQCalculations::dispatch(null, $force);
        
        $this->info('EOQ calculation job dispatched!');
        
        return Command::SUCCESS;
    }
}