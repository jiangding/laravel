<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Csrecord extends Model implements Transformable
{
    use SoftDeletes;
    use TransformableTrait;

    public $timestamps = true;
    protected $fillable = [
		'appid',
		'csopenid',
		'useropenid',
		'mode', // 0: user to cs  1: cs to user  2: system to user  3: system to cs
		'Msgid',
		'type',
		'Mediaid',
		'record',
		'read',
		'push',
	];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'openid', 'useropenid');
    }

	public function kf()
	{
		return $this->hasOne('App\Models\Admin', 'id' , 'csopenid');
	}
}
