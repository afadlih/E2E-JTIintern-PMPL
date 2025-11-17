<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Console\Commands\AutomationDaemon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ SIMPLE: Support both local AND production tanpa business hours check
        if (!app()->runningInConsole()) {
            $this->startAutomationSimple();
        }
    }

    private function startAutomationSimple()
    {
        try {
            $environment = config('app.env');
            $isDebugMode = $environment === 'local';
            
            // ✅ REMOVE: Business hours check yang bikin hang
            // No business hours check - automation bisa jalan 24/7
            
            // ✅ CHECK: Daemon already running
            if (AutomationDaemon::isDaemonRunning()) {
                return;
            }
            
            // ✅ RATE LIMITING: Environment-based
            $rateLimitMinutes = $isDebugMode ? 1 : 30; // Debug: 1 min, Production: 30 min
            $lastAttempt = Cache::get('automation_start_attempt');
            
            if ($lastAttempt && now()->diffInMinutes(Carbon::parse($lastAttempt)) < $rateLimitMinutes) {
                return;
            }
            
            Log::info('🚀 Starting automation daemon for environment: ' . $environment);
            
            // ✅ INTERVAL: Environment-based
            $interval = $isDebugMode ? 120 : 3600; // Debug: 2 min, Production: 1 hour
            
            // ✅ START: Daemon
            $result = AutomationDaemon::autoStart($interval);
            
            if ($result) {
                // ✅ CACHE: Environment-based rate limit
                Cache::put('automation_start_attempt', now(), $rateLimitMinutes * 60);
                
                Log::info('✅ Automation daemon started successfully', [
                    'environment' => $environment,
                    'interval' => $interval,
                    'rate_limit_minutes' => $rateLimitMinutes
                ]);
            } else {
                Log::error('❌ Failed to start automation daemon', [
                    'environment' => $environment
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('💥 Error starting automation: ' . $e->getMessage());
        }
    }
}