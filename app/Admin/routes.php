<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('users', UserController::class);

    $router->resource('music', MusicController::class);

    $router->get('activity/selectGroup', 'ActivityMemberController@selectGroup');
    $router->resource('activity', ActivityController::class);

    $router->get('activityMember/info', 'ActivityMemberController@info');
    $router->resource('activityMember', ActivityMemberController::class);

});



Route::group([
    'prefix'        => 'api',
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.api-middleware'),
], function (Router $router) {

    $router->post('/editor/upload/picture', 'Api\ToolsController@editor_upload_pic');

});