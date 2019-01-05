<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\SendMsgRequest;
use App\Models\SendMsg;

class SendMsgsController extends Controller
{
    // 添加模板消息
    public function send(SendMsgRequest $request)
    {
        $user = $this->user();
        $data = [
            'type'=>$request->type,
            'user_id'=>$request->user_id,
            'form_id'=>$request->form_id,
            'keyword1'=>$request->keyword1,
            'keyword2'=>$request->keyword2,
            'keyword3'=>$request->keyword3,
            'keyword4'=>$request->keyword4,
        ];
        SendMsg::create($data);
        return $this->response->array([
            'code' => '0',
            'msg' => '模板消息设置成功',
        ]);
    }
}
