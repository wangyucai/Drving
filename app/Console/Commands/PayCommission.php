<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\SendLog;
use App\Models\Commission;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;

class PayCommission extends Command
{
    protected $signature = 'pay:commissions';

    protected $description = '佣金的发放';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // 获取教练佣金
        $trainer_coms = Commission::where('type',2)->first();
        // 教练佣金发放 --查询已认证教练的信息并且是会员
        $trainers = User::where('if_check', 2)->where('type','trainer')->whereNotNull('parent_id')->whereNotNull('member_time')->where('is_get_cash',false)->get();
        $now = date("Y-m-d H:i:s");
        foreach ($trainers as $trainer) {
            // 先判断会员是否有效期 过期的话更改状态 跳出循环
            if($trainer->member_time < $now){
                $trainer->is_get_cash = true;
                $trainer->save();
                continue;
            }
            $p_trainer = $trainer->path_ids;
            $p_trainer_total = count($p_trainer);
            if($p_trainer_total==1){ // 该用户获得一级教练佣金
                User::where('id',$p_trainer[0])->increment('my_points', $trainer_coms->one_level);
                // 记录获得积分情况
                SendLog::create(['user_id'=>$p_trainer[0],'points'=>$trainer_coms->one_level,'add_time'=>time()]);
                $trainer->is_get_cash = true;
                $trainer->save();
            }elseif($p_trainer_total==2){ // 第一个用户获得一级教练佣金 第二个用户获得二级教练佣金
                User::where('id',$p_trainer[0])->increment('my_points', $trainer_coms->one_level);
                User::where('id',$p_trainer[1])->increment('my_points', $trainer_coms->two_level);
                // 记录获得积分情况
                \DB::table('send_logs')->insert([
                    ['user_id' => $p_trainer[0], 'points' => $trainer_coms->one_level,'add_time'=>time()],
                    ['user_id' => $p_trainer[1], 'points' => $trainer_coms->two_level,'add_time'=>time()]
                ]);
                $trainer->is_get_cash = true;
                $trainer->save();
            }elseif($p_trainer_total==3){// 第一个用户获得一级教练佣金 第二个用户获得二级教练佣金 三得三级
                User::where('id',$p_trainer[0])->increment('my_points', $trainer_coms->one_level);
                User::where('id',$p_trainer[1])->increment('my_points', $trainer_coms->two_level);
                User::where('id',$p_trainer[2])->increment('my_points', $trainer_coms->three_level);
                // 记录获得积分情况
                \DB::table('send_logs')->insert([
                    ['user_id' => $p_trainer[0], 'points' => $trainer_coms->one_level,'add_time'=>time()],
                    ['user_id' => $p_trainer[1], 'points' => $trainer_coms->two_level,'add_time'=>time()],
                    ['user_id' => $p_trainer[2], 'points' => $trainer_coms->three_level,'add_time'=>time()],
                ]);
                $trainer->is_get_cash = true;
                $trainer->save();
            }
        }
        // 学员佣金发放 --查询已录入的学员信息
        $students = User::where('type','student')->whereNotNull('f_uid')->where('is_get_cash',false)->get();
        foreach ($students as $student) {
            $p_student = $student->path_ids;
            $p_student_total = count($p_student);
            $order = Order::where('user_id',$student->f_uid)->where('student_id',$student->id)->where('pay_status',1)->first();
            if($p_student_total==1){ // 该用户获得一级学员佣金
                // 判断录入他的教练是否支付佣金成功
                if($order){ // 发放积分
                    User::where('id',$p_student[0])->increment('my_points', $order->one_amount);
                    SendLog::create(['user_id'=>$p_student[0],'points'=>$order->one_amount,'add_time'=>time()]);
                    $student->is_get_cash = true;
                    $student->save();
                }else{
                    $this->warn("教练{$student->f_uid}没有支付录入学员{$student->id} 时的佣金");
                }
            }elseif($p_student_total==2){ // 第一个用户获得一级学员佣金 第二个用户获得二级学员佣金
                // 判断录入他的教练是否支付佣金成功
                if($order){ // 发放积分
                    User::where('id',$p_student[0])->increment('my_points', $order->one_amount);
                    User::where('id',$p_student[1])->increment('my_points', $order->two_amount);
                    // 记录获得积分情况
                    \DB::table('send_logs')->insert([
                        ['user_id' => $p_student[0], 'points' => $order->one_amount,'add_time'=>time()],
                        ['user_id' => $p_student[1], 'points' => $order->two_amount,'add_time'=>time()],
                    ]);
                    $student->is_get_cash = true;
                    $student->save();
                }else{
                    $this->warn("教练{$student->f_uid}没有支付录入学员{$student->id} 时的佣金");
                }
            }elseif($p_student_total==3){// 第一个用户获得一级学员佣金 第二个用户获得二级学员佣金 三得三级
                // 判断录入他的教练是否支付佣金成功
                if($order){ // 发放积分
                    User::where('id',$p_student[0])->increment('my_points', $order->one_amount);
                    User::where('id',$p_student[1])->increment('my_points', $order->two_amount);
                    User::where('id',$p_student[2])->increment('my_points', $order->three_amount);
                    // 记录获得积分情况
                    \DB::table('send_logs')->insert([
                        ['user_id' => $p_student[0], 'points' => $order->one_amount,'add_time'=>time()],
                        ['user_id' => $p_student[1], 'points' => $order->two_amount,'add_time'=>time()],
                        ['user_id' => $p_student[2], 'points' => $order->three_amount,'add_time'=>time()],
                    ]);
                    $student->is_get_cash = true;
                    $student->save();
                }else{
                    $this->warn("教练{$student->f_uid}没有支付录入学员{$student->id} 时的佣金");
                }
            }
        }
    }
}
