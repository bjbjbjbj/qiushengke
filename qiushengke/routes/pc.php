<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace'=>'Match'], function () {
    Route::any("/", function (){
        return redirect('/match/foot/immediate.html');
    });
    Route::any("/index.html", function (){
        return redirect('/match/foot/immediate.html');
    });
    Route::any("/error", "HomeController@error");

    Route::any('/static/score.json',"MatchController@test");
    Route::any('/static/roll.json',"MatchController@test2");

    //足球
    Route::get("/match/foot/immediate.html", "MatchController@immediate_f"); //今天
    Route::get("/match/foot/result_{dateStr}.html", "MatchController@result_f"); //完赛比分
    Route::get("/match/foot/schedule_{dateStr}.html", "MatchController@schedule_f"); //下日赛程

    //篮球 按联赛排序
    Route::get("/match/basket/immediate_{order}.html", "MatchController@immediate_bk"); //今天
    Route::get("/match/basket/result_{dateStr}_{order}.html", "MatchController@result_bk"); //完赛比分
    Route::get("/match/basket/schedule_{dateStr}_{order}.html", "MatchController@schedule_bk"); //下日赛程

    //比赛终端
    Route::get("/match/foot/{first}/{second}/{mid}.html", "MatchDetailController@matchDetail"); //今天
});

Route::group(['namespace'=>'League'], function () {
    //赛事专题
    Route::get("/league/foot/{season}/{lid}.html", "LeagueController@leagueSeason");//联赛
    Route::get("/league/foot/{lid}.html", "LeagueController@league");//联赛
    Route::get("/cup_league/foot/{season}/{lid}.html", "LeagueController@leagueSeason");//杯赛
    Route::get("/cup_league/foot/{lid}.html", "LeagueController@league");//杯赛
    //篮球
    Route::get("/league/basket/{lid}.html", "LeagueController@leagueBK");//赛程
    Route::get("/league/basket/schedule/{lid}.html", "LeagueController@leagueBKWithDate");//赛程by时间
});