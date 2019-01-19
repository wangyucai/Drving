<?php

namespace App\Transformers;

use App\Models\Appointment;
use League\Fractal\TransformerAbstract;

class AppointmentTransformer extends TransformerAbstract
{
    public function transform(Appointment $appointment)
    {
        return [
            'id' => $appointment->id,
            'student_name' => $appointment->user->name,
            'student_phone' => $appointment->user->phone,
            'student_avatar' => $appointment->user->avatar,
            'trainer_name' => $appointment->trainer->name,
            'trainer_avatar' => $appointment->trainer->avatar,
            'trainer_phone' => $appointment->trainer->phone,
            'trainer_trainingground_site' => $appointment->trainer->trainingground_site,
            'trainer_car_number' => $appointment->trainer->car_number,
            'trainer_sub' => $appointment->type,
            'schedule' => $appointment->schedule->time,
        ];
    }
}
