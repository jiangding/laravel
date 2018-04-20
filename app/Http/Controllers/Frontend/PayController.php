<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Traits\PayTrait;
use Illuminate\Http\Request;
use Session;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;


class PayController extends CommonController
{
    use PayTrait;
    public function reward(Request $request, $uuid)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            $result = $this->unifiedorder($request,$currentUser['openid'],$this->uuid(),$request->input('price'),'打赏-刚好','JSAPI','REWARD');
            if($result)
            {
                $config = $this->wechat->payment->configForJSSDKPayment($result->prepay_id);
                return response()->json($config);
            }
            else
            {
                return false;
            }
        }
        else
        {
            $config = $this->wechat->js->config(array('chooseWXPay'),false,false,false);
            return view('Frontend.Pay.reward',[
                'title' => '打赏',
                'uuid' => $uuid,
                'debug' => 'false',
                'appId' => $config['appId'],
                'nonceStr' => $config['nonceStr'],
                'timestamp' => $config['timestamp'],
                'url' => $config['url'],
                'signature' => $config['signature'],
                'jsApiList' => json_encode($config['jsApiList']),
            ]);
        }
    }

}
