<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;


class ScanController extends Controller
{

    public function index(Request $request)
    {
        $config = $this->wechat->js->config(array('scanQRCode'),false,false,false, env('APP_URL').'/scan');
        return view('frontend.Scan.index',[
            'title' => '扫描条码',
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
