<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    // 用户类型
    const TRAINER_TYPE = 'trainer';
    const STYDENT_TYPE = 'student';

    public static $userTypeMap = [
        self::TRAINER_TYPE    => '教练',
        self::STYDENT_TYPE    => '学员',
    ];
    // 练练认证状态
    const TRAINER_0 = 0;
    const TRAINER_1 = 1;
    const TRAINER_2 = 2;
    const TRAINER_3 = 3;

    public static $trainerStatusMap = [
        self::TRAINER_0    => '注册学员',
        self::TRAINER_1    => '未认证',
        self::TRAINER_2    => '已认证',
        self::TRAINER_3    => '认证失败',
    ];
    // 考试科目
    const SUBJECT_1 = 1;
    const SUBJECT_2 = 2;
    const SUBJECT_3 = 3;
    const SUBJECT_4 = 4;

    public static $subjectStatusMap = [
        self::SUBJECT_1    => '科目一',
        self::SUBJECT_2    => '科目二',
        self::SUBJECT_3    => '科目三',
        self::SUBJECT_4    => '科目四',
    ];
    // Rest omitted for brevity
    protected $fillable = [
        'username','avatar','phone', 'carno', 'name', 'type', 'car_number', 'registration_site', 'trainingground_site','weapp_openid', 'weixin_session_key','all_time','single_time','day_times','f_uid','introduction','if_check','car_photo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function parent()
    {
        return $this->belongsTo(User::class,'f_uid');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'f_uid');
    }

    public function image()
    {
        return $this->hasOne(Image::class, 'user_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}