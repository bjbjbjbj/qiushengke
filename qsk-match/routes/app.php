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

//比赛相关
//比赛列表
Route::group(["namespace" => "App\\Match"], function () {
    Route::get("/matches/{date}/{sport}/{type}.json", "MatchesController@index");

    Route::get("/statistic/matches/{sport}", "MatchesController@onAllMatchStatistic");
});

//比赛终端
Route::group(["namespace" => "App\\MatchDetail"], function () {
    Route::get("/match/detail/{date}/{sport}/{mid}/{tab}.json", "DetailController@index");

    Route::get("/statistic/match/detail/{action}", "DetailController@statistic");
});