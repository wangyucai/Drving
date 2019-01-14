<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CashRequest;
use App\Models\Cash;
use Illuminate\Http\Request;
use App\Transformers\CashTransformer;
use App\Models\Image;

class CashesController extends Controller
{
    // 获取提现账号
    public function index(Request $request,Cash $cash)
    {
        $user = $this->user();
        $query = $cash->where('user_id',$user->id);
        // 是否传分类
        if($type = $request->type){
            $query->where('type', $type);
        }
        $cashes = $query->get();
        return $this->response->collection($cashes, new CashTransformer());
    }
    // public function store(CashRequest $request, Cash $cash)
    // {
    //     // 判断是否存在提现账号
    //     $if_ex = $cash->where('user_id',$this->user()->id)->where('type',$request->type)->first();
    //     if($if_ex){
    //         return $this->response->errorForbidden('提现账号已存在，请勿重复提交');
    //     }
    //     $data = [
    //                 'type'=>$request->type,
    //             ];
    //     if($request->type==1){
    //         $data['name'] = $request->name;
    //         $data['identity'] = $request->identity;
    //     }elseif($request->type==2){
    //         if ($request->wx_image_id) {
    //             $image = Image::find($request->wx_image_id);
    //             $data['wechat_code'] = $image->path;
    //         }
    //     }
    //     $cash->fill($data);
    //     // 添加/更新微信二维码
    //     $cash->user_id = $this->user()->id;
    //     $cash->save();
    //     return $this->response->item($cash, new CashTransformer())
    //         ->setStatusCode(201);
    // }

    // 更新
    public function update(CashRequest $request,Cash $cash)
    {
        $user = $this->user();
        $attributes = $request->only(['type']);
        // 判断参数
        if($request->type==1){
            if(!$request->has('name')){
                return $this->response->errorForbidden('请输入支付宝提现姓名');
            }
        }
        if($request->type==2){
            if(!$request->has('wx_image_id')){
                return $this->response->errorForbidden('请上传微信提现二维码');
            }
        }
        // 添加/更新微信二维码
        if ($request->wx_image_id && $request->type==2) {
            $image = Image::find($request->wx_image_id);
            $attributes['wechat_code'] = $image->path;
        }
        if ($request->name && $request->type==1) {
            $attributes['name'] = $request->name;
            $attributes['identity'] = $request->identity;
        }
        $cashs = Cash::where('user_id',$user->id)->where('type',$request->type)->first();
        if($cashs){
            $cashs->update($attributes);
        }else{
            $cash->fill($attributes);
            $cash->user_id = $this->user()->id;
            $cash->save();
            $cashs = $cash;
        }
        return $this->response->item($cashs, new CashTransformer());
    }
}
