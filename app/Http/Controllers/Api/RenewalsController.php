<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Renewal;
use App\Transformers\RenewalTransformer;

class RenewalsController extends Controller
{
    // 获取续费设置表
    public function index(Request  $request,Renewal $renewal)
    {
        $query = $renewal->query();
        $renewals = $query->get();
        return $this->response->collection($renewals, new RenewalTransformer());
    }
}
