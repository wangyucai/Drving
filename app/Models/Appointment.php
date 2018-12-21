<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['schedule_id','user_id','trainer_id','yy_times','if_send'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function trainer()
    {
        return $this->belongsTo(User::class,'trainer_id');
    }
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
