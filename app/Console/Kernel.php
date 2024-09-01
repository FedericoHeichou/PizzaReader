<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        Commands\DownloadsClear::class,
        Commands\RatingsClear::class,
        Commands\ThumbnailResize::class,
        Commands\ViewsClear::class,
        Commands\PagesCheck::class,
        Commands\CacheGC::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('views:clear --days='.config('app.cron_views_clear_days'))->daily();
        $schedule->command('cache:gc')->hourly();
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
