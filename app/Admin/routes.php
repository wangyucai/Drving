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
    $router->get('users/trainer', 'UsersController@index')->name('admin.trainer.index');
    $router->get('users/trainer/{id}', 'UsersController@showtrainer')->name('admin.trainer.show');
    $router->post('users/trainer/check', 'UsersController@checktrainer')->name('admin.trainer.check');

    // 学员列表
    $router->get('users/member', 'UsersController@member')->name('admin.member.index');
    $router->get('users/member/{id}', 'UsersController@showmember')->name('admin.member.show');

    // 时刻表管理
    $router->get('schedules', 'SchedulesController@index');
    $router->get('schedules/create', 'SchedulesController@create');
    $router->get('schedules/{id}', 'SchedulesController@show');
    $router->get('schedules/{id}/edit', 'SchedulesController@edit');
    $router->post('schedules', 'SchedulesController@store');
    $router->put('schedules/{id}', 'SchedulesController@update');
    $router->delete('schedules/{id}', 'SchedulesController@destroy');

    // 积分提现管理
    $router->get('mycashes', 'MyCashesController@index')->name('admin.mycashes.index');
    $router->get('mycashes/{id}', 'MyCashesController@show')->name('admin.mycashes.show');
    $router->post('mycashes/check', 'MyCashesController@check')->name('admin.trainer.check');

    // 佣金设置管理
    $router->get('commissions', 'CommissionsController@index');
    $router->get('commissions/create', 'CommissionsController@create');
    $router->get('commissions/{id}', 'CommissionsController@show');
    $router->get('commissions/{id}/edit', 'CommissionsController@edit');
    $router->post('commissions', 'CommissionsController@store');
    $router->put('commissions/{id}', 'CommissionsController@update');
    $router->delete('commissions/{id}', 'CommissionsController@destroy');

});
