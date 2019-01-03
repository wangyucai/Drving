<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'no',
        'user_id',
        'total_amount',
        'paid_at',
        'pay_status',
        'days',
    ];
    protected $dates = [
        'paid_at',
    ];
}
