<?php
/*
 * 支付宝提现类
 */
namespace App\Handlers;

class WithdrawCashHandler
{
    public function toTransfer($orderNo, $account, $amount, $payName, $payeeRealName, $remark)
    {
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        $aop->appId = config('alipay.AppId');
        $aop->rsaPrivateKey = config('alipay.PriKey');
        $aop->alipayrsaPublicKey = config('alipay.PubKey');
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'UTF-8';
        $aop->format = 'json';
        $request = new \AlipayFundTransToaccountTransferRequest();
        $request->setBizContent("{" .
            "\"out_biz_no\":\"$orderNo\"," .
            "\"payee_type\":\"ALIPAY_LOGONID\"," .
            "\"payee_account\":\"$account\"," .
            "\"amount\":\"$amount\"," .
            "\"payer_show_name\":\"$payName\"," .
            "\"payee_real_name\":\"$payeeRealName\"," .
            "\"remark\":\"$remark\"" .
            "}");
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";

        $resultCode = $result->$responseNode->code;

        if (!empty($resultCode) && $resultCode == 10000) {
            return [
                'code' => 0,
                'msg' => '成功',
            ];
        } else {
            return [
                'code' => -1,
                'msg' => $result->$responseNode->sub_msg,
            ];
        }
    }
}

