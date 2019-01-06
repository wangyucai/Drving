<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerTime extends Model
{
    protected $fillable = ['type','schedule_id', 'school_car_number', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
