<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $primaryKey = "id";

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'uid');
    }

    public function admin()
    {
        return $this->hasOne('App\Models\Admin', 'id', 'mid');
    }
}
