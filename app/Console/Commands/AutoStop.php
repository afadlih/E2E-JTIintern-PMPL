<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoStop extends Command
{
    protected $signature = 'automation:auto-stop';
    protected $description = 'Auto stop daemon when web server stops';

    public function handle()
    {
        try {
            $this->info('🛑 Auto-stopping automation daemon...');
            
            // Kill all PHP automation processes
            if (PHP_OS_FAMILY === 'Windows') {
                exec('taskkill /F /IM php.exe 2>NUL');
            } else {
                exec('pkill -f "automation:daemon"');
            }

            // Remove lock file
            $lockFile = storage_path('automation.lock');
            if (file_exists($lockFile)) {
                unlink($lockFile);
                $this->info('✅ Lock file removed');
            }

            // Clear cache
            Cache::flush();

            $this->info('✅ All automation processes stopped');
            Log::info('🛑 Auto-stop completed');

        } catch (\Exception $e) {
            $this->error('❌ Auto-stop error: ' . $e->getMessage());
        }
    }
}