<?php

namespace App\Http\Traits;
use App\Models\Stock;
use Log;
use EasyWeChat\Payment\Order;
use App\Models\Pay;
use Queue;
use App\Jobs\Message;
use Ramsey\Uuid\Uuid;
use App\Models\User;

trait PayTrait
{

    //custom_type == ORDER,REWARD
    public function unifiedorder($request,$openid,$uuid,$total_fee,$body,$trade_type = 'JSAPI',$custom_type = 'ORDER')
    {
        if($openid && $body && ($total_fee > 0))
        {
            $total_fee = (integer) ($total_fee*100);
            $payment = app('wechat')->payment;
            $attributes = [
                'trade_type'       => $trade_type,
                'body'             => $body,
                'detail'           => $body,
                'out_trade_no'     => $uuid,
                'total_fee'        => $total_fee,
                'notify_url'       => env('APP_URL').env('WECHAT_PAYMENT_NOTIFY'),
                'openid'           => $openid,
                'spbill_create_ip' => self::getUserIP($request)
            ];

            $order = new Order($attributes);
            $result = $payment->prepare($order);
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS')
            {
                Log::info('生成统一订单号..'.$result->prepay_id);
                // 生成支付
                self::insertNewPay($result->appid,$result->mch_id,$result->prepay_id,$order->openid,$order->out_trade_no,$order->total_fee,$result->trade_type,$custom_type);
                // 生成订单

                return $result->prepay_id;
            }else{
                Log::info("调用支付出错!");
                return false;
            }
        }
        else
        {
            \Log::info('支付数据不完整~');
            return false;
        }
    }

    /**
     * 生成支付记录
     */
    public function insertNewPay($appid,$mch_id,$prepay_id,$openid,$out_trade_no,$total_fee,$trade_type = 'JSAPI',$custom_type = 'ORDER')
    {
        $total_fee = bcdiv($total_fee,100,2);
        try {
            $NewPay = array(
                'appid' => $appid,
                'mch_id' => $mch_id,
                'prepay_id' => $prepay_id,
                'openid' => $openid,
                'out_trade_no' => $out_trade_no,
                'total_fee' => $total_fee,
                'trade_type' => $trade_type,
                'custom_type' => $custom_type,
                'status' => 0,
            );

            Pay::create($NewPay);

            $response = [
                'message' => 'Order created.',
                'data'    => '',
                'status'  => true
            ];

        } catch (ValidatorException $e) {

            $response = [
                'message' => '生成支付记录失败',
                'status'  => false
            ];
        }

        return $response;
    }


    /**
     * 微信回调调用方法
     */
    public function payCallback($notify, $successful)
    {
        Log::info('payBack start');
        Log::info($notify);
        Log::info($successful);
        if($successful)
        {
            try
            {
                $pay = Pay::where([
                    'appid' => $notify->appid,
                    'mch_id' => $notify->mch_id,
                    'openid' => $notify->openid,
                    'out_trade_no' => $notify->out_trade_no,
                    'status' => 0
                ])->first();

                if($pay){
                    // 更新pay
                    $pay->bank_type = $notify->bank_type;
                    $pay->cash_fee = $notify->cash_fee;
                    $pay->fee_type = $notify->fee_type;
                    $pay->is_subscribe = $notify->is_subscribe;
                    $pay->result_code = $notify->result_code;
                    $pay->sign = $notify->sign;
                    $pay->time_end = $notify->time_end;
                    $pay->transaction_id = $notify->transaction_id;
                    $pay->status = 1; //支付
                    $affect = $pay->save();
                    if($affect){
                        Log::info('pay 更新成功 , 更新订单');
                        $order =  \App\Models\Order::where(['uuid'=>$notify->out_trade_no, 'status'=>0])->first();
                        if($order){
                            $order -> status = 1; // 支付完成
                            $order -> payid = $pay->id;
                            $orderAffect = $order -> save();
                            if($orderAffect){
                                // 记录日志
                                $l = new \App\Models\Log();
                                $l->type = 1;
                                $l->uid = $order->userid;
                                $l->pkey = $order->orderid;
                                $l->pval = "支付订单";
                                $l->save();

                                // 设置新人状态
                                switch($order->platform){
                                    case "JD":
                                        if($order->total < 99){
                                            $u = User::find($order->userid);
                                            $u->is_new = 0;
                                            $u->save();
                                        }
                                        break;
                                    case "TMALL":
                                        if($order->total < 88){
                                            $u = User::find($order->userid);
                                            $u->is_new = 0;
                                            $u->save();
                                        }
                                        break;
                                    case "YHD":
                                        if($order->total < 68){
                                            $u = User::find($order->userid);
                                            $u->is_new = 0;
                                            $u->save();
                                        }
                                        break;
                                }

                                Log::info(' 订单更新success ');

                                // 更改用户库存
                                $jsonProducts = $order->product;

                                $arrProducts = json_decode($jsonProducts, true);
                                // 添加库存
                                foreach($arrProducts as $p){
                                    if($p['price'] > 0){
                                        // 查找当前库存
                                        $curStock = Stock::where('userid', $order->userid)
                                            ->where('productid', $p['id'])
                                            ->first();
                                        if(count($curStock) > 0){
                                            // 剩余量加
                                            $curStock->quantity = bcadd($curStock->quantity, $p['num'], 2);
                                            // 还剩天数
                                            $curStock->lastday = bcmul(bcadd($curStock->quantity,$curStock->last,2),$curStock->cycle,2);
                                            $curStock->is_send = 0;
                                            $curStock->save();
                                        }
                                    }
                                }

                                // 推送支付成功给用户
                                $message = array('touser' => 1);
                                $message['appid'] = env('WECHAT_ORIGINALID');
                                $message['from'] = '2';
                                $user = User::find($order->userid);
                                $message['to'] = $user->openid;
                                $message['MsgId'] = str_replace('-','',(string) Uuid::uuid4());
                                $message['message'] = "你已支付订单，物流状况的查询请在这里召唤客服吧。 \n\n <a href='".env('APP_URL')."/pay/order/".$order->id."'>详情</a>";
                                Queue::push(new Message((object)$message));

                                return true;
                            }
                        }else{
                            Log::info(' 未找到订单 ');
                        }
                    }

                }else{
                    Log::info(' 未找到pay ');
                }

                Log::info(' 更新失败 ');

                return false;

            }
            catch(ModelNotFoundException $e)
            {
                Log::info('找不到待支付订单：'.$notify->out_trade_no);
            }
        }
        return false;
    }

    public function getUserIP($request)
    {
        $request->setTrustedProxies(['127.0.0.1']);
        return $request->ip();
    }
}