<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Console\Command;
use EasyWeChat\Factory;
use Carbon\Carbon;

class SendMsg extends Command
{
    protected $signature = 'send:msg';

    protected $description = '学员约车成功后发通知给教练';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // 查询预约的学员
        $appointments = Appointment::where('if_send',0)->get();
        foreach ($appointments as $appointment) {
            $app = \EasyWeChat::miniProgram();
            // 获取所有模板列表、
            $send = $app->template_message->send([
                'touser' => 'o2zCf4rgJuZTqknWrLxz-sG-H0SU',
                'template_id' => 'gusdAUJyBv9Q3uPXGr77GvJT7ts9r5Xf6sS_MEJYLAg',
                'page' => 'index',
                'form_id' => 'form-id',
                'data' => [
                    'keyword1' => '司家伟',
                    'keyword2' => '18351978376',
                    'keyword3' => '2018-12-20 22:00',
                    'keyword4' => '科目一',
                ],
            ]);

            dd($send);
            $this->info($officialAccount );
        }
    }
}
