<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Queue;
use App\Jobs\Events;
use App\Jobs\Message;


class WechatController extends CommonController
{

    public function serve(Request $request)
    {
        if($this->check_in_wechatip($this->wechat,$request))
        {
            Log::info('wechat begin');
            $this->wechat->server->setMessageHandler(function ($message) {
                Log::info($message);
                switch ($message->MsgType) {
                    case 'event':
                        Queue::push(new Events($message));
                        break;
                    case 'text':
                        if($message->Content == 'if1'){
                            return "<a href='".env('APP_URL')."/stock/a'>b1</a> \n\n <a href='".env('APP_URL')."/stock/b'>b2</a>";
                        }elseif($message->Content == 'if2'){
                            return "<a href='".env('APP_URL')."/stock/c'>p1</a> \n\n <a href='".env('APP_URL')."/stock/d'>p2</a>";
                        }
                        Queue::push(new Message($message));
                        break;
                    case 'image':
                        Queue::push(new Message($message));
                        break;
                    case 'voice':
                        Queue::push(new Message($message));
                        break;
                    case 'video':
                        Queue::push(new Message($message));
                        break;
                    case 'location':

                        break;
                    case 'link':

                        break;
                    default:

                        break;
                }
            });
            $this->wechat->server->setRequest($request);
            return $this->wechat->server->serve();
        }
        else
        {
            Log::info('Illegal interface request '.$request->ip());
            return ;
        }

    }

    public function applet(Request $request)
    {

            Log::info('applet begin');
            $this->wechat->server->setMessageHandler(function ($message) {
                Log::info($message);
                $openid = $message->FromUserName;
                // 获取线上的数据
                $wechat_user = app('wechat')->user->get($openid);
                Log::info('user info ' . $openid);
                Log::info($wechat_user);
            });
            $this->wechat->server->setRequest($request);
            return $this->wechat->server->serve();

    }
}
