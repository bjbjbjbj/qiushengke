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

Route::get('/', 'WinSpiderManager\SpriderManagerController@spiderIndex');

Route::group(['namespace' => 'WinSpiderManager'], function () {
    Route::get('/admin/spider/manager', 'SpriderManagerController@spiderIndex');
    Route::get('/admin/spider/lotteryManager', 'SpriderManagerController@spiderLotteryIndex');
});

//比赛爆料 重新爬取数据
Route::get('/analyse/tool/matchTipsRefresh', 'WinSpider\SpiderController@refreshTipByMid');

Route::get('/storage/{one?}/{two?}/{three?}/{four?}/{five?}/{six?}/{seven?}/{eight?}/{nine?}',function(){
    $realpath = str_replace('storage','',\Illuminate\Support\Facades\Request::path());
    $path = storage_path() . $realpath;
    return file_get_contents($path);
});
