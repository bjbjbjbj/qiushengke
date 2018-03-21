<?php

namespace App\Console;

use App\Console\Match\Basketball\BasketballDetailCommands;
use App\Console\Match\Basketball\BasketballDetailIngCommands;
use App\Console\Match\BasketballMatchCommands;
use App\Console\Match\Football\FootballDetailCommands;
use App\Console\Match\Football\FootballDetailIngCommands;
use App\Console\Match\FootballMatchCommands;
use App\Console\Match\HourCommands;
use App\Console\Match\IndexCommands;
use App\Console\Match\IndexFiveCommands;
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
        IndexCommands::class,//index缓存 即时的 1分钟
        IndexFiveCommands::class,//index缓存 赛程赛果的 5分钟
        HourCommands::class,//一小时一次
        FootballDetailCommands::class,//足球比赛终端
        FootballDetailIngCommands::class,//足球比赛终端
        //篮球
        BasketballDetailCommands::class,
        BasketballDetailIngCommands::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //首页静态化
        $schedule->command('index_cache:run')->everyMinute();
        $schedule->command('index_five_cache:run')->everyFiveMinutes();

        //通用页面静态化 现在只有odd.html
        $schedule->command('hour_cache:run')->hourly();

        //比赛终端静态化
        $schedule->command('fb_ing_detail_cache:run')->everyFiveMinutes();//正在比赛的足球赛事终端每分五种静态化一次。
        $schedule->command('fb_detail_cache:run')->everyTenMinutes();//每10分钟执行一次 每次缓存15个页面(赛程赛果各15)
        $schedule->command('bb_ing_detail_cache:run')->everyFiveMinutes();//正在比赛的篮球赛事终端每分五种静态化一次。
        $schedule->command('bb_detail_cache:run')->everyTenMinutes();//每10分钟执行一次 每次缓存15个页面(赛程赛果各15)


        $schedule->command('football_matches_in_db:run')->everyTenMinutes();
        $schedule->command('basketball_matches_in_db:run')->everyTenMinutes();
        $schedule->command('inspire')
            ->hourly();
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
