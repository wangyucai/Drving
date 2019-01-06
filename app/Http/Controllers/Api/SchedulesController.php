<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\TrainerTime;
use App\Transformers\ScheduleTransformer;
use App\Transformers\TrainerTimeTransformer;

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

    // 获取我的教练时刻表
    public function myTrainer(Request  $request,TrainerTime $trainerTime)
    {
        $user_id = $request->uid;
        $query = $trainerTime->query()->where('user_id', $user_id)->where('type',$request->type);
        $trainerTimes = $query->get();
        return $this->response->collection($trainerTimes, new TrainerTimeTransformer());
    }
}
