<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use function EasyWeChat\Kernel\Support\generate_sign;

class WxPayController extends Controller
{
    public function wxPay(Request $request, Order $order)
    {

        if(!$request->has('days') || !$request->has('money')){
            return $this->response->errorForbidden('缺少参数days或money');
        }
        $days = $request->days; // 续费天数
        $money = $request->money; // 续费金额
        $user_id = $request->user_id; // 续费金额

        $weapp_openid = User::where('id',$user_id)->value('weapp_openid');
        if(!$weapp_openid){
            return $this->response->errorForbidden('没有获取到用户的openid,请重新登录');
        }
        $out_trade_no = date('YmdHis') . mt_rand(1000, 9999);
        $payment = \EasyWeChat::payment(); // 微信支付
        $result = $payment->order->unify([
            'body'         => '会员续费',
            'out_trade_no' => $out_trade_no,
            'trade_type'   => 'JSAPI',  // 必须为JSAPI
            'openid'       => $weapp_openid, // 这里的openid为付款人的openid
            'total_fee'    => $money*100, // 总价 单位是分
        ]);
        // 如果成功生成统一下单的订单，那么进行二次签名
        if ($result['return_code'] === 'SUCCESS') {
            // 二次签名的参数必须与下面相同
            $params = [
                'appId'     => config('wechat.mini_program.default.app_id'),
                'timeStamp' => time(),
                'nonceStr'  => $result['nonce_str'],
                'package'   => 'prepay_id=' . $result['prepay_id'],
                'signType'  => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));
            unset($params['appId']);
            // 生成的订单入库
            \DB::transaction(function () use ($out_trade_no, $user_id,$money,$order) {
                $data = [
                    'no' => $out_trade_no,
                    'user_id' => $user_id,
                    'total_amount' => $money,
                ];
                $order->fill($data);
                $order->save();
            });
            return $params;
        } else {
            return $result;
        }
    }

    // 回调信息
    public function notify()
    {
        $payment = \EasyWeChat::payment(); // 微信支付
        $response = $payment->handlePaidNotify(function($message, $fail){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::where('no',$message['out_trade_no'])->first();

            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
            $pay_result = $payment->order->queryByOutTradeNumber($message['out_trade_no']);

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS' && $pay_result['trade_state'] =="SUCCESS") {
                    $order->paid_at = time(); // 更新支付时间为当前时间
                    $order->pay_status = 1;

                // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->pay_status = 2;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单

            return true; // 返回处理完成
        });

        return $response;
    }
}