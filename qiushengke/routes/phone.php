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
    //足球
    Route::get("/match/foot/schedule/immediate.html", "MatchController@immediate_f"); //今天
    Route::get("/match/foot/schedule/{dateStr}/result.html", "MatchController@result_f"); //完赛比分
    Route::get("/match/foot/schedule/{dateStr}/schedule.html", "MatchController@schedule_f"); //下日赛程

    //篮球 按联赛排序
    Route::get("/match/basket/schedule/immediate_{order}.html", "MatchController@immediate_bk"); //今天
    Route::get("/match/basket/schedule/{dateStr}/result_{order}.html", "MatchController@result_bk"); //完赛比分
    Route::get("/match/basket/schedule/{dateStr}/schedule_{order}.html", "MatchController@schedule_bk"); //下日赛程

    //直播终端
    Route::get("/live/foot/{first}/{second}/{mid}.html", "LiveController@liveDetail"); //足球
    Route::get("/live/basket/{first}/{second}/{mid}.html", "LiveController@liveDetail_bk"); //篮球
});


Route::group(['namespace'=>'Detail'], function () {
    //足球终端
    Route::get("/match/foot/detail/{sub1}/{sub2}/{mid}.html", "FootballController@detail");
    Route::get("/match/foot/detail/odd_cell/{sub1}/{sub2}/{mid}.html", "FootballController@dataOdd");

    //篮球终端
    Route::get("/match/basket/detail/{sub1}/{sub2}/{mid}.html", "BasketballController@detail");
    Route::get("/match/basket/detail/odd_cell/{sub1}/{sub2}/{mid}.html", "BasketballController@dataOdd");

});