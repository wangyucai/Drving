<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyCash extends Model
{
    protected $fillable = ['points', 'cash_id', 'user_id', 'if_check'];

    protected $dates = [
        'check_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }

}
