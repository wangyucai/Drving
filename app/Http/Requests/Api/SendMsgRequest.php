<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class SendMsgRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'form_id' => 'required|string',
            'page' => 'required|string',
            'type' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'type' => '类型',
            'form_id' => '表单ID',
            'page' => '页面地址',
        ];
    }
}
