<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'name',
        'manufacturer',
        'spec',
        'trademark',
        'goodstype',
        'catid',
        'price',
        'img',
        'sptmimg',
        'status',
        'spider',
        'url',
    ];

    public function category()
    {
        return $this->hasOne('App\Models\Catalog', 'id', 'catid');
    }
}
