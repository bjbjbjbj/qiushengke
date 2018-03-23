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

    Route::get('/football/one',"MatchController@staticOneMin");//静态化PC足球终端
});