<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Cs2user extends Model implements Transformable
{
    use SoftDeletes;
    use TransformableTrait;

    public $timestamps = true;
    protected $fillable = [
		'appid',
		'csopenid',
		'useropenid',
	];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'openid', 'useropenid');
    }

    public function cs()
    {
        return $this->hasOne('App\Models\User', 'openid', 'csopenid');
    }
}
