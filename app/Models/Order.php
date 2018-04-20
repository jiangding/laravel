<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'orderid',
        'userid',
        'uuid',
        'platform',
        'platform_orderid',
        'address',
        'address_name',
        'address_phone',
        'arrival',
        'invoice_type',
        'invoice_name',
        'invoice_content',
        'product',
        'total',
        'payid',
        'logisticsid',
        'status', // 0 未支付; 1 已支付; 2 已取消; 3 退款申请; 4 退款中；5 已退款
        'type', // 0 主线 1 专线
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'userid');
    }

    public function pay()
    {
        return $this->hasOne('App\Models\Pay', 'id', 'payid');
    }
//    public function kf()
//    {
//        return $this->hasOne('App\Models\Admin', 'id', 'kfid');
//    }
}
