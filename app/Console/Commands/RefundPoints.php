<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\MyCash;
use Illuminate\Console\Command;
use EasyWeChat\Factory;
use Carbon\Carbon;

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
        // 查询被拒绝的提现要求
        $myCashes = MyCash::where('if_check',2)->get();
        foreach ($myCashes as $myCash) {
            // 把积分退回给用户
            $user = User::where('id',$myCash->user_id)->first();
            $user->my_points = $user->my_points + $myCash->points;
            $user->save();
            $myCash->if_check = 3;
            $myCash->check_time = Carbon::now();
            $myCash->save();
            $this->info("用户id: {$user->id} 成功退还 {$myCash->points} 积分");
        }
    }
}
