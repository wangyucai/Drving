<?php

namespace App\Transformers;

use App\Models\TrainerTime;
use League\Fractal\TransformerAbstract;

class TrainerTimeTransformer extends TransformerAbstract
{
    public function transform(TrainerTime $trainerTime)
    {
        return [
            'id' => $trainerTime->id,
            'userInfo' => $trainerTime->user,
            'school_car_time' => $trainerTime->schedule->time,
            'schedule_id' => $trainerTime->schedule_id,
            'school_car_number' => $trainerTime->school_car_number,
            'created_at' => $trainerTime->created_at->toDateTimeString(),
            'updated_at' => $trainerTime->updated_at->toDateTimeString(),
        ];
    }
}
