<?php

namespace App\Transformers;

use App\Models\Cash;
use League\Fractal\TransformerAbstract;

class CashTransformer extends TransformerAbstract
{
    public function transform(Cash $cash)
    {
        return [
            'id' => $cash->id,
            'type' => $cash->type,
            'name' => $cash->name,
            'identity' => $cash->identity,
            'wechat_code' => $cash->wechat_code,
        ];
    }
}
