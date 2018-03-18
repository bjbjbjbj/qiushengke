<?php

namespace App\Console;

use App\Console\Match\BasketballMatchCommands;
use App\Console\Match\FootballMatchCommands;
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
        FootballMatchCommands::class,//同步-1 - 3天内的指定足球赛事的比赛
        BasketballMatchCommands::class,//同步-1 - 3天内的指定篮球赛事的比赛
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('football_matches_in_db:run')->everyTenMinutes();
        $schedule->command('basketball_matches_in_db:run')->everyTenMinutes();
        // $schedule->command('inspire')
        //          ->hourly();
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
