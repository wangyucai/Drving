<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'no',
        'user_id',
        'student_id',
        'total_amount',
        'paid_at',
        'pay_status',
        'days',
        'student_name',
        'student_carno',
        'student_registration_site',
    ];
    protected $dates = [
        'paid_at',
    ];
}
