<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\SendMsg;
use Illuminate\Console\Command;
use EasyWeChat\Factory;
use Carbon\Carbon;

class SendMessage2 extends Command
{
    protected $signature = 'send:msg:xf';

    protected $description = '发教练会员到期模板消息';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $app = \EasyWeChat::miniProgram();
        $message = "尊敬的教练您好！您的会员即将到期，到期后将无法享有小程序里的功能，请及时续费！";
        $appointments = SendMsg::where('type',2)->where('if_send',0)->get();
        foreach ($appointments as $appointment) {
            $user = User::where('id',$appointment->user_id)->first();
            $time = ($user->member_time->timestamp)-time();
            if($time>0){
                $sy = $this->sy($time);
            }else{
                $sy = 0;
            }
            // 获取所有模板列表、
            $send = $app->template_message->send([
                'touser' => $user->weapp_openid,
                'template_id' => 'Bgkole4mxqFGZGUJK6vF1aZvOZUd1SvphDqIcWAil_E',
                'page' => 'pages/accredit/index',
                'form_id' => $appointment->form_id,
                'data' => [
                    'keyword1' => '会员续费',
                    'keyword2' => $user->member_time->toDateTimeString(),// 到期时间
                    'keyword3' => $sy, // 剩余时长
                    'keyword4' => $message, // 提醒
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
    protected function sy($time)
    {
        $d = floor($time / (3600*24));
        $h = floor(($time % (3600*24)) / 3600);
        $m = floor((($time % (3600*24)) % 3600) / 60);
        if($d>'0'){
            $sy =  $d.'天'.$h.'小时'.$m.'分钟';
        }else{
            if($h!='0'){
                $sy = $h.'小时'.$m.'分钟';
            }else{
                $sy = $m.'分钟';
            }
        }
        return $sy;
    }
}
