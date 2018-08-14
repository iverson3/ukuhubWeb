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

// Music Api
Route::group(['namespace' => 'Api', 'prefix' => 'music'], function() {
	Route::post('getMusicList', 'MusicController@list');
});
Route::group(['namespace' => 'Api', 'prefix' => 'activity'], function() {
	Route::post('getActivityList', 'ActivityController@list');
});
