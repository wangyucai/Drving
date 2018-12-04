<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use App\Models\TrainerTime;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    // 预约时刻表
    public function store(Request $request, Appointment $appointment)
    {

        $s_time=date("Y-m-d",time())." 0:0:0";
        $e_time=date("Y-m-d",time())." 24:00:00";
        $user = $this->user();
        // 首先判断预约的教练当天的预约次数是否用完
        $trainer_id = $request->trainer_id;
        // 教练24小时可预约次数
        $day_times = User::where('id',$trainer_id)->value('day_times');
        // 当天此教练已被预约的次数
        $appointmented_time = $appointment->where('trainer_id',$trainer_id)->whereBetween('yy_times',[strtotime($s_time), strtotime($e_time)])->count();
        // 要预约的次数
        $appointment_infos = json_decode($request->appointment_infos,true);
        $appointment_time= count($appointment_infos);
        // 可预约次数
        $kyy_times = $day_times-$appointmented_time;
        // 预约后剩下的次数
        $sx_times = $kyy_times-$appointment_time;
        if($sx_times<0){
            return $this->response->errorForbidden('该教练当天可预约还剩'.$kyy_times.'次');
        }
        // 添加操作
        foreach ($appointment_infos as $k => $v) {
            foreach ($v as $key => $value) {
                // 判断该学员当天该时间段预约是否重复
                $my = $appointment->where('id',$user->id)->where('trainer_id',$trainer_id)->where('schedule_id',$value)->whereBetween('yy_times',[strtotime($s_time), strtotime($e_time)])->count();
                if($my>0){
                    return $this->response->errorForbidden('今天该教练该时间段'.$timess.'你已经预约过了,请勿重复预约');
                }
                // 判断该教练的改时间段是否预约次数已满
                $times = $appointment->where('trainer_id',$trainer_id)->where('schedule_id',$value)->whereBetween('yy_times',[strtotime($s_time), strtotime($e_time)])->count();
                // 该时间段教练的可预约次数
                $trainer_time = TrainerTime::where('user_id',$trainer_id)->where('schedule_id',$value)->value('school_car_number');
                $timess = Schedule::where('id',$value)->value('time');
                if($times>=$trainer_time){
                    return $this->response->errorForbidden('该教练该时间段'.$timess.'可预约次数已满');
                }
                Appointment::create([
                    'user_id' => $user->id,
                    'trainer_id' => $trainer_id,
                    'schedule_id' => $value,
                    'yy_times' => time(),
                ]);
            }
        }
        return $this->response->array([
            'code' => '0',
            'msg' => '预约成功',
        ]);
    }
}
