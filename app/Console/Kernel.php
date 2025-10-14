<?php

namespace App\Console;

use App\Console\Commands\AggregateNews;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log; // Import the Log facade

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AggregateNews::class
    ];

    /**
     * Define the application's command schedule.
     *
     * This method is where you register all commands that Laravel should run
     * automatically at specified intervals (e.g., daily, hourly).
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:aggregate-news')->everyMinute(); //->hourly(); 
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}