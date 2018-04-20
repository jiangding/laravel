<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = [
        'userid',
        'productid',
        'remind_at',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'productid');
    }
}
