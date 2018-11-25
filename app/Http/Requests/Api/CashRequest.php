<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class CashRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'type' => '类型',
        ];
    }
}
