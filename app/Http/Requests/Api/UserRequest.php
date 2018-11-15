<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch($this->method()) {
            case 'POST':
                return [
                    'username' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,username',
                    'password' => 'required|string|min:6',
                    'email' => 'email',
                    'personal_name' => 'required',
                    'drive_school_name' => 'required',
                    'registration_site' => 'required',
                    'trainingground_site' => 'required',
                    'class_introduction' => 'required',
                    'captcha_key' => 'required|string',
                    'captcha_code' => 'required|string',
                ];
                break;
            case 'PATCH':
                $userId = \Auth::guard('api')->id();
                return [
                    'username' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,username',
                    'email' => 'email',
                    'personal_name' => 'required',
                    'drive_school_name' => 'required',
                    'registration_site' => 'required',
                    'trainingground_site' => 'required',
                    'class_introduction' => 'required',
                ];
                break;
        }
        return [

        ];
    }

    public function attributes()
    {
        return [
            'captcha_key' => '图片验证码 key',
            'captcha_code' => '图片验证码',
        ];
    }

    public function messages()
    {
        return [
            'username.unique' => '用户名已被占用，请重新填写',
            'username.regex' => '用户名只支持英文、数字、横杆和下划线。',
            'username.between' => '用户名必须介于 3 - 25 个字符之间。',
            'username.required' => '用户名不能为空。',
        ];
    }
}
