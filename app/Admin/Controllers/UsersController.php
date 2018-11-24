<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('教练列表')
            ->body($this->grid('trainer'));
    }
    public function member(Content $content)
    {
        return $content
            ->header('学员列表')
            ->body($this->grid('member'));
    }
    public function showtrainer($id, Content $content)
    {
        return $content
            ->header('查看教练')
            ->body($this->detail($id));
    }
    public function showmember($id, Content $content)
    {
        return $content
            ->header('查看学员')
            ->body($this->detail($id));
    }
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));
        $show->id('Id');
        $show->name('姓名');
        $show->phone('手机号');
        $show->carno('身份证号');
        return $show;
    }
    protected function grid($member)
    {
        // Laravel-Admin 1.5.19 之后的新写法，原写法也仍然可用
        $grid = new Grid(new User);
        if ($member == 'trainer') {
            $grid->model()->where('type', '=', 'trainer');
        }else{
            $grid->model()->where('type', '=', 'student');
        }
        $grid->id('ID')->sortable();
        $grid->name('姓名');
        $grid->phone('手机号');
        if($member == 'trainer'){
            $grid->if_check('是否认证')->display(function ($value) {
                if ($value == 1) {
                    return $value = '未认证';
                } elseif ($value == 2) {
                    return $value = '已认证';
                } elseif ($value == 3) {
                    return $value = '认证失败';
                }
            });
        }else{
            $grid->if_uid('是否录入')->display(function ($value) {
                    return $value ? '已录入':'未录入';
            });
        }

        $grid->carno('身份证');

        $grid->disableExport();
        // 禁用创建按钮
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        return $grid;
    }
}
