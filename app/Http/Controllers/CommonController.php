<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use App\Repositories\PayRepository;
//use App\Transformers\PayTransformer;
//use App\Validators\PayValidator;
//
////use App\Repositories\OrderRepository;
////use App\Transformers\OrderTransformer;
//use App\Validators\OrderValidator;

use EasyWeChat\Foundation\Application;
use Carbon\Carbon;
use Cache;
use Log;


use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class CommonController extends Controller
{

    protected $wechat;

//    protected $PayRepository;
//    protected $PayValidator;
//    protected $PayTransformer;
//
//    protected $OrderRepository;
//    protected $OrderValidator;
//    protected $OrderTransformer;

    public function __construct(
//        PayRepository $payRepository,
//        PayValidator $payValidator,
//        PayTransformer $payTransformer,
//
//        OrderRepository $orderRepository,
//        OrderValidator $orderValidator,
//        OrderTransformer $orderTransformer,
        Application $wechat
    )
    {
        $this->wechat = $wechat;
//        $this->PayRepository = $payRepository;
//        $this->PayValidator = $payValidator;
//        $this->PayTransformer = $payTransformer;
//
//        $this->OrderRepository = $orderRepository;
//        $this->OrderValidator = $orderValidator;
//        $this->OrderTransformer = $orderTransformer;
    }

    public function getUserIP($request)
    {
        $request->setTrustedProxies(['127.0.0.1']);
        return $request->ip();
    }

    public function check_in_wechatip($wechat,$request)
    {
        // 5分钟
        $expiresAt = Carbon::now()->addMinutes(5);
        if(Cache::has('WECHAT_API_IP'))
        {
            Log::info('get WECHAT_API_IP from cache');
            $wechat_ip = unserialize(Cache::get('WECHAT_API_IP'));
        }
        else
        {
            Log::info('get WECHAT_API_IP from wechat');
            $wechat_ip = $wechat->staff->parseJSON('get', ['https://api.weixin.qq.com/cgi-bin/getcallbackip']);
            Cache::put('WECHAT_API_IP', serialize($wechat_ip), $expiresAt);
        }

        foreach ($wechat_ip['ip_list'] as $_wechatip)
        {
            if ($this->ip_in_range($this->getUserIP($request),$_wechatip))
            {
                return true;
            }
        }
        return false;
    }

    public function ip_in_range( $ip, $range ) {
        if ( strpos( $range, '/' ) == false ) {
            $range .= '/32';
        }
        list( $range, $netmask ) = explode( '/', $range, 2 );
        $range_decimal = ip2long( $range );
        $ip_decimal = ip2long( $ip );
        $wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
    }

    public function uuid()
    {
        try {
            return str_replace('-','',(string) Uuid::uuid4());
        } catch (UnsatisfiedDependencyException $e) {
            return false;
        }
    }
}
