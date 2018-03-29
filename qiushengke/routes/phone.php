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
});