<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    // 用常量的方式定义支持的优惠券类型
    const TYPE_2 = 2;
    const TYPE_3 = 3;

    public static $typeMap = [
        self::TYPE_2   => '科目二',
        self::TYPE_3 => '科目三',
    ];
    protected $fillable = ['type','time'];
}
