<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use App\Models\User;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('统计数据')
            ->body(new Box('Bar chart', view('admin.chart')));
    }

    public function getData(User $user)
    {
        // 获取已认证教练数量
        $checked_trainer = $user->where('if_check',2)->count();
        // 获取未认证教练数量
        $checking_trainer = $user->where('if_check',1)->count();
        // 已录入学员的数量
        $checked_student = $user->whereNotNull('f_uid')->count();
        // 未录入学员的数量
        $checking_student = $user->whereNull('f_uid')->whereIn('if_check', ['0', '3'])->count();
        return [
            'checked_trainer'=>$checked_trainer,
            'checking_trainer'=>$checking_trainer,
            'checked_student'=>$checked_student,
            'checking_student'=>$checking_student,
            'all_trainer'=>$checked_trainer+$checking_trainer,
            'all_student'=>$checked_student+$checking_student,
        ];
    }
}
