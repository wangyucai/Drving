<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use App\Models\TrainerTime;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Transformers\AppointmentTransformer;

class AppointmentsController extends Controller
{
    // 查询学员预约的接口
    public function index(Request $request, Appointment $appointment)
    {
        // 教练查询
        $user = $this->user();
        if(!$request->has('q_date')){
            return $this->response->errorForbidden('缺少参数查询日期');
        }
        $s_date = strtotime($request->q_date);
        $e_date = $s_date + 86400-1;

        if($user->type == 'student'){
            $query = $appointment->where('user_id',$user->id);
        }else{
            if($user->if_check != 2){
                return $this->response->errorForbidden('您还不是认证教练,不能查看学员预约情况');
            }
            $query = $appointment->where('trainer_id',$user->id);
        }
        $yy = $query->whereBetween('yy_times', [$s_date, $e_date])->get();
        return $this->response->collection($yy, new AppointmentTransformer());
    }
    // 预约时刻表
    public function store(Request $request, Appointment $appointment)
    {
        // 前一天晚上9点后--当天晚上九点前 可预约第二天的
        $s_time=date("Y-m-d",time()-60*60*24)." 21:00:00";
        $e_time=date("Y-m-d",time())." 20:59:59";
        $user = $this->user();
        // 判断是否是学员，只有学员才可以预约
        if($user->type != "student"){
            return $this->response->errorForbidden('对不起，您不是学员，不能预约');
        }
        // 预约的科目
        $type = $request->type;
        // 预约教练的ID
        $trainer_id = $request->trainer_id;
        // 判断该科目的学员是否预约的是对应科目
        if($type != $user->subject){
            return $this->response->errorForbidden('您不属于该预约科目里的学员,请预约对应的科目');
        }
        // 该学员提交的预约申请
        $appointment_infos = json_decode($request->appointment_infos,true);
        foreach ($appointment_infos as $k => $v) {
            foreach ($v as $key => $value) {
                // 预约的时间段
                $timess = Schedule::where('id',$value)->value('time');
                // 判断该学员当天该时间段该科目预约是否重复
                $my = $appointment->where('user_id',$user->id)->where('type',$type)->where('trainer_id',$trainer_id)->where('schedule_id',$value)->whereBetween('created_at',[$s_time, $e_time])->count();
                if($my>0){
                    return $this->response->errorForbidden('今天该教练该时间段'.$timess.'你已经预约过了,请勿重复预约');
                }
                // 查询预约的这个时间段能有几个学员预约
                $student_all_times = TrainerTime::where('user_id',$trainer_id)->where('schedule_id',$value)->value('school_car_number');
                // 查询此教练该科目该时间段今天有几个学员已经预约了
                $times = $appointment->where('trainer_id',$trainer_id)->where('type',$type)->where('schedule_id',$value)->whereBetween('created_at',[$s_time, $e_time])->count();
                if($times>=$student_all_times){
                    return $this->response->errorForbidden('该教练的科目'.$type.'的时间段:'.$timess.'今天可预约次数已满,请选择其他时间段');
                }
                // 24小时限制预约次数设置里--
                // 查询该科目24小时限制预约次数
                if($type==2){
                    $limit_times = User::where('id',$trainer_id)->value('day_times');
                }else{
                    $limit_times = User::where('id',$trainer_id)->value('day_times_3');
                }
                // 查询该学员每天该科目已经预约几个时间段
                $my_times = $appointment->where('user_id',$user->id)->where('type',$type)->where('trainer_id',$trainer_id)->whereBetween('created_at',[$s_time, $e_time])->count();
                if($my_times>=$limit_times){
                    return $this->response->errorForbidden('您选择的预约时间段已超过您今天可预约的时间段次数');
                }
                $data[] = [
                    'user_id' => $user->id,
                    'trainer_id' => $trainer_id,
                    'schedule_id' => $value,
                    'type' => $type,
                    'yy_times' => time()+86400, // 加一天的时间戳
                ];
            }
        }
        if($data){
            foreach ($data as $k => $v) {
                Appointment::create($v);
            }
            return $this->response->array([
                'code' => '0',
                'msg' => '预约成功',
            ]);
        }else{
            return $this->response->array([
                'code' => -1,
                'msg' => '预约失败',
            ]);
        }

    }
}
