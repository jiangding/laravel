<?php

namespace App\Http\Traits;
use Log;

trait LogisticsTrait
{
    public function curl($postData, $url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        curl_close($ch);
        //打印获得的数据
        return(json_decode($output));
    }
}