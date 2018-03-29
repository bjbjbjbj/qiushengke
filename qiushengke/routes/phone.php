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
});


Route::group(['namespace'=>'Detail'], function () {
    //足球终端
    Route::get("/match/foot/{sub1}/{sub2}/{mid}.html", "FootballController@detail"); //今天


});