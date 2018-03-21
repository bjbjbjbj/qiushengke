<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([], function () {
    Route::get('/sign.html', 'Auth\AuthController@sign');//登陆页面
    Route::post('/sign.html', 'Auth\AuthController@sign');//登陆逻辑

    Route::get('/logout.html', 'Auth\AuthController@logout');
    Route::get('/no_role.html', function () {
        return view('admin.noatt');
    });
});

Route::group(['middleware' => 'admin_auth'], function () {
    Route::get('/links', 'LinkController@links');//外链列表
    Route::post('/links/save', 'LinkController@saveLink');//保存外链
    Route::get('/links/del', 'LinkController@deleteLink');//删除友链
});

/**
 * 主播相关
 */
Route::group(['middleware' => 'admin_auth'], function () {
    Route::get('/anchor/platforms', 'Anchor\LivePlatformController@platforms');//直播间平台列表
    Route::post('/anchor/platforms/save', 'Anchor\LivePlatformController@savePlatform');//保存直播间平台

    Route::get('/anchor', 'Anchor\AnchorController@anchors');//主播列表
    Route::post('/anchor/save', 'Anchor\AnchorController@saveAnchor');//保存主播信息
    Route::get('/anchor/change', 'Anchor\AnchorController@changeStatus');//显示/隐藏主播

    Route::get('/anchor/rooms', 'Anchor\AnchorController@rooms');//直播间列表
    Route::post('/anchor/rooms/save', 'Anchor\AnchorController@saveRoom');//保存直播间信息
    Route::get('/anchor/rooms/del', 'Anchor\AnchorController@deleteRoom');//删除直播间

    //比赛相关 开始
    Route::get('/anchor/football/leagues', 'Anchor\MatchController@leagues');//足球 可预约主播的联赛设置
    Route::get('/anchor/basketball/leagues', 'Anchor\MatchController@basketLeagues');//篮球 可预约主播的联赛设置

    Route::post('/anchor/leagues/change', 'Anchor\MatchController@changeStatus');//设置预约/取消预约

    Route::get('/anchor/football/matches', 'Anchor\MatchController@matches');//足球 比赛预约页面
    Route::get('/anchor/basketball/matches', 'Anchor\MatchController@basketMatches');//篮球 比赛预约页面

    Route::post('/anchor/matches/book', 'Anchor\MatchController@anchorBook');//主播预约比赛
    Route::post('/anchor/matches/cancel', 'Anchor\MatchController@cancelBook');//主播取消预约比赛
    //比赛相关 结束
});

/**
 * 热门录像操作
 */
Route::group(['middleware' => 'admin_auth'], function () {
    Route::get('/videos/types', 'Video\VideoController@types');//录像类型列表
    Route::post('/videos/types/save', 'Video\VideoController@saveType');//保存录像类型


    Route::get('/videos', 'Video\VideoController@videos');//录像列表
    Route::get('/videos/edit', 'Video\VideoController@videoEdit');//录像编辑
    Route::post('/videos/save', 'Video\VideoController@saveVideo');//保存录像
});

Route::group(['middleware' => 'admin_auth'], function () {
    Route::post("/upload/cover", "UploadController@uploadCover");//上传封面
});

/**
 * 用户、权限操作
 */
Route::group(['middleware' => 'admin_auth'], function () {
    Route::get('/', function () {
        return view('admin.index');
    });

    Route::get('/accounts', 'Auth\AccountController@accounts');//用户列表
    Route::post('/accounts/save', 'Auth\AccountController@saveAccount');//保存用户

    Route::any("/roles", "Auth\RoleController@index");//角色列表
    Route::any("/roles/detail", "Auth\RoleController@detail");//新建、修改 角色页面
    Route::post("/roles/save", "Auth\RoleController@saveRole");//保存角色资料
    Route::post("/roles/del", "Auth\RoleController@delRole");//删除角色资料

    Route::any("/resources", "Auth\ResourceController@resources");//权限列表
    Route::post("/resources/save", 'Auth\ResourceController@saveRes');//保存权限
    Route::post("/resources/del", 'Auth\ResourceController@delRes');//删除权限
});