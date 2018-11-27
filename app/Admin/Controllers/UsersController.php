<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MyCash;
use App\Models\Cash;
use App\Models\Schedule;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Admin\Extensions\CheckUser;
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
            ->header('学员列表')
            ->body($this->grid('member'));
    }
    public function showtrainer($id)
    {
        $user = User::find($id);
        if($user->all_time){
            $all_time = explode(',', $user->all_time);
            $all_time = Schedule::whereIn('id',$all_time)->pluck('time');
            $a = [];
            foreach ($all_time as $v) {
                array_push($a, $v);
            }
            $user->all_time = implode(',',$a);
        }
        if($user->single_time){
            $single_time = Schedule::where('id',$user->single_time)->first();
            $user->single_time = $single_time->time;
        }
        $mycashes =  MyCash::where('user_id',$id)->get();
        $cashes =  Cash::where('user_id',$id)->get();
        return Admin::content(function (Content $content) use ($user,$mycashes,$cashes) {
            $content->header('查看教练');
            $content->body(view('admin.users.showtrainer', ['user' => $user, 'mycashes' => $mycashes, 'cashes' => $cashes]));
        });
    }
    public function showmember($id,MyCash $mycash)
    {
        $user = User::find($id);
        $mycashes =  $mycash->where('user_id',$id)->get();
        $cashes =  Cash::where('user_id',$id)->get();
        return Admin::content(function (Content $content) use ($user,$mycashes,$cashes) {
            $content->header('查看学员');
            // body 方法可以接受 Laravel 的视图作为参数
            $content->body(view('admin.users.showmember', ['user' => $user,'mycashes' => $mycashes, 'cashes' => $cashes]));
        });
    }

    public function checktrainer(User $user, Request $request)
    {
        $data = $this->validate($request, [
            'user_id' => ['required'],
            'if_check'      => ['required'],
        ], [], [
            'user_id' => '用户ID',
            'if_check'      => '审核状态',
        ]);
        $user->where('id',$request->user_id)->update([
            'if_check' => $request->if_check,
        ]);
        $msg = $request->if_check==2?'认证成功':'认证失败';
        // 返回上一页
        return response()->json(
            [
                'code'=>0,
                'msg'=>$msg,
            ]
        );
    }

    protected function grid($member)
    {
        // Laravel-Admin 1.5.19 之后的新写法，原写法也仍然可用
        $grid = new Grid(new User);
        $grid->filter(function($filter) use($member){
            $filter->like('name', '姓名');
            if($member == 'trainer'){
                $filter->equal('if_check','教练状态')->select(['1' => '未审核','2' => '已认证','3' => '已拒绝']);
            }
            $filter->disableIdFilter();

        });
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
                    return $value = "<a class='btn btn-xs btn-primary'>未审核</a>";
                } elseif ($value == 2) {
                    return $value = "<a class='btn btn-xs btn-success'>已认证</a>";
                } elseif ($value == 3) {
                    return $value = "<a class='btn btn-xs btn-danger'>认证失败</a>";
                }
            });
        }else{
            $grid->f_uid('是否录入')->display(function ($value) {
                    return $value>0 ? "<a class='btn btn-xs btn-success'>已录入</a>":"<a class='btn btn-xs btn-primary'>未录入</a>";
            });
        }

        $grid->carno('身份证');

        $grid->disableExport();
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) use($member) {
            // 禁用删除和编辑及查看按钮
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            if($member == 'trainer'){
                $rounte = 'admin.trainer.show';
            }else{
                $rounte = 'admin.member.show';
            }
            $actions->append(new CheckUser($actions->getKey()));

            $actions->append('<a class="btn btn-xs btn-primary" href="' . route($rounte, [$actions->getKey()]) . '">查看</a>');
        });

        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        return $grid;
    }
}
