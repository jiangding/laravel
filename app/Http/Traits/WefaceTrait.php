<?php

namespace App\Http\Traits;
use Log;


trait WefaceTrait
{
    public function str2face($str){
        $faceRule = array(
            '/::)' => 0,
            '/::~' => 1,
            '/::B' => 2,
            '/::|' => 3,
        );

        foreach($faceRule as $k=>$v){
           $str =  str_replace($str, $k, '<img src="https://res.wx.qq.com/mpres/htmledition/images/icon/common/emotion_panel/smiley/smiley_'.$v.'3518e6.png">');
        }

        return $str;
    }
}