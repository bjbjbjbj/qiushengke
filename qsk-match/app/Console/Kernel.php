<?php

namespace App\Console;

use App\Console\Commands\LineUpCommand;
use App\Console\Commands\MatchStatic\BasketballImmediateCommand;
use App\Console\Commands\MatchStatic\BasketballLeagueCommand;
use App\Console\Commands\MatchStatic\BasketballNextTerminalCommand;
use App\Console\Commands\MatchStatic\BasketballOddDaysCommand;
use App\Console\Commands\MatchStatic\BasketballRollChangeCommand;
use App\Console\Commands\MatchStatic\BasketballRollListChangeCommand;
use App\Console\Commands\MatchStatic\BasketballScheduleCommand;
use App\Console\Commands\MatchStatic\BasketballTechCommand;
use App\Console\Commands\MatchStatic\FootballCurTerminalCommand;
use App\Console\Commands\MatchStatic\FootballImmediateCommand;
use App\Console\Commands\MatchStatic\FootballLeagueCommand;
use App\Console\Commands\MatchStatic\FootballNextTerminalCommand;
use App\Console\Commands\MatchStatic\FootballOddDaysCommand;
use App\Console\Commands\MatchStatic\FootballRollChangeCommand;
use App\Console\Commands\MatchStatic\FootballRollListChangeCommand;
use App\Console\Commands\MatchStatic\FootballScheduleCommand;
use App\Console\Commands\MatchStatic\ScoreChangeDelCommand;
use App\Console\Commands\MatchStaticTool;
use App\Console\Commands\MatchTipCommand;
use App\Console\Commands\RefereeCommand;
use App\Console\Commands\SpiderBallbarCommand;
use App\Console\Commands\SpiderDayCommand;
use App\Console\Commands\SpiderLongZhuCommand;
use App\Console\Commands\SpiderQQSportCommand;
use App\Console\Commands\SpiderSportBetCommand;
use App\Console\Commands\SpiderTTZBCommand;
use App\Console\Commands\SpiderWinTenCommand;
use App\Console\Commands\WithoutTidMatchCommand;
use App\Http\Controllers\App\Match\MatchesController;
use App\Http\Controllers\App\MatchDetail\DetailController;
use App\Http\Controllers\WinSpider\basket\SpiderBasketController;
use App\Models\LiaoGouModels\MatchLive;
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
        //
        WithoutTidMatchCommand::class,
        SpiderBallbarCommand::class,
        SpiderTTZBCommand::class,
        SpiderSportBetCommand::class,
        SpiderWinTenCommand::class,
        MatchTipCommand::class,
        LineUpCommand::class,
        RefereeCommand::class,
        SpiderDayCommand::class,
        SpiderLongZhuCommand::class,
        SpiderQQSportCommand::class,
        BasketballImmediateCommand::class,
        FootballImmediateCommand::class,
        BasketballScheduleCommand::class,
        FootballScheduleCommand::class,
        BasketballNextTerminalCommand::class,
        FootballNextTerminalCommand::class,
        FootballCurTerminalCommand::class,
        BasketballRollChangeCommand::class,
        FootballRollChangeCommand::class,
        BasketballRollListChangeCommand::class,
        FootballRollListChangeCommand::class,
        BasketballOddDaysCommand::class,
        FootballOddDaysCommand::class,
        ScoreChangeDelCommand::class,
        BasketballTechCommand::class,
        BasketballLeagueCommand::class,
        FootballLeagueCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //足球爬虫相关
        $schedule->command('without_tid_match:run')->hourly();
        $schedule->command('spider_win_ten:run')->everyTenMinutes();
//        $schedule->command('match_tip:run')->hourly();
        $schedule->command('match_lineup:run')->everyTenMinutes();
        $schedule->command('referee_calculate:run')->hourly();

        //篮球爬虫相关
        $schedule->call(function (){
            $controller = new SpiderBasketController();
            $controller->spiderTeam();
        })->everyTenMinutes();
        $schedule->call(function (){
            $controller = new SpiderBasketController();
            $controller->spiderMatch();
            $controller->fillLiaogouMatch();
        })->everyFiveMinutes();
        $schedule->call(function (){
            $controller = new SpiderBasketController();
            $controller->spiderScheduleWithSeason();
        })->everyTenMinutes();
        $schedule->call(function (){
            $controller = new SpiderBasketController();
            $controller->spiderMatchToday();
            $controller->fillLiaogouMatchToday();
        })->everyFiveMinutes();

        //直播数据相关
//        $schedule->command('spider_sport365:run')->everyFiveMinutes();
//        $schedule->command('spider_wcj:run')->everyTenMinutes();
//        $schedule->command('spider_ttzb:run')->hourlyAt(10);
//        $schedule->command('spider_ballbar:run')->hourlyAt(5);
//        $schedule->command('day:run')->twiceDaily(1, 13);
//        $schedule->command('spider_input_lz:run')->twiceDaily(6, 18);//每五分钟抓取龙珠直播的五大联赛足球赛事
//        $schedule->command('spider_input_qqsport:run')->everyFiveMinutes();//腾讯体育,五分钟一次

        //新版比赛静态数据相关的定时任务
        $schedule->command('static_basket_immediate:run')->everyMinute();
        $schedule->command('static_foot_immediate:run')->everyMinute();
        $schedule->command('static_basket_schedule:run')->everyFiveMinutes();
        $schedule->command('static_foot_schedule:run')->everyFiveMinutes();
        $schedule->command('static_basket_next_terminal:run')->everyFiveMinutes();
        $schedule->command('static_foot_next_terminal:run')->everyFiveMinutes();
        $schedule->command('static_foot_cur_terminal:run')->everyFiveMinutes();
//        $schedule->command('static_basket_roll_change:run')->everyMinute();
//        $schedule->command('static_foot_roll_change:run')->everyMinute();
        $schedule->command('static_basket_roll_list_change:run')->everyFiveMinutes();
        $schedule->command('static_foot_roll_list_change:run')->everyFiveMinutes();
        $schedule->command('static_basket_odd_days:run')->hourly();
        $schedule->command('static_foot_odd_days:run')->hourly();
        $schedule->command('static_basket_tech:run')->everyMinute();
        $schedule->command('static_score_change_del:run')->everyMinute();
        $schedule->command('static_basket_league:run')->twiceDaily(15, 23);
        $schedule->command('static_foot_league:run')->twiceDaily(7, 14);
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
