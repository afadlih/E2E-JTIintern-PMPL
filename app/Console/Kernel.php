<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The application's artisan commands.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\AutomationDaemon::class,
        \App\Console\Commands\StopDaemon::class,
        \App\Console\Commands\ClearDaemonState::class,
        \App\Console\Commands\SwitchMode::class,
        \App\Console\Commands\AutoStop::class,
    ];
}
