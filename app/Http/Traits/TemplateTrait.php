<?php

namespace App\Http\Traits;
use Log;
use Queue;
use App\Jobs\Template;


trait TemplateTrait
{
    /**
     * @param 物流模板消息
     */
    public function sendLogisticsTemplate($orderid,$openid)
    {
        $order = Order::where('orderid',$orderid)->first();
        switch ($order->platform)
        {
            case 'TMALL':
                $platform = '天猫';
                break;
            case 'JD':
                $platform = '京东';
                break;
            case 'YHD':
                $platform = '一号店';
                break;
            default:
                $platform = '刚好';
        }
        $message = (object)[
            'MsgType' => 'LOGISTICS',
            'data' => [
                'first' => '你的订单已经送达',
                'keyword1' => $orderid,
                'keyword2' => $order->created_at,
                'keyword3' => $platform,
                'remark'   => '如有疑问请联系客服'
            ],
            'url' => env('APP_URL').'/stock',
            'openid' => $openid
        ];
        Queue::push(new Template($message));
    }

    public function sendStockNotEnoughTemplate($title,$openid)
    {
        $message = (object)[
            'MsgType' => 'STOCKNOTENOUGH',
            'data' => [
                'first' => '家里有商品库存不足',
                'keyword1' => '刚好俠',
                'keyword2' => $title,
                'keyword3' => '小于7天',
                'remark'   => '如有疑问请联系客服'
            ],
            'url' => env('APP_URL').'/stock',
            'openid' => $openid
        ];
        Queue::push(new Template($message));
    }

    /**
     * 库存不足模板消息
     */
    public function sendStockNullTemplate($title,$openid, $id)
    {
        $message = (object)[
            'MsgType' => 'STOCKNOTENOUGH',
            'data' => [
                'first' => '家里有商品快用完了',
                'keyword1' => '刚好俠',
                'keyword2' => $title,
                'keyword3' => '快用完了',
                'remark'   => '如有疑问请联系客服'
            ],
            'url' => env('APP_URL').'/record/'. $id,
            'openid' => $openid
        ];
        Queue::push(new Template($message));
    }
}