<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MyCash;
use Illuminate\Console\Command;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Storage;

class RefundPoints extends Command
{
    protected $signature = 'refund:points';

    protected $description = '申请提现失败后退还积分给用户';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // $config = [
        //     //...
        // ];

        $miniProgram = \EasyWeChat::miniProgram();
        // 获取 access token 实例
        $response = $miniProgram->app_code->getUnlimit('scene-value', [
            'path' => '/path/to/directory',
        ]);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs(app()->storagePath('/app/public'), 'appcode.png');
            $url = Storage::url($filename);
            dd(asset($url));
            // return $this->jsonData(ResultCode::SUCCESS_CODE, '', asset($url));
        }
        // return $this->jsonMessage(ResultCode::ERROR_CODE, '错误');


        // 查询被拒绝的提现要求
        $myCashes = MyCash::where('if_check',2)->get();
        foreach ($myCashes as $myCash) {
            // 把积分退回给用户
            User::where('id',$myCash->user_id)->update();
        }

        if (!$user) {
            return $this->error('用户不存在');
        }

    }
}
