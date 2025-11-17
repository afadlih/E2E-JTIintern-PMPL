<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class StopDaemon extends Command
{
    protected $signature = 'automation:stop';
    protected $description = 'Stop automation daemon safely';

    public function handle()
    {
        try {
            // Kill PHP processes
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
            $this->call('cache:clear');
            $this->call('config:clear');

            $this->info('✅ All processes stopped and cache cleared');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}