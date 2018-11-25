<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    protected $fillable = ['type', 'name', 'identity', 'user_id', 'wechat_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

