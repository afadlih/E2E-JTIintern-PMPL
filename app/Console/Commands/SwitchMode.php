<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;

class SwitchMode extends Command
{
    protected $signature = 'automation:mode {mode : debug|production}';
    protected $description = 'Switch automation between debug and production mode';

    public function handle()
    {
        $mode = $this->argument('mode');
        
        if ($mode === 'debug') {
            $this->info('🔧 Switching to DEBUG mode:');
            $this->line('  - Interval: 120 seconds (2 minutes)');
            $this->line('  - Batch size: 3 records');
            $this->line('  - Cache: 4 minutes');
            $this->line('  - Business hours: DISABLED');
            
        } elseif ($mode === 'production') {
            $this->info('🚀 Switching to PRODUCTION mode:');
            $this->line('  - Interval: 3600 seconds (1 hour)');
            $this->line('  - Batch size: 20 records');
            $this->line('  - Cache: 1-2 hours');
            $this->line('  - Business hours: ENABLED (8AM-5PM)');
            
        } else {
            $this->error('Invalid mode. Use: debug or production');
            return 1;
        }
        
        $this->warn('⚠️  Don\'t forget to update .env APP_ENV setting!');
        $this->info('🔄 Restart daemon: php artisan automation:stop && php artisan serve');
    }
}