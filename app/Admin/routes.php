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
    $router->get('users/trainer/{id}', 'UsersController@showtrainer');
    // 学员列表
    $router->get('users/member', 'UsersController@member');
    $router->get('users/member/{id}', 'UsersController@showmember');

    // 时刻表管理
    $router->get('schedules', 'SchedulesController@index');
    $router->get('schedules/create', 'SchedulesController@create');
    $router->get('schedules/{id}', 'SchedulesController@show');
    $router->get('schedules/{id}/edit', 'SchedulesController@edit');
    $router->post('schedules', 'SchedulesController@store');
    $router->put('schedules/{id}', 'SchedulesController@update');
    $router->delete('schedules/{id}', 'SchedulesController@destroy');

});
