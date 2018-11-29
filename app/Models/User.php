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

    protected $fillable = [
        'username','avatar','phone', 'carno', 'name', 'type', 'car_number', 'registration_site', 'trainingground_site','weapp_openid', 'weixin_session_key','all_time','single_time','day_times','f_uid','introduction','if_check','car_photo','is_get_cash', 'level', 'path','parent_id'
    ];

    protected $casts = [
        'is_get_cash' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听 User 的创建事件，用于初始化 path 和 level 字段值
        static::creating(function (User $user) {
            // 如果创建的是一个根用户
            if (is_null($user->parent_id)) {
                // 将层级设为 0
                $user->level = 0;
                // 将 path 设为 -
                $user->path  = '-';
            } else {
                // 将层级设为父级的层级 + 1
                $user->level = $user->cash_parent->level + 1;
                // 将 path 值设为父的 path 追加父 ID 以及最后跟上一个 - 分隔符
                $user->path  = $user->cash_parent->path.$user->parent_id.'-';
            }
        });
    }
    public function cash_parent()
    {
        return $this->belongsTo(User::class,'parent_id');
    }

    public function cash_children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

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

    // 定一个一个访问器，获取祖先 ID 最近的三级
    public function getPathIdsAttribute()
    {
        // trim($str, '-') 将字符串两端的 - 符号去除
        // explode() 将字符串以 - 为分隔切割为数组
        // 最后 array_filter 将数组中的空值移除
        // array_reverse 将数组倒叙
        // array_slice 取数组的最近三级
        return array_slice(array_reverse(array_filter(explode('-', trim($this->path, '-')))),0,3);
    }

    // 定义一个访问器，获取所有祖先类目并按层级排序
    public function getAncestorsAttribute()
    {
        return User::query()
            // 使用上面的访问器获取所有祖先 ID
            ->whereIn('id', $this->path_ids)
            // 按层级排序
            ->orderBy('level')
            ->get();
    }
}