<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SimpleAutomationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutomationDaemon extends Command
{
    // ✅ FIX: Remove config dependency
    protected $signature = 'automation:daemon {--interval=120} {--auto-start}';
    protected $description = 'Run automation daemon that checks periodically';

    private $running = true;

    // ✅ FIX: Remove config dari constructor
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // ✅ FIX: Get interval dari option atau default environment-based
        $isDebugMode = config('app.env') === 'local';
        $defaultInterval = $isDebugMode ? 120 : 3600;
        
        $interval = $this->option('interval') ? (int) $this->option('interval') : $defaultInterval;
        $isQuiet = $this->option('auto-start');
        
        // ✅ VALIDATION: Simple validation
        if ($interval < 10) {
            $interval = $defaultInterval;
            if (!$isQuiet) {
                $this->warn("⚠️  Invalid interval. Using environment default: {$interval}s");
            }
        }
        
        // ✅ REGISTER: Cleanup on exit
        register_shutdown_function([$this, 'cleanup']);
        
        // ✅ LOG: Start daemon info
        $startMessage = "🤖 Automation Daemon Started - Interval: {$interval}s";
        if (!$isQuiet) {
            $this->info($startMessage);
            $this->info("🔄 Press Ctrl+C to stop");
            $this->newLine();
        }
        
        Log::info($startMessage, [
            'interval' => $interval,
            'auto_start' => $isQuiet,
            'pid' => getmypid()
        ]);

        // ✅ SIGNAL HANDLING untuk graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'handleSignal']);
            pcntl_signal(SIGINT, [$this, 'handleSignal']);
        }

        $service = new SimpleAutomationService();
        $lastRun = null;
        $runCount = 0;

        while ($this->running) {
            try {
                $now = Carbon::now();
                $runCount++;
                
                if (!$isQuiet) {
                    $this->line("[{$now->format('Y-m-d H:i:s')}] 🔍 Checking for expired magang... (Run #{$runCount})");
                }
                
                // ✅ RUN: Automation completion
                $result = $service->autoCompleteExpired();
                
                // ✅ NEW: Check expired magang need evaluation
                $evaluationResult = $service->checkExpiredNeedEvaluation();
                
                if ($result['success']) {
                    if ($result['completed'] > 0) {
                        $message = "✅ Automation completed {$result['completed']} magang";
                        if (!$isQuiet) $this->info($message);
                        
                        Log::info('🎯 Automation successful completion', [
                            'completed' => $result['completed'],
                            'failed' => $result['failed'],
                            'evaluation_reminders' => $evaluationResult['notifications_sent'] ?? 0,
                            'expired_need_evaluation' => $evaluationResult['expired_need_evaluation_count'] ?? 0,
                            'run_count' => $runCount,
                            'interval' => $interval
                        ]);
                    } else {
                        // ✅ LOG: Include evaluation check result
                        if ($runCount % 6 === 0) {
                            if (!$isQuiet) {
                                $this->line("ℹ️  No expired magang found (Run #{$runCount})");
                                if ($evaluationResult['success'] && $evaluationResult['expired_need_evaluation_count'] > 0) {
                                    $this->line("📝 {$evaluationResult['expired_need_evaluation_count']} expired magang need evaluation reminder");
                                }
                            }
                            
                            Log::info('📊 Automation periodic status', [
                                'status' => 'no_expired_magang',
                                'evaluation_check' => $evaluationResult,
                                'run_count' => $runCount,
                                'interval' => $interval
                            ]);
                        }
                    }
                } else {
                    $message = "❌ Automation failed: " . $result['error'];
                    if (!$isQuiet) $this->error($message);
                    
                    Log::error('💥 Automation daemon error', [
                        'error' => $result['error'],
                        'run_count' => $runCount,
                        'interval' => $interval
                    ]);
                }

                $lastRun = $now;

                // ✅ WAIT: Sleep until next check
                if (!$isQuiet && $runCount <= 3) {
                    $this->line("💤 Sleeping for {$interval} seconds...");
                    $this->newLine();
                } elseif ($runCount === 4 && !$isQuiet) {
                    $this->line("🔄 Daemon is now running in background mode...");
                    $this->newLine();
                }
                
                sleep($interval);

                // ✅ SIGNAL CHECK (untuk Unix systems)
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

            } catch (\Exception $e) {
                $message = "💥 Daemon error: " . $e->getMessage();
                if (!$isQuiet) $this->error($message);
                
                Log::error('💥 Automation daemon exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'run_count' => $runCount,
                    'interval' => $interval
                ]);
                
                if (!$isQuiet) $this->line("⏳ Waiting 60 seconds before retry...");
                sleep(1);
            }
        }

        // ✅ CLEANUP: On normal exit
        $this->cleanup();
        
        $stopMessage = "🛑 Automation Daemon stopped gracefully";
        if (!$isQuiet) {
            $this->newLine();
            $this->info($stopMessage);
            $this->info("📊 Last run: " . ($lastRun ? $lastRun->format('Y-m-d H:i:s') : 'Never'));
            $this->info("📈 Total runs: {$runCount}");
        }
        
        Log::info($stopMessage, [
            'total_runs' => $runCount,
            'last_run' => $lastRun ? $lastRun->toDateTimeString() : 'Never',
            'stopped_at' => now()->toDateTimeString()
        ]);
    }

    // ✅ NEW: Cleanup method
    public function cleanup()
    {
        try {
            $lockFile = storage_path('automation.lock');
            if (file_exists($lockFile)) {
                unlink($lockFile);
                Log::info('🧹 Cleanup: Removed lock file');
            }
        } catch (\Exception $e) {
            Log::warning('⚠️ Cleanup error: ' . $e->getMessage());
        }
    }

    /**
     * ✅ HANDLE: Graceful shutdown signals
     */
    public function handleSignal($signal)
    {
        if (!$this->option('auto-start')) {
            $this->newLine();
            $this->warn("🔔 Received shutdown signal: {$signal}");
        }
        
        $this->running = false;
        $this->cleanup();
    }

    /**
     * ✅ AUTO START: Static method untuk dipanggil dari AppServiceProvider
     */
    public static function autoStart($interval = 120)
    {
        try {
            $lockFile = storage_path('automation.lock');
            
            if (file_exists($lockFile)) {
                $lockContent = file_get_contents($lockFile);
                $lockData = json_decode($lockContent, true);
                
                if ($lockData && isset($lockData['pid']) && self::isProcessRunning($lockData['pid'])) {
                    return false;
                }
                
                unlink($lockFile);
            }
            
            // ✅ REMOVE: Interval validation untuk debug
            // Langsung terima interval yang diberikan
            
            // ✅ START: Background process
            $command = self::getBuildCommand($interval);
            
            Log::info('🎯 Starting daemon with command', [
                'command' => $command,
                'interval' => $interval
            ]);
            
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows: Start in background
                pclose(popen($command, 'r'));
                $pid = 'unknown';
            } else {
                // Unix: Start in background and save PID
                $pid = exec($command . ' & echo $!');
            }
            
            // ✅ SAVE: Lock file dengan info lengkap
            $lockData = [
                'pid' => $pid,
                'started_at' => now()->toDateTimeString(),
                'interval' => $interval,
                'command' => $command
            ];
            file_put_contents($lockFile, json_encode($lockData));
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("❌ Failed to auto-start daemon", [
                'error' => $e->getMessage(),
                'interval' => $interval
            ]);
            return false;
        }
    }

    /**
     * ✅ BUILD: Command tanpa --quiet flag
     */
    private static function getBuildCommand($interval)
    {
        $artisan = base_path('artisan');
        
        if (PHP_OS_FAMILY === 'Windows') {
            return "start /B php \"{$artisan}\" automation:daemon --interval={$interval} --auto-start";
        } else {
            return "php \"{$artisan}\" automation:daemon --interval={$interval} --auto-start > /dev/null 2>&1";
        }
    }

    /**
     * ✅ CHECK: Process running
     */
    private static function isProcessRunning($pid)
    {
        if ($pid === 'unknown') {
            return false;
        }
        
        if (PHP_OS_FAMILY === 'Windows') {
            $result = shell_exec("tasklist /FI \"PID eq {$pid}\" 2>NUL");
            return $result && strpos($result, (string)$pid) !== false;
        } else {
            return file_exists("/proc/{$pid}");
        }
    }

    /**
     * ✅ STOP: Daemon
     */
    public static function stopDaemon()
    {
        try {
            $lockFile = storage_path('automation.lock');
            
            if (file_exists($lockFile)) {
                $lockContent = file_get_contents($lockFile);
                $lockData = json_decode($lockContent, true);
                
                if ($lockData && isset($lockData['pid'])) {
                    $pid = $lockData['pid'];
                    
                    if (PHP_OS_FAMILY === 'Windows') {
                        exec("taskkill /PID {$pid} /F 2>NUL");
                    } else {
                        exec("kill {$pid} 2>/dev/null");
                    }
                    
                    Log::info("🛑 Automation daemon stopped manually", [
                        'pid' => $pid,
                        'started_at' => $lockData['started_at'] ?? 'unknown',
                        'stopped_at' => now()->toDateTimeString()
                    ]);
                }
                
                unlink($lockFile);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error("❌ Failed to stop daemon: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ CHECK: Daemon status
     */
    public static function isDaemonRunning()
    {
        $lockFile = storage_path('automation.lock');
        
        if (!file_exists($lockFile)) {
            return false;
        }
        
        $lockContent = file_get_contents($lockFile);
        $lockData = json_decode($lockContent, true);
        
        if (!$lockData || !isset($lockData['pid'])) {
            return false;
        }
        
        return self::isProcessRunning($lockData['pid']);
    }
}