<?php
/**
 * Created by PhpStorm.
 * User: ricky007
 * Date: 2018/3/1
 * Time: 17:33
 */

namespace App\Console\Commands;

use App\Http\Controllers\Statistic\Change\OddChangeController;
use App\Http\Controllers\Statistic\Change\ScoreChangeController;
use App\Http\Controllers\Statistic\Schedule\ScheduleDataController;
use App\Http\Controllers\Statistic\Terminal\MatchTerminalController;
use App\Models\LiaoGouModels\MatchLive;

trait MatchStaticTool
{
    /**
     * 比赛列表 静态化数据
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     */
    private function onMatchScheduleStatic($schedule) {
        //比赛列表相关
        $schedule->call(function (){
            $controller = new ScheduleDataController();
            $controller->onMatchesStaticByDate(MatchLive::kSportFootball);
        })->everyMinute();
        $schedule->call(function (){
            $controller = new ScheduleDataController();
            $controller->onMatchesStaticByDate(MatchLive::kSportFootball, date('Y-m-d', strtotime('-1 day')));
            $controller->onMatchesStaticByDate(MatchLive::kSportFootball, date('Y-m-d', strtotime('+1 day')));
        })->everyFiveMinutes();

        $schedule->call(function (){
            $controller = new ScheduleDataController();
            $controller->onMatchesStaticByDate(MatchLive::kSportBasketball);
        })->everyMinute();
        $schedule->call(function (){
            $controller = new ScheduleDataController();
            $controller->onMatchesStaticByDate(MatchLive::kSportBasketball, date('Y-m-d', strtotime('-1 day')));
            $controller->onMatchesStaticByDate(MatchLive::kSportBasketball, date('Y-m-d', strtotime('+1 day')));
        })->everyFiveMinutes();
    }

    /**
     * 比赛终端 静态化数据
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     */
    private function onMatchTerminalStatic($schedule) {
        //比赛终端
        $schedule->call(function (){
            $controller = new MatchTerminalController();
            $controller->onStatic('date', MatchLive::kSportFootball, date('Ymd'));
        })->everyFiveMinutes();
        $schedule->call(function (){
            $controller = new MatchTerminalController();
            $controller->onStatic('date', MatchLive::kSportFootball, date('Ymd', strtotime('+1 day')));
        })->everyFiveMinutes();


        $schedule->call(function (){
            $controller = new MatchTerminalController();
            $controller->onStatic('date', MatchLive::kSportBasketball, date('Ymd', strtotime('+1 day')));
        })->everyFiveMinutes();
    }

    /**
     * 比赛实时变化 静态化数据
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     */
    private function onMatchChangeStatic($schedule) {
        //滚球盘更改
        $schedule->call(function (){
            $controller = new OddChangeController();
            $controller->rollChangeStatic(MatchLive::kSportFootball, false);
        })->everyMinute();
        $schedule->call(function (){
            $controller = new OddChangeController();
            $controller->rollChangeStatic(MatchLive::kSportBasketball, false);
        })->everyMinute();

        //滚球盘 列表更改
        $schedule->call(function (){
            $controller = new OddChangeController();
            $controller->rollListChangeStatic(MatchLive::kSportFootball, false);
        })->everyFiveMinutes();
        $schedule->call(function (){
            $controller = new OddChangeController();
            $controller->rollListChangeStatic(MatchLive::kSportBasketball, false);
        })->everyFiveMinutes();

        //篮球技术统计
        $schedule->call(function (){
            $controller = new ScoreChangeController();
            $controller->onBasketTechDataSpider();
        })->everyMinute();

        //比分更改score.json无效数据的删除
        $schedule->call(function (){
            $controller = new ScoreChangeController();
            $controller->onUselessScoreDelete(MatchLive::kSportFootball);
            $controller->onUselessScoreDelete(MatchLive::kSportBasketball);
        })->everyTenMinutes();

        //当日和次日的盘口数据静态化
        $schedule->call(function (){
            $controller = new OddChangeController();
            $controller->oddDaysChangeStatistic(MatchLive::kSportFootball, false);
        })->hourly();
        $schedule->call(function (){
            $controller = new OddChangeController();
            $controller->oddDaysChangeStatistic(MatchLive::kSportBasketball, false);
        })->hourly();
    }
}