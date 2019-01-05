<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\SendMsg;
use Illuminate\Console\Command;
use EasyWeChat\Factory;
use Carbon\Carbon;

class SendMessage extends Command
{
    protected $signature = 'send:msg';

    protected $description = '发学员约车模板消息';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $app = \EasyWeChat::miniProgram();
        // 查询预约的学员 每天晚上八点推送模板消息给教练
        $appointments = SendMsg::where('type',1)->where('if_send',0)->get();
        foreach ($appointments as $appointment) {
            $weapp_openid = User::where('id',$appointment->user_id)->value('weapp_openid');
            // 获取所有模板列表、
            $send = $app->template_message->send([
                'touser' => $weapp_openid,
                'template_id' => 'gusdAUJyBv9Q3uPXGr77GvJT7ts9r5Xf6sS_MEJYLAg',
                'page' => 'pages/accredit/index',
                'form_id' => $appointment->form_id,
                'data' => [
                    'keyword1' => $appointment->keyword1,
                    'keyword2' => $appointment->keyword2,
                    'keyword3' => $appointment->keyword3,
                    'keyword4' => $appointment->keyword4,
                ],
            ]);
            if($send['errcode']==0){
                $appointment->if_send = 1;
                $appointment->save();
                $this->info("模板消息ID={$appointment->id}发送成功");
            }else{
                $appointment->if_send = 2;
                $appointment->save();
                $msg = $send['errmsg'];
                $this->warn("模板消息ID={$appointment->id}发送失败 原因：{$msg}");
                continue;
            }
        }
    }
}
