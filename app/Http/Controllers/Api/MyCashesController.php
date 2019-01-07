<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\MyCashRequest;
use App\Models\Cash;
use App\Models\MyCash;
use App\Transformers\MyCashTransformer;
use Illuminate\Http\Request;

class MyCashesController extends Controller
{
    // 获取我的提现流水
    public function index(Request $request, MyCash $mycash)
    {
        $user = $this->user();

        $query = $mycash->query();
        // 是否传入审核字段
        if($request->has('if_check')){
            $query->where('if_check', $request->if_check);
        }
        $mycashes = $query->where('user_id',$user->id)->get();
        return $this->response->collection($mycashes, new MyCashTransformer());
    }
    // 我的提现申请
    public function store(MyCashRequest $request, Cash $cash, MyCash $mycash)
    {
        $user = $this->user();
        // 判断是否存在提现账号
        $if_ex = $cash->where('user_id', $this->user()->id)->where('type', $request->type)->first();
        if (!$if_ex) {
            return $this->response->errorForbidden('提现账号不存在，请勿添加后再提交提交');
        }
        // 提现的同时扣除我的积分（后台审核不通过则退回）
        $user->my_points=$user->my_points-$request->points;
        if ($user->my_points<0) {
            return $this->response->errorForbidden('提现积分超过了已有积分');
        }
        $user->save();

        $data = [
            'points' => $request->points,
            'user_id' => $user->id,
            'cash_id' => $if_ex->id,
            'if_check' => 0,
        ];
        $mycash->fill($data);
        $mycash->save();
        return $this->response->item($mycash, new MyCashTransformer())
            ->setStatusCode(201);
    }
}
