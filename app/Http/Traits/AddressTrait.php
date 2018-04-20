<?php

namespace App\Http\Traits;
use Log;
use App\Models\Address;

trait AddressTrait
{
    /**
     * 获取用户的默认地址
     * @param $userid
     * @return mixed
     */
    public function getUserDefaultAddress($userid)
    {
        // 默认地址
        $address = Address::where(["userid" => $userid, "default" => 1])->first();

        // 如果没有获取到默认地址
        if(!$address)
        {
           // 没有获取到默认地址就随便获取一个地址
           $address =  Address::where(["userid" => $userid])->first();
           if(!$address){
               return 0;
           }
        }
        return $address->id;
    }

    /**
     * 获取用户的地址列表
     * @param $userid
     * @return mixed
     */
    public function getUserAddress($userid)
    {
        return Address::where("userid", $userid)->get();
    }
}