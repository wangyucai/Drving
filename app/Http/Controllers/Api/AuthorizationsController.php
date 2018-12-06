<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use Illuminate\Support\Facades\Storage;

class AuthorizationsController extends Controller
{
    // 会员登录
    public function store(AuthorizationRequest $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['username'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        return $this->respondWithToken($token)->setStatusCode(201);
    }
    // 小程序登录
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;
        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);
        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }
        // 找到 openid 对应的用户
        $user = User::where('weapp_openid', $data['openid'])->first();

        $attributes['weixin_session_key'] = $data['session_key'];
        $attributes['openid'] = $data['openid'];

        // 未找到对应用户则需要进行用户手机号与微信信息绑定
        if (!$user) {
            // 如果未提交手机号，403 错误提示
            if (!$request->phone) {
                return $this->response->errorForbidden('请输入手机号后登陆');
            }else{
                $f_phone = User::where('phone', $request->phone)->first();
                if($f_phone){
                    return $this->response->errorForbidden('您输入的手机号已被注册');
                }
            }
            $userInfo = [
                'username' => $request->username,
                'avatar' => $request->avatar,
                'phone' => $request->phone,
                'weapp_openid' => $data['openid'],
                'weixin_session_key' => $data['session_key'],
            ];
            if ($request->parent_id) {
                $userInfo['parent_id']=$request->parent_id;
            }
            // 创建用户
            $user = User::create($userInfo);
        }else{
            // 更新用户数据
            $user->update($attributes);
        }
        // 为对应用户创建 JWT
        $token = Auth::guard('api')->fromUser($user);
        return $this->response->item($user, new UserTransformer())
            ->setMeta([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
            ])
            ->setStatusCode(201);
        // return $this->respondWithToken($token)->setStatusCode(201);
    }
    // 刷新token
    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
    // 删除token
    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    // 小程序码
    protected function weappCode(Request $request)
    {
        $miniProgram = \EasyWeChat::miniProgram();
        $response = $miniProgram->app_code->getUnlimit($request->uid, [
            'path' => $request->path,
        ]);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->saveAs(public_path() . '/uploads/code/', 'code_'.$request->uid.'.png');
            $url = config('app.url'). "/uploads/code/code_".$request->uid.'.png';
            // $url = Storage::url($filename);
            return $this->response->array([
                'code' => 0,
                'url' => $url
            ])->setStatusCode(201);
        }
        return $this->response->errorForbidden('获取失败');
    }
}
