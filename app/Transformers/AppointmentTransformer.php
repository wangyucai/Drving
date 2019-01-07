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
            'trainer_name' => $appointment->trainer->name,
            'trainer_phone' => $appointment->trainer->phone,
            'schedule' => $appointment->schedule->time,
        ];
    }
}
