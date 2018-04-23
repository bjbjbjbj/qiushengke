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

});


Route::group(['namespace'=>'Detail'], function () {
    Route::get("/match/foot/detail", "FootballController@detail");

    Route::get("/match/basket/detail", "BasketballController@detail");
});

Route::group(['namespace'=>'League'], function () {

});

Route::group(['namespace'=>'Anchor'], function () {

});