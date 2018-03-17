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