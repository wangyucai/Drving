<?php

namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Auth;

class CheckRenew
{
    /**
     * 教练=> 是否续费
     * 学员=> 他的教练是否续费
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $now = date("Y-m-d H:i:s");
        $user = Auth::guard('api')->user();
        $member_time = $user->member_time;
        if($user->type == 'trainer'){
            // 判断 是否是有期限内的会员
            if(!$member_time ||  $member_time < $now){
                return response()->json(['code'=>-1,'msg'=>'该教练还不是会员，请续费后在体验此功能']);
            }
        }
        if($user->type == 'student'){
            $member_time = User::where('id',$user->f_uid)->value('member_time');
            // 判断 是否是有期限内的会员
            if(!$member_time ||  $member_time < $now){
                return response()->json(['code'=>-1,'msg'=>'你的该教练还不是会员，等教练续费后在体验此功能']);
            }
        }
        return $next($request);
    }
}
