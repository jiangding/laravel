<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'userid',
        'productid',
        'quantity',
        'cycle',
        'last',
        'lastday',
        'status',
    ];

    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'productid');
    }
}
