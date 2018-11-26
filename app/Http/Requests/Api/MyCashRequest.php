<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class MyCashRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string',
            'points' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'type' => '类型',
            'points' => '积分',
        ];
    }
}
