<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Commission;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        // 获取学员佣金---根据教练设置学员佣金的
        $student_coms = Commission::where('type',3)->first();
        // 教练佣金发放 --查询已认证教练的信息
        $trainers = User::where('if_check', 2)->where('type','trainer')->whereNotNull('parent_id')->where('is_get_cash',false)->get();
        foreach ($trainers as $trainer) {
            $p_trainer = $trainer->path_ids;
            $p_trainer_total = count($p_trainer);
            if($p_trainer_total==1){ // 该用户获得一级教练佣金
                User::where('id',$p_trainer[0])->increment('my_points', $trainer_coms->one_level);
            }elseif($p_trainer_total==2){ // 第一个用户获得一级教练佣金 第二个用户获得二级教练佣金
                User::where('id',$p_trainer[0])->increment('my_points', $trainer_coms->one_level);
                User::where('id',$p_trainer[1])->increment('my_points', $trainer_coms->two_level);
            }elseif($p_trainer_total==3){// 第一个用户获得一级教练佣金 第二个用户获得二级教练佣金 三得三级
                User::where('id',$p_trainer[0])->increment('my_points', $trainer_coms->one_level);
                User::where('id',$p_trainer[1])->increment('my_points', $trainer_coms->two_level);
                User::where('id',$p_trainer[2])->increment('my_points', $trainer_coms->three_level);
            }
            $trainer->is_get_cash = true;
            $trainer->save();
        }
        // 学员佣金发放 --查询已录入的学员信息
        $students = User::where('type','student')->whereNotNull('f_uid')->where('is_get_cash',false)->get();
        foreach ($students as $student) {
            $p_student = $student->path_ids;
            $p_student_total = count($p_student);
            // 判断该用户id是否是认证教练  是的话该用户可以赚取差价
            $p_s = User::find($p_student[0]);
            if($p_s->if_check==2){// 差价为
                $diff_cash = $student_coms->one_level-$p_s->one_level;
            }else{
                $diff_cash = 0;
            }
            if($p_student_total==1){ // 该用户获得一级学员佣金
                User::where('id',$p_student[0])->increment('my_points', $student_coms->one_level+$diff_cash);
            }elseif($p_student_total==2){ // 第一个用户获得一级学员佣金 第二个用户获得二级学员佣金
                $p_ss = User::find($p_student[1]);
                if($p_ss->if_check==2){// 差价为
                    $diff_cashs = $student_coms->two_level-$p_ss->two_level;
                }else{
                     $diff_cashs = 0;
                }
                User::where('id',$p_student[0])->increment('my_points', $student_coms->one_level+$diff_cash);
                User::where('id',$p_student[1])->increment('my_points', $student_coms->two_level+$diff_cashs);
            }elseif($p_student_total==3){// 第一个用户获得一级学员佣金 第二个用户获得二级学员佣金 三得三级
                $p_ss = User::find($p_student[1]);
                if($p_ss->if_check==2){// 差价为
                    $diff_cashs = $student_coms->two_level-$p_ss->two_level;
                }else{
                     $diff_cashs = 0;
                }
                $p_sss = User::find($p_student[2]);
                if($p_sss->if_check==2){// 差价为
                    $diff_cashss = $student_coms->three_level-$p_sss->three_level;
                }else{
                     $diff_cashss = 0;
                }
                User::where('id',$p_student[0])->increment('my_points', $student_coms->one_level+$diff_cash);
                User::where('id',$p_student[1])->increment('my_points', $student_coms->two_level+$diff_cashs);
                User::where('id',$p_student[2])->increment('my_points', $student_coms->three_level+$diff_cashss);
            }
            $student->is_get_cash = true;
            $student->save();
        }
    }
}
