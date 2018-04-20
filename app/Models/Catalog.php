<?php

namespace App\Models;   // 命名空间

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model  // 继承model
{
    //protected $table = "Catalogs";  // 指定那个表
    protected $primaryKey = "id";   // 指定主键
    // 默认维护
    public $timestamps = true;      // 指定时间戳

}
