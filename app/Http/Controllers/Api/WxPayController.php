<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use function EasyWeChat\Kernel\Support\generate_sign;

class WxPayController extends Controller
{
    public function wxPay(Request $request)
    {
        if(!$request->has('days') || !$request->has('money')){
            return $this->response->errorForbidden('缺少参数days或money');
        }
        $days = $request->days; // 续费天数
        $money = $request->money; // 续费金额

        $weapp_openid = User::where('id',$request->user_id)->value('weapp_openid');
        if(!$weapp_openid){
            return $this->response->errorForbidden('没有获取到用户的openid,请重新登录');
        }
        $payment = \EasyWeChat::payment(); // 微信支付
        $result = $payment->order->unify([
            'body'         => '会员续费',
            'out_trade_no' => date('YmdHis') . mt_rand(1000, 9999),
            'trade_type'   => 'JSAPI',  // 必须为JSAPI
            'openid'       => $weapp_openid, // 这里的openid为付款人的openid
            'total_fee'    => $money, // 总价
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

            // 处理业务逻辑

            return $params;
        } else {
            return $result;
        }
    }
}