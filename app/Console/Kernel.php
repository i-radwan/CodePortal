<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncJudgeProblems::class,
        Commands\SyncJudgeSubmissions::class,
        Commands\SyncNewHandles::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Run problems sync every day
        $schedule->command('sync:judge-problems --judge=codeforces')->daily();
        $schedule->command('sync:judge-problems --judge=uva')->daily();
        $schedule->command('sync:judge-problems --judge=live-archive')->daily();

        // Sync all submissions
        $schedule->command('sync:judge-submissions "*" --judge=codeforces')->everyMinute();
        $schedule->command('sync:judge-submissions "*" --judge=uva')->withoutOverlapping()->everyMinute();
        $schedule->command('sync:judge-submissions "*" --judge=live-archive')->withoutOverlapping()->everyMinute();

        // Sync submissions for new handles
        $schedule->command('sync:handles-submissions')->withoutOverlapping()->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
