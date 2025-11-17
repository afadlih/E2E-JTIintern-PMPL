<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDaemonState extends Command
{
    protected $signature = 'automation:clear';
    protected $description = 'Clear all daemon state and cache';

    public function handle()
    {
        try {
            // 1. Kill processes
            if (PHP_OS_FAMILY === 'Windows') {
                exec('taskkill /F /IM php.exe 2>NUL');
            }

            // 2. Remove lock file
            $lockFile = storage_path('automation.lock');
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }

            // 3. Clear all caches
            Cache::forget('automation_start_attempt');
            Cache::forget('last_auto_completion');
            Cache::forget('auto_completion_stats');
            
            $this->call('cache:clear');
            $this->call('config:clear');

            $this->info('✅ All daemon state cleared');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}