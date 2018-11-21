<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Transformers\ScheduleTransformer;

class SchedulesController extends Controller
{
    // 获取时刻表
    public function index(Request  $request,Schedule $schedule)
    {
        $query = $schedule->query();
        // 是否传分类id
        if($type = $request->type){
            $query->where('type', $type);
        }
        $schedules = $query->get();
        return $this->response->collection($schedules, new ScheduleTransformer());
    }
}
