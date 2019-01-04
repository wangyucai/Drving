<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Order;
use Illuminate\Console\Command;
use EasyWeChat\Factory;
use Carbon\Carbon;

class WxPay extends Command
{
    protected $signature = 'order:status';

    protected $description = '更改支付状态';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $payment = \EasyWeChat::payment(); // 微信支付
        // 查询被拒绝的提现要求
        $orders = Order::where('pay_status',0)->get();
        foreach ($orders as $order) {
            $pay_result = $payment->order->queryByOutTradeNumber($order->no);
            if($pay_result['trade_state'] =="SUCCESS"){
                $order->paid_at = time(); // 更新支付时间为当前时间
                $order->pay_status = 1;
                // 更新用户表的会员到期时间
                $user_id = $order->user_id;
                $user = User::where('id', $user_id)->first();
                if(!$user){
                    $this->warn("用户{$user_id} 不存在");
                    continue;
                }
                $now = date("Y-m-d H:i:s");
                if (isset($user->member_time) && $user->member_time > $now) {
                    $dq_time = strtotime("+" . $order->days . " months", strtotime($user->member_time));
                } else {
                    $dq_time = strtotime("+" . $order->days . " months", strtotime($now));
                }
                $user->member_time = $dq_time;
                $user->save();
                $this->info("用户id{$user_id} 成功支付一笔订单");
            }else{
                // 十分钟后未支付的修改订单状态为已取消
                if(date("Y-m-d H:i:s", time() - 10 * 60)>$order->created_at){
                    $order->pay_status = 2;
                    $this->info("用户id{$order->user_id} 取消订单{$order->no}");
                }
            }
            $order->save(); // 保存订单
        }
    }
}
