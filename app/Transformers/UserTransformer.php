<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\Image;
use App\Models\Schedule;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        if($user->all_time){
            $all_time = explode(',', $user->all_time);
            $all_time = Schedule::whereIn('id',$all_time)->pluck('time');
            $a = [];
            foreach ($all_time as $v) {
                array_push($a, $v);
            }
            $user->all_time = implode(',',$a);
        }
        if($user->single_time){
            $single_time = Schedule::where('id',$user->single_time)->first();
            $user->single_time = $single_time->time;
        }
        if($user->car_photo){
            $space_image_id = explode(',',$user->car_photo);
            $images = Image::whereIn('path',$space_image_id)->get();
            foreach ($images as $k => $v) {
                $space[]=$v->id;
            }
            $car_photo_id = implode(',',$space);
        }
        return [
            'id' => $user->id,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'phone' => $user->phone,
            'carno' => $user->carno,
            'name' => $user->name,
            'type' => $user->type,
            'f_uid' => $user->f_uid,
            'subject' => $user->subject,
            'car_number' => $user->car_number,
            'registration_site' => $user->registration_site,
            'trainingground_site' => $user->trainingground_site,
            'car_photo' => $user->car_photo,
            'car_photo_id' => $car_photo_id?$car_photo_id:'',
            'all_time' => $user->all_time,
            'single_time' => $user->single_time,
            'day_times' => $user->day_times,
            'introduction' => $user->introduction,
            'if_check' => $user->if_check,
            'car_photo' => $user->car_photo,
            'my_points' => $user->my_points,
            'pid' => $user->pid,
        ];
    }
}
