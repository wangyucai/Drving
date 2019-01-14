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
        $orders = Order::where('pay_status',0)->whereNull('student_id')->get();
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
                // 每次续费 都要发放一次佣金积分
                $user->is_get_cash = false;
                $user->save();
                $this->info("用户id{$user_id} 成功支付一笔订单");
            }else{
                // 十分钟后未支付的修改订单状态为已取消
                if(date("Y-m-d H:i:s", time() - 10 * 60)>$order->created_at){
                    $s = $payment->order->close($order->no);
                    if($s['result_code']=='SUCCESS'){
                        $order->pay_status = 2;
                        $this->info("用户id{$order->user_id} 取消订单{$order->no}成功");
                    }else{
                        $this->warn("用户id{$order->user_id} 取消订单{$order->no}失败");
                    }
                }
            }
            $order->save(); // 保存订单
        }

        // 教练支付佣金的订单
        $orderss = Order::where('pay_status',0)->whereNotNull('student_id')->get();
        foreach ($orderss as $order) {
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
                // 录入成功
                // 录入学员的信息
                $attributes = [
                    'name' => $order->student_name,
                    'carno' => $order->student_carno,
                    'registration_site' => $order->student_registration_site,
                    'f_uid' => $order->user_id
                ];
                User::where('id', $order->student_id)->update($attributes);
                $this->info("教练id{$user_id} 成功支付一笔佣金订单 并且录入学员成功");
            }else{
                // 十分钟后未支付的修改订单状态为已取消
                if(date("Y-m-d H:i:s", time() - 10 * 60)>$order->created_at){
                    $s = $payment->order->close($order->no);
                    if($s['result_code']=='SUCCESS'){
                        $order->pay_status = 2;
                        $this->info("教练id{$order->user_id} 取消订单{$order->no}成功");
                    }else{
                        $this->warn("教练id{$order->user_id} 取消订单{$order->no}失败");
                    }
                }
            }
            $order->save(); // 保存订单
        }
    }
}
