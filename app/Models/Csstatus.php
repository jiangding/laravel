<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Csstatus extends Model implements Transformable
{
    use SoftDeletes;
    use TransformableTrait;

    public $timestamps = true;
    protected $fillable = [
		'appid',
		'csopenid',
		'fd',
		'status',
	];

}
