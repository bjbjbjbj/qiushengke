<?php
/*
|--------------------------------------------------------------------------
| STATIC Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['namespace'=>'PC\Match'], function () {
    Route::get('/football/detail/{id}',"MatchDetailController@staticMatchDetail");//静态化PC足球终端
    Route::get('/basketball/detail/{id}',"MatchDetailController@staticMatchDetailBK");//静态化PC篮球终端

    Route::get('/football/one',"MatchController@staticOneMin");//静态化PC足球、篮球列表
    Route::get('/football/five',"MatchController@staticFiveMin");//静态化PC足球、篮球列表(赛程赛果 5分钟一次)

    Route::get('/football/hour',"MatchDetailController@staticOddDetail");//静态化一小时一次,手动执行用
    Route::get('/football/error',"MatchController@staticError");//静态化一小时一次,手动执行用

    Route::get('/live/{sport}/{mid}',"LiveController@staticLiveDetail");//静态化直播终端
});