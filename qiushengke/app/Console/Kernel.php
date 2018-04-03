<?php

namespace App\Console;

use App\Console\League\BasketballCommands;
use App\Console\League\FootballCommands;
use App\Console\Live\AKQCommands;
use App\Console\Live\LiveCommands;
use App\Console\Live\LiveDetailJsonCommands;
use App\Console\Live\LivingCommands;
use App\Console\Live\PlayerJsonCommands;
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
        //篮球终端
        BasketballDetailCommands::class,
        BasketballDetailIngCommands::class,
        //专题
        FootballCommands::class,
        BasketballCommands::class,
        //直播终端
        LiveCommands::class,
        LivingCommands::class,
        //json
        LiveDetailJsonCommands::class,
        PlayerJsonCommands::class,
        //爱看球同步直播数据
        AKQCommands::class,
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

        //专题静态化
        $schedule->command('league_foot:run')->dailyAt('7:10');
        $schedule->command('league_foot:run')->dailyAt('14:10');
        $schedule->command('league_basket:run')->dailyAt('15:10');
        $schedule->command('league_basket:run')->dailyAt('11:10');

        //拿爱看球数据
        $schedule->command('akq_data:run')->everyFiveMinutes();

        //直播终端静态化
        $schedule->command('live_detail_cache:run')->everyTenMinutes();
        $schedule->command('living_detail_cache:run')->everyFiveMinutes();
        $schedule->command('live_detail_json_cache:run')->everyFiveMinutes();
        $schedule->command('player_json_cache:run')->everyFiveMinutes();//播放的频道静态化

        //其他
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
