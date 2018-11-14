<?php

namespace App\Http\Controllers\Api;

use Gregwar\Captcha\CaptchaBuilder;

class CaptchasController extends Controller
{
    public function store(CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-' . str_random(15);
        // 通过它的 build 方法，创建出来验证码图片
        $captcha = $captchaBuilder->build();
        // 图片验证码的过期时间
        $expiredAt = now()->addMinutes(2);
        // getPhrase 方法获取验证码文本,存入缓存
        // 参数：key,value,Cachetime
        \Cache::put($key, ['code' => $captcha->getPhrase()], $expiredAt);
        // inline 方法获取的 base64 图片验证码
        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline(),
        ];

        return $this->response->array($result)->setStatusCode(201);
    }
}
