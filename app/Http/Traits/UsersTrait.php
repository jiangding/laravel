<?php

namespace App\Http\Traits;
use Log;

use App\Models\Admin;


trait UsersTrait
{
    public function getCustomerService($appid)
    {
//        return Users::with(['roles' => function($q){
//            $q->whereIn('name', ['r_administrator','r_customer-service']);
//        }])->where('appid',$appid)->get();
        return Admin::all();
    }

    public function getCustomerServiceRandom($appid)
    {
//        return Users::with(['roles' => function($q){
//            $q->whereIn('name', ['r_administrator','r_customer-service']);
//        }])->where('appid',$appid)->inRandomOrder()->first();
        // 随机获取一个客服
        return Admin::where([])->inRandomOrder()->first();
    }
}