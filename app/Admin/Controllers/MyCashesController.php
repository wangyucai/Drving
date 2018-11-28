<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cash;
use App\Models\MyCash;
use App\Models\Schedule;
use App\Models\User;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Admin\Extensions\CheckCash;

class MyCashesController extends Controller
{
    use HasResourceActions;

    public function index(Content $content)
    {
        return $content
            ->header('提现积分会员列表')
            ->body($this->grid('trainer'));
    }

    public function check(MyCash $mycash, Request $request)
    {
        $data = $this->validate($request, [
            'id' => ['required'],
            'if_check' => ['required'],
        ], [], [
            'id' => '我的提现ID',
            'if_check' => '审核状态',
        ]);
        $mycash->where('id', $request->id)->update([
            'if_check' => $request->if_check,
            'check_time' => Carbon::now(),
        ]);
        $msg = $request->if_check == 1 ? '审核通过' : '已拒绝';
        // 返回上一页
        return response()->json(
            [
                'code' => 0,
                'msg' => $msg,
            ]
        );
    }

    protected function grid()
    {
        $grid = new Grid(new MyCash);
        $grid->model()->with(['user','cash']);
        $grid->filter(function ($filter) {
            $filter->like('user.name', '姓名');
            $filter->equal('if_check', '提现状态')->select(['0' => '未提现', '1' => '已提现', '2' => '提现失败']);
            $filter->disableIdFilter();
        });
        $grid->id('ID')->sortable();
        $grid->column('user.name', '姓名');
        $grid->column('user.phone', '手机号');
        $grid->column('user.type', '用户类型')->display(function ($value) {
            return $value=='trainer'?'教练':'学员';
        });
        $grid->column('cash.type', '账号类型')->display(function ($value) {
            return $value==1?'支付宝':'微信';
        });
        $grid->if_check('是否提现')->display(function ($value) {
            if ($value == 0) {
                return $value = "<a class='btn btn-xs btn-primary'>未审核</a>";
            } elseif ($value == 1) {
                return $value = "<a class='btn btn-xs btn-success'>已提现</a>";
            } elseif ($value == 2) {
                return $value = "<a class='btn btn-xs btn-danger'>已拒绝</a>";
            } elseif ($value == 3) {
                return $value = "<a class='btn btn-xs btn-danger'>已退还</a>";
            }
        });
        $grid->points('提现积分');
        $grid->disableExport();
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑及查看按钮
            $actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->append(new CheckCash($actions->getKey()));
            $user = User::find($actions->row->user_id);
            if($user && $user->type == 'trainer'){
                $rounte = 'admin.trainer.show';
            }else{
                $rounte = 'admin.member.show';
            }
            $actions->append('<a class="btn btn-xs btn-primary" href="' . route($rounte, [$actions->row->user_id]) . '">查看</a>');
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
