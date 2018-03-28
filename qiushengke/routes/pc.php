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
    //测试用,免得跨域
    Route::get("/test", "MatchController@test"); //今天

    //错误页面
    Route::any("/500.html", "MatchController@error");

    Route::any("/", function (){
        return redirect('/match/foot/schedule/immediate.html');
    });
    Route::any("/index.html", function (){
        return redirect('/match/foot/schedule/immediate.html');
    });

    //足球
    Route::get("/match/foot/schedule/immediate.html", "MatchController@immediate_f"); //今天
    Route::get("/match/foot/schedule/{dateStr}/result.html", "MatchController@result_f"); //完赛比分
    Route::get("/match/foot/schedule/{dateStr}/schedule.html", "MatchController@schedule_f"); //下日赛程

    //篮球 按联赛排序
    Route::get("/match/basket/schedule/immediate_{order}.html", "MatchController@immediate_bk"); //今天
    Route::get("/match/basket/schedule/{dateStr}/result_{order}.html", "MatchController@result_bk"); //完赛比分
    Route::get("/match/basket/schedule/{dateStr}/schedule_{order}.html", "MatchController@schedule_bk"); //下日赛程

    //比赛终端
    Route::get("/match/foot/{first}/{second}/{mid}.html", "MatchDetailController@matchDetail"); //足球
    Route::get("/match/basket/{first}/{second}/{mid}.html", "MatchDetailController@basketDetail"); //篮球

    //赔率终端
    Route::get("/match/foot/odd.html", "MatchDetailController@oddDetail"); //足球

    //直播终端
    Route::get("/live/foot/{first}/{second}/{mid}.html", "LiveController@liveDetail"); //足球
    Route::get("/live/basket/{first}/{second}/{mid}.html", "LiveController@liveDetail_bk"); //篮球
    Route::get('/live/player/player-{cid}.html',"LiveController@player");//播放器
});

//聊天室
Route::group(['namespace'=>'Chat'], function () {
    //获取聊天记录
    Route::get("/chat/foot/{first}/{second}/{mid}.json", "ChatController@getChat");
    //获取聊天记录
    Route::get("/chat/basket/{first}/{second}/{mid}.json", "ChatController@getChat");
    //发聊天
    Route::post("/chat/post", "ChatController@postChat");
});

Route::group(['namespace'=>'League'], function () {
    //赛事专题
    Route::get("/league/foot/{lid}.html", "LeagueController@league");//联赛
    Route::get("/cup_league/foot/{lid}.html", "LeagueController@league");//杯赛
    //篮球
    Route::get("/league/basket/{lid}.html", "LeagueController@leagueBK");//赛程

    //暂时没用到
    Route::get("/league/foot/{season}/{lid}.html", "LeagueController@leagueSeason");//联赛
    Route::get("/cup_league/foot/{season}/{lid}.html", "LeagueController@leagueSeason");//杯赛
    //篮球动态请求
    Route::get("/league/basket/schedule/{lid}.html", "LeagueController@leagueBKWithDate");//赛程by时间
});

Route::group(['namespace'=>'Anchor'], function () {
    Route::get("/anchor/index.html", "AnchorController@anchorIndex");//首页

    Route::get("/anchor/room/live/{id}", "AnchorRoomController@liveUrl");//主播房间信号
});