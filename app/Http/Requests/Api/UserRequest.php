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
                    'phone' => 'required|unique:users,phone',
                ];
                break;
            case 'PUT':
                $userId = \Auth::guard('api')->id();
                return [
                    'phone' => 'required',
                    'carno' => 'required',
                    'name' => 'required',
                    'car_number' => 'required',
                    'registration_site' => 'required',
                    'trainingground_site' => 'required',
                    'introduction' => 'required',
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
            'carno.unique' => '身份证已被占用，请重新填写',
        ];
    }
}
