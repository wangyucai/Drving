<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;
use App\Models\Image;

class UsersController extends Controller
{
    // 注册
    public function store(UserRequest $request)
    {
        $captchaData = \Cache::get($request->captcha_key);

        if (!$captchaData) {
            return $this->response->error('图片验证码已失效', 422);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'personal_name' => $request->personal_name,
            'drive_school_name' => $request->drive_school_name,
            'registration_site' => $request->registration_site,
            'trainingground_site' => $request->trainingground_site,
            'class_introduction' => $request->class_introduction,
            'password' => bcrypt($request->password),
        ]);

        // 清除图片验证码缓存
        \Cache::forget($request->captcha_key);

        return $this->response->item($user, new UserTransformer())
        ->setMeta([
            'access_token' => \Auth::guard('api')->fromUser($user),
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ])
        ->setStatusCode(201);
    }

    public function me()
    {
        return $this->response->item($this->user(), new UserTransformer());
    }

    // 小程序注册
    public function weappStore(UserRequest $request)
    {
        $captchaData = \Cache::get($request->captcha_key);

        if (!$captchaData) {
            return $this->response->error('图片验证码已失效', 422);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        // 获取微信的 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($request->code);

        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 如果 openid 对应的用户已存在，报错403
        $user = User::where('weapp_openid', $data['openid'])->first();

        if ($user) {
            return $this->response->errorForbidden('微信已绑定其他用户，请直接登录');
        }

        // 创建用户
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'type' => 'student',
            'personal_name' => $request->personal_name,
            'drive_school_name' => $request->drive_school_name,
            'registration_site' => $request->registration_site,
            'trainingground_site' => $request->trainingground_site,
            'class_introduction' => $request->class_introduction,
            'password' => bcrypt($request->password),
            'weapp_openid' => $data['openid'],
            'weixin_session_key' => $data['session_key'],
        ]);

        // 清除图片验证码缓存
        \Cache::forget($request->captcha_key);

        // meta 中返回 Token 信息
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
    }
    // 教练入驻及更新
    public function update(UserRequest $request)
    {
        $user = $this->user();
        $attributes = $request->only(['phone', 'carno', 'name', 'car_number', 'registration_site', 'trainingground_site', 'introduction','if_check','type']);
        //判断身份证是否绑定其他用户
        $carno = User::where('id','!=',$user->id)->where('carno',$request->carno)->first();
        if($carno){
            return $this->response->errorForbidden('身份证已绑定其他用户，请换身份证');
        }
        // 添加/更新头像资源
        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);
            $attributes['avatar'] = $image->path;
        }
        // 教练车辆照片上传（2-5张）

        $user->update($attributes);

        return $this->response->item($user, new UserTransformer());
    }
    // 设置时刻表
    public function schedule(Request $request)
    {
        $user = $this->user();

        $attributes = $request->only(['all_time', 'single_time', 'day_times']);

        $user->update($attributes);
        dd($user);
        return $this->response->item($user, new UserTransformer());
    }
}

