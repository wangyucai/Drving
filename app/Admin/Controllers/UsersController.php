<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Image;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

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
            ->header('会员列表')
            ->body($this->grid('member'));
    }

    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑教练')
            ->body($this->form(true)->edit($id));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建教练')
            ->body($this->form(false));
    }

    protected function grid($member)
    {
        // Laravel-Admin 1.5.19 之后的新写法，原写法也仍然可用
        $grid = new Grid(new User);
        if($member=='trainer'){
            $grid->model()->where('type', '=', 'trainer');
        }else{
            // 禁用创建按钮
            $grid->disableCreateButton();
            $grid->disableActions();
        }
        $grid->id('ID')->sortable();
        $grid->username('用户名');
        $grid->personal_name('个人名称');
        $grid->email('邮箱');
        $grid->type('会员类型')->display(function ($value) {
            return $value=='student' ? '学员' : '教练';
        });
        $grid->drive_school_name('驾校名称');
        $grid->registration_site('报名地点');
        $grid->trainingground_site('训练场地点');
        $grid->class_introduction('班别介绍');
        $grid->actions(function ($actions) {
            // 不展示 Laravel-Admin 默认的查看按钮
            $actions->disableView();
        });
        $grid->disableExport();

        return $grid;
    }

    protected function form($isEditing = false)
    {
        // Laravel-Admin 1.5.19 之后的新写法，原写法也仍然可用
        $form = new Form(new User);
        $form_image = new Form(new Image);

        $form->text('username', '用户名')->rules('required');
        if(!$isEditing){
            $form->text('password', '密码')->rules('required');
            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
        }
        $form->text('personal_name', '个人名称')->rules('required');
        $form->text('email', '邮箱')->rules('required');
        $form->text('drive_school_name', '驾校名称')->rules('required');
        $form->text('registration_site', '报名地点')->rules('required');
        $form->text('trainingground_site', '训练场地')->rules('required');
        $form->text('class_introduction', '班别介绍')->rules('required');

        return $form;
    }
}
