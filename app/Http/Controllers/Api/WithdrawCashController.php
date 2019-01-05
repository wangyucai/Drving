<?php

namespace App\Http\Controllers\Api;

use App\Handlers\WithdrawCashHandler;

class WithdrawCashController extends Controller
{
    /*
     *支付宝提现接口
     */
    public function alipayToTransfer(WithdrawCashHandler $withdrawCash)
    {
        $orderNo = '3142321423432';
        $account = 'xdfehc7219@sandbox.com';
        $amount = 12.23;
        $payName = '支付宝提现';
        $payeeRealName = '沙箱环境';
        $remark = '提现';
        $result = $withdrawCash->toTransfer($orderNo, $account, $amount, $payName, $payeeRealName, $remark);
        if($result['code']==0){
            return $this->response->noContent();
        }else{
            return $this->response->errorUnauthorized($result['msg']);
        }


    }

    // 微信提现
    public function wxToTransfer(){
        // dd(1);
        $payment = \EasyWeChat::payment(); // 微信支付
        $a = $payment->transfer->toBalance([
            'partner_trade_no' => '1233455', // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => 'o2zCf4rgJuZTqknWrLxz-sG-H0SU',
            'check_name' => 'FORCE_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            're_user_name' => '司家伟', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
            'amount' => 10, // 企业付款金额，单位为分
            'desc' => '提现', // 企业付款操作说明信息。必填
        ]);
        dd($a);
    }
}
