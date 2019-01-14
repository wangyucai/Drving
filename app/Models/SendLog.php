<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendLog extends Model
{
    protected $fillable = ['user_id','points','add_time'];

    protected $dates = [
        'add_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
