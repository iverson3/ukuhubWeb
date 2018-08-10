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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



// 微信开发相关路由设置
Route::any('/wechat', 'WeChatController@serve');

// Route::get('/wechat/music/import', 'WeChatController@multImportImgs');
// Route::get('/wechat/music/list', 'WeChatController@musicCenter')->name('wechat.music.list');
// Route::get('/wechat/music/info', 'WeChatController@musicInfo')->name('wechat.music.info');