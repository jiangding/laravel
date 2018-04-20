<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    //protected $table = "admins";
    protected $primaryKey = "id";
    // 默认维护
    public $timestamps = false;
}
