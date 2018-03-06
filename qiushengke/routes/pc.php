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

Route::group([], function () {
    Route::any("/", function (){
        return redirect('/match/immediate.html');
    });
    Route::any("/index.html", function (){
        return redirect('/match/immediate.html');
    });
    Route::any("/error", "HomeController@error");

    //即时比赛列表
    Route::get("/match/{sport}/immediate.html", "MatchController@immediate"); //今天
    Route::get("/match/{sport}/result_{dateStr}.html", "MatchController@result"); //完赛比分
    Route::get("/match/{sport}/schedule_{dateStr}.html", "MatchController@schedule"); //下日赛程
});