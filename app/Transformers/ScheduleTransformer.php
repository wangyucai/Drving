<?php

namespace App\Transformers;

use App\Models\Schedule;
use League\Fractal\TransformerAbstract;

class ScheduleTransformer extends TransformerAbstract
{
    public function transform(Schedule $schedule)
    {
        return [
            'id' => $schedule->id,
            'type' => $schedule->type,
            'time' => $schedule->time,
            'created_at' => $schedule->created_at->toDateTimeString(),
            'updated_at' => $schedule->updated_at->toDateTimeString(),
        ];
    }
}
