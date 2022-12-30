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
        Commands\WppRecordTask::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('wpprecord:task')->timezone('America/Argentina/Buenos_Aires')->twiceDaily(10, 11);
        $schedule->command('wppservok:task')->timezone('America/Argentina/Buenos_Aires')->everyThirtyMinutes();
        
        $schedule->command('cargatablaausentes:task')->timezone('America/Argentina/Buenos_Aires')->dailyAt('07:00');
        $schedule->command('wppencausentes:task')->timezone('America/Argentina/Buenos_Aires')->dailyAt('17:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
