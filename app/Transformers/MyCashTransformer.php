<?php

namespace App\Transformers;

use App\Models\MyCash;
use League\Fractal\TransformerAbstract;

class MyCashTransformer extends TransformerAbstract
{
    public function transform(MyCash $mycash)
    {
        return [
            'id' => $mycash->id,
            'userInfo' => $mycash->user,
            'points' => $mycash->points,
            // 'type' => $mycash->cash->type,
            // 'name' => $mycash->cash->name,
            // 'identity' => $mycash->cash->identity,
            // 'wechat_code' => $mycash->cash->wechat_code,
            'if_check' => $mycash->if_check,
            'created_at' => $mycash->created_at->toDateTimeString(),
            'check_time' => $mycash->check_time?$mycash->check_time->toDateTimeString():'',
        ];
    }
}
