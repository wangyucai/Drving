<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    // 教练管理
    $router->get('users/trainer', 'UsersController@index');
    $router->get('users/trainer/create', 'UsersController@create');
    $router->get('users/trainer/{id}/edit', 'UsersController@edit');
    $router->post('users/trainer', 'UsersController@store');
    $router->put('users/trainer/{id}', 'UsersController@update');
    $router->delete('users/trainer/{id}', 'UsersController@destroy');

    // 会员列表
    $router->get('users/member', 'UsersController@member');

    // 时刻表管理
    $router->get('schedules', 'SchedulesController@index');
    $router->get('schedules/create', 'SchedulesController@create');
    $router->get('schedules/{id}', 'SchedulesController@show');
    $router->get('schedules/{id}/edit', 'SchedulesController@edit');
    $router->post('schedules', 'SchedulesController@store');
    $router->put('schedules/{id}', 'SchedulesController@update');
    $router->delete('schedules/{id}', 'SchedulesController@destroy');

});
