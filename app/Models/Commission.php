<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    // 用常量的方式定义佣金类型
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;

    public static $typeMap = [
        self::TYPE_1   => '后台佣金',
        self::TYPE_2 => '教练佣金',
        self::TYPE_3 => '学员佣金',
    ];

    protected $fillable = ['type','one_level','two_level','three_level'];
}
