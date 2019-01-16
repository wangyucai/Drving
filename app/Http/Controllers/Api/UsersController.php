<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UserRequest;
use App\Models\Image;
use function EasyWeChat\Kernel\Support\generate_sign;
use App\Models\Order;

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
        $attributes = $request->only(['phone', 'name', 'car_number', 'registration_site', 'trainingground_site', 'introduction','type']);
        if($request->if_check){
            $attributes['if_check'] = $request->if_check;
        }
        //判断身份证是否绑定其他用户
        $carno = User::where('id','!=',$user->id)->where('carno',$request->carno)->first();
        if($carno){
            return $this->response->errorForbidden('身份证已绑定其他用户，请换身份证');
        }
        $attributes['carno'] = $request->carno;
        // 添加/更新头像资源
        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);
            $attributes['avatar'] = $image->path;
        }
        // 教练车辆照片上传（2-5张）
        if ($request->space_image_id) {
            $space_image_id = explode(',',$request->space_image_id);
            $images = Image::whereIn('id',$space_image_id)->get();
            foreach ($images as $k => $v) {
                $space[]=$v->path;
            }
            $car_photo = implode(',',$space);
            $attributes['car_photo'] = $car_photo;
        }
        $user->update($attributes);

        return $this->response->item($user, new UserTransformer());
    }
    // 设置时刻表
    public function schedule(Request $request)
    {
        $user = $this->user();
        if($user->if_check != 2){
            return $this->response->errorForbidden('该用户还不是认证教练,不能设置时刻表');
        }
        $attributes = $request->only(['all_time', 'single_time', 'day_times']);
        $user->update($attributes);
        return $this->response->item($user, new UserTransformer());
    }

    // 教练录入学员
    public function student(Request $request, Order $order)
    {
        $user = $this->user();
        if($user->if_check != 2){
            return $this->response->errorForbidden('该用户还不是认证教练,不能录入学员');
        }
        // 判断录入的学员是否注册或者是否已被录入
        $user_student = User::where('phone', $request->phone)->first();
        // 不能录入自己的手机号
        if($user_student && $user_student->phone ==$user->phone){
            return $this->response->errorForbidden('不能录入自己手机号');
        }
        // 判断手机号是否已被录入
        if($user_student && $user_student->carno){
            return $this->response->errorForbidden('该手机号已被录入');
        }
        if($user_student){
            //判断身份证是否绑定其他用户
            $carno = User::where('carno',$request->carno)->first();
            if($carno){
                return $this->response->errorForbidden('身份证已绑定其他用户，请换身份证');
            }
        }else{
            return $this->response->errorForbidden('该学员手机号还未注册小程序');
        }
        $attributes = $request->only(['name','carno','registration_site']);
        $attributes['f_uid'] = $user->id;
        // 录入前教练要支付佣金
        //  先判断被录入的学员的 所有父级分销id的数量
        $p_student = $user_student->path_ids;
        $p_student_total = count($p_student);
        if($user->one_level<=0 || $user->two_level<=0 || $user->three_level<=0){
            return $this->response->errorForbidden('请先设置您的教练佣金后再录入学员！');
        }
        if($p_student_total==1){
            $money = $user->one_level; // 支付的金额
            $one_amount = $user->one_level;
            $two_amount = 0.00; // 支付的金额
            $three_amount = 0.00; // 支付的金额
        }elseif($p_student_total==2){
            $money = $user->one_level+$user->two_level; // 支付的金额
            $one_amount = $user->one_level; // 支付的金额
            $two_amount = $user->two_level; // 支付的金额
            $three_amount = 0.00; // 支付的金额
        }elseif($p_student_total==3){
            $money = $user->one_level+$user->two_level+$user->three_level; // 支付的金额
            $one_amount = $user->one_level; // 支付的金额
            $two_amount = $user->two_level; // 支付的金额
            $three_amount = $user->three_level; // 支付的金额
        }else{ // 根学员 不需要支付
            // 录入成功
            $user_student->update($attributes);
            return $this->response->array([
                'code' => '0',
                'msg' => '录入成功',
            ]);
        }
        $user_id = $user->id; // 用户id
        $student_id = $user_student->id; // 被录入的学员id

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
            \DB::transaction(function () use ($out_trade_no, $user_id,$money,$student_id,$one_amount,$two_amount,$three_amount,$order,$attributes) {
                $data = [
                    'no' => $out_trade_no,
                    'user_id' => $user_id,
                    'student_id' => $student_id,
                    'total_amount' => $money,
                    'one_amount' => $one_amount,
                    'two_amount' => $two_amount,
                    'three_amount' => $three_amount,
                    'student_name' => $attributes['name'],
                    'student_carno' => $attributes['carno'],
                    'student_registration_site' => $attributes['registration_site']
                ];
                $order->fill($data);
                $order->save();
            });
            return $params;
        } else {
            return $result;
        }
    }
    // 教练获取自己的学员列表
    public function studentList(Request  $request,User $user)
    {
        // 登录教练信息
        $user_f = $this->user();
        // 登录教练录入的学员
        $query = $user->query();
        // 是否传学员科目
        if($subject = $request->subject){
            $query->where('subject', $subject);
        }
        $user = $query->where('f_uid',$user_f->id)->get();
        return $this->response->collection($user, new UserTransformer());
    }

    // 教练移动学员列表
    public function toStudent(Request $request)
    {
        $user = $this->user();
        if($user->if_check != 2){
            return $this->response->errorForbidden('该用户还不是认证教练,不能移动学员');
        }
        $students = json_decode($request->students,true);
        foreach ($students as $k => $v) {
            foreach ($v as $key => $value) {
                User::where('id',$key)->update(['subject' => $value]);
            }
        }
        return $this->response->array([
            'code' => '0',
            'msg' => '移动成功',
        ]);
    }

    // 我的教练信息
    public function myTrainer(Request $request)
    {
        $user = $this->user();
        $myTrainer = User::query()->where('id',$user->f_uid)->get();
        return $this->response->collection($myTrainer, new UserTransformer());
    }
    // 所有已认证教练列表
    public function allTrainers(Request $request)
    {
        $user = $this->user();
        $alltrainers = User::query()->where('if_check',2)->where('type','trainer')->get();
        return $this->response->collection($alltrainers, new UserTransformer());
    }

    // 我的教练信息
    public function trainer(Request $request)
    {
        $trainer = User::query()->where('id',$request->id)->get();
        return $this->response->collection($trainer, new UserTransformer());
    }

    // 教练设置自己的学员佣金
    public function commissions(Request $request)
    {
        // 判断用户是否是认证教练
        $user = $this->user();
        if($user->if_check != 2){
            return $this->response->errorForbidden('该用户还不是认证教练,不能设置佣金');
        }
        User::where('id',$user->id)->update([
            'one_level' => $request->one_level,
            'two_level' => $request->two_level,
            'three_level' => $request->three_level,
        ]);
        return $this->response->array([
            'code' => '0',
            'msg' => '佣金设置成功',
        ]);
    }

    // 小程序发送模板消息--学员预约车
    public function send(Request $request)
    {
        try {
            $user = $this->user();
            $weapp_openid = User::where('id',$request->f_uid)->value('weapp_openid');
            $app = \EasyWeChat::miniProgram();
            // 获取所有模板列表、
            $send = $app->template_message->send([
                'touser' => $weapp_openid,
                'template_id' => 'gusdAUJyBv9Q3uPXGr77GvJT7ts9r5Xf6sS_MEJYLAg',
                'page' => $request->page,
                'form_id' => $request->form_id,
                'data' => [
                    'keyword1' => $user->weapp_openid,//'司家伟',
                    'keyword2' => $user->phone,//'18351978376',
                    'keyword3' => $request->keyword3,//'2018-12-20 22:00',
                    'keyword4' => $request->keyword4,//'科目一',
                ],
            ]);
            return $this->response->array([
                'code' => 0,
                'msg' => '预约成功'
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->response->errorForbidden('获取失败');
        }
    }

}

