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
}
