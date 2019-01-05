<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendMsg extends Model
{
    protected $fillable = ['type', 'user_id', 'form_id','keyword1','keyword2','keyword3','keyword4','if_send'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
