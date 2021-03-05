<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CleanMedia;
use App\Console\Commands\BackupDatabase;
use App\Console\Commands\ClearQueryLogFile;
use App\Console\Commands\ItourMobile\RestoreItourMobile;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CleanMedia::class,
        RestoreITourMobile::class,
        BackupDatabase::class,
        ClearQueryLogFile::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('media:clean')->daily();

        $schedule->command('analytics:summary')->everyTenMinutes();
        // Daily db backup
        $schedule->command('db:backup')->daily();
        //monthly query log clean
        $schedule->command('querylog:clear')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
