<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $table = "Users";
    protected $primaryKey = "id";
    // 默认维护
    public $timestamps = true;

    protected $fillable = [
        'openid',
        'unionid',
        'selfcode',
        'name',
        'nickname',
        'mobile',
        'sex',
        'identity',
        'birthday',
        'industry',
        'email',
        'country',
        'city',
        'avatar',
        'age',
        'constellation',
        'remind',
        'is_new',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
