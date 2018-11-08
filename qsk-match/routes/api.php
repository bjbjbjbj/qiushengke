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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', 'Controller@index');

Route::get('/spider/{action}', 'WinSpider\SpiderController@index');

Route::get('/spider/isport/{action}', 'ISportSpider\SpiderISportController@index');

Route::get('/spider/basket/{action}', 'WinSpider\basket\SpiderBasketController@index');

//Route::get('/liaoGouSpider/{action}', 'LiaogouMatch\SpiderController@index');

Route::get('/spider/ballbar/{action}', 'Ballbar\SpiderBallbarController@index');

Route::get('/spider/ss365/{action}', 'Sportstream365\SpiderController@index');

Route::get('/spider/ttzb/{action}', 'TTZB\SpiderTTZBController@index');

Route::get('/spider/wcj/{action}', 'WuChaJian\SpiderWCJController@index');

Route::get('/spider/kbs/{action}', 'Kanbisai\SpiderController@index');

Route::get('/spider/lz/football', 'LongZhu\LongZhuController@spiderFootball');

Route::get('/match-tip/{action}', 'Tip\MatchTipController@index');

Route::get('/spider/qq/{action}', 'QQSport\SpiderQQSportController@index');

//盘王计算
Route::get('/calculateKing/{action}', 'LiaogouAnalyse\TeamOddResultController@index');

Route::group(["namespace" => "BotAuthor"], function () {
    Route::get('/bot-author/moro/{action}', 'MoroController@index');
});

Route::group(["namespace" => "Statistic"], function () {
    //篮球赛程,按日期拿
    Route::get("/schedule/2/{lid}.json", "Schedule\\LeagueController@getLeagueScheduleBK");
});

Route::group(['namespace'=>'Statistic'], function () {
    //发聊天
    Route::post("/chat/post", "ChatController@postChat");
});