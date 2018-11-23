<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    // Rest omitted for brevity
    protected $fillable = [
        'username','avatar','phone', 'carno', 'name', 'type', 'car_number', 'registration_site', 'trainingground_site','weapp_openid', 'weixin_session_key','all_time','single_time','day_times','f_uid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

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