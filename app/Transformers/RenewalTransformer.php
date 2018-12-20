<?php

namespace App\Transformers;

use App\Models\Renewal;
use League\Fractal\TransformerAbstract;

class RenewalTransformer extends TransformerAbstract
{
    public function transform(Renewal $renewal)
    {
        return [
            'id' => $renewal->id,
            'days' => $renewal->days,
            'money' => $renewal->money,
        ];
    }
}
