<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Log;
use App\Http\Traits\PayTrait;
use App\Models\User;


class PayController extends CommonController
{
    use PayTrait;
    public function callback(Request $request)
    {
        Log::info('Wechat pay begin');
        if($this->check_in_wechatip($this->wechat,$request))
        {
            $response = $this->wechat->payment->handleNotify(function($notify, $successful){
                return $this->payCallback($notify, $successful);
            });
            return $response;
        }
        else
        {
            Log::info('Illegal interface request '.$request->ip());
        }
    }

}
