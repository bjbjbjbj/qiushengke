<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["namespace" => "Statistic"], function () {
    //接口部分
    Route::get("/schedule/{date}/{sport}/{type}.json", "Schedule\\ScheduleDataController@index");

    Route::get("/terminal/{sport}/{a}/{b}/{mid}/{name}.json", "Terminal\\MatchTerminalController@index");

    //赛事赛程部分
    Route::get("/league/{sport}/{lid}.json", "Schedule\\LeagueController@onMatchLeagueScheduleStatic");
    Route::get("/league/2/{lid}", "Schedule\\LeagueController@getLeagueScheduleBkByDate"); //篮球赛程（传时间）

    //手动控制部分
    Route::get("/manual/change/{sport}/score", "Change\\ScoreChangeController@onScoreChangeStatic");
    Route::get("/manual/change/{sport}/scoreDel", "Change\\ScoreChangeController@onUselessScoreDelete");

    Route::get("/manual/change/{sport}/rollList", "Change\\OddChangeController@rollListChangeStatic");
    Route::get("/manual/change/{sport}/roll", "Change\\OddChangeController@rollChangeStatic");
    Route::get("/manual/change/{sport}/odd", "Change\\OddChangeController@oddChangeStatistic");
    Route::get("/manual/change/{sport}/oddDays", "Change\\OddChangeController@oddDaysChangeStatistic");

    Route::get("/manual/schedule/{sport}", "Schedule\\ScheduleDataController@onMatchesStatistic");
    Route::get("/manual/terminal/{type}/{sport}/{key}", "Terminal\\MatchTerminalController@onStatic");

    Route::get("/manual/league/{sport}", "Schedule\\LeagueController@onMatchLeagueManualStatic");
});