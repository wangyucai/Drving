<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => 'serializer:array'
], function ($api) {
    // 增加调用频率限制 一分钟一次
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api){
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')
            ->name('api.captchas.store');
        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
        // 登录
        $api->post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
        // 小程序登录
        $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');
        // 小程序注册
        $api->post('weapp/users', 'UsersController@weappStore')
            ->name('api.weapp.users.store');
        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
    });

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function ($api) {
        // 游客可以访问的接口
            // 获取时刻表
            $api->get('schedule', 'SchedulesController@index')
                    ->name('api.schedule.index');
            // 所有已认证教练列表
            $api->get('alltrainers', 'UsersController@allTrainers')
                ->name('api.alltrainers.allTrainers');
            // 获取教练的详情
            $api->get('trainer', 'UsersController@trainer')
                ->name('api.user.trainer');
        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')
                ->name('api.user.show');
            // 图片资源
            $api->post('images', 'ImagesController@store')
                ->name('api.images.store');
            // 编辑登录用户信息
            $api->put('user', 'UsersController@update')
                ->name('api.user.update');
            // 教练设置自己的时刻表
            $api->put('user/schedule', 'UsersController@schedule')
                ->name('api.user.schedule');
            // 教练查看自己的时刻表
            $api->get('trainer/times', 'TrainerTimesController@index')
                ->name('api.trainer.index');
            // 教练设置自己的时刻表-->新的接口
            $api->put('trainer/times', 'TrainerTimesController@update')
                ->name('api.trainer.times');
            // 支付宝提现
            $api->post('cashes', 'WithdrawCashController@alipayToTransfer')
                ->name('api.cashes.alipayToTransfer');
            // 教练录入学员
            $api->put('user/student', 'UsersController@student')
                ->name('api.student.update');
            // 教练获取自己的学员列表
            $api->get('student', 'UsersController@studentList')
                ->name('api.student.studentList');
            // 教练移动学员列表
            $api->post('student', 'UsersController@toStudent')
                ->name('api.student.toStudent');
            // 我的教练信息
            $api->get('mytrainer', 'UsersController@myTrainer')
                ->name('api.mytrainer.myTrainer');
            // 获取提现账号
            $api->get('card_cash', 'CashesController@index')
                ->name('api.card_cash.index');
            // 绑定卡号提现
            $api->post('card_cash', 'CashesController@store')
                ->name('api.card_cash.store');
            // 修改提现账号
            $api->put('card_cash', 'CashesController@update')
                ->name('api.card_cash.update');
            // 我的提现流水
            $api->get('my_cash', 'MyCashesController@index')
                ->name('api.card_cash.index');
            // 我的提现申请
            $api->post('my_cash', 'MyCashesController@store')
                ->name('api.card_cash.store');
            // 学员约车
            $api->post('student/appointments', 'AppointmentsController@store')
                ->name('api.student.appointments');
        });
    });
});
