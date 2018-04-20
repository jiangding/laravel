<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Traits\PayTrait;
use App\Models\Detail;
use App\Models\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;
use EasyWeChat\Foundation\Application;
use App\Models\Order;
use App\Http\Requests;
use App\Http\Traits\OrderTrait;
use Queue;
use App\Jobs\Message;
use App\Http\Traits\LogisticsTrait;


class OrderController extends Controller
{
    use PayTrait, OrderTrait;
    use LogisticsTrait;

    /**
     * 订单首页
     */
    public function index(Request $request)
    {
        $currentUser = $this->currentUser;

        // 是否找未支付
        $nopay = $request->input('nopay');
        $navStatus = "all";
        if(isset($nopay)){
            // 未支付
            $orders = Order::where('userid', $currentUser['id'])->where('status', 0)->orderBy('created_at', 'desc')->get();
            $navStatus = "unpay";
        }else{
            // 当前用户所有订单
            $orders = Order::where('userid', $currentUser['id'])->orderBy('created_at', 'desc')->get();
        }
        foreach ($orders as $order){
            $order->product = json_decode($order->product, true);
            $order->product = $this->fliterArray($order->product);
        }

        return view('frontend.Order.index', compact('orders', 'navStatus'));
    }


    /**
     * 订单
     */
    public function order(Request $request,$uuid)
    {

        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            $detailRow = Detail::where('uuid',$uuid)->first();
            $addressid = $request->input('address_id');
            $platform = $request->input('platform');
            $product = $request->input('product');

            // 生成订单, 返回总价钱和商品信息
            $totalArr = $this->newOrder($platform,$product, $addressid, $detailRow, $currentUser->is_new);
            $prepay_id = $this->unifiedorder($request,$currentUser->openid,$uuid,$totalArr['price'],$totalArr['product'],'JSAPI','ORDER');
            if($prepay_id)
            {
                $config = $this->wechat->payment->configForJSSDKPayment($prepay_id);

                Log::info('微信返回调用支付参数');
                Log::info($config);

                return $config;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $order = Order::where(['userid'=>$currentUser->id, 'id'=>$uuid])->first();

            // 未支付就去支付
            if($order->status == 0)
            {
                $config = $this->wechat->js->config(array('chooseWXPay'), false, false, false);

                $order->product = json_decode($order->product, true);
                $order->product = $this->fliterArray( $order->product);

                return view('frontend.Order.paid', [
                    'title' => '订单',
                    'debug' => 'false',
                    'appId' => $config['appId'],
                    'nonceStr' => $config['nonceStr'],
                    'timestamp' => $config['timestamp'],
                    'url' => $config['url'],
                    'signature' => $config['signature'],
                    'jsApiList' => json_encode($config['jsApiList']),
                    'order' => $order,
                    'products' => $order->product
                ]);
            }
            else
            {
                //查取物流信息
                $childrenIdList = [];//平台订单可能有子订单号
                $tempList = [];
                if($order->childrenId){
                    //如果有子订单，则按子订单查取物流
                    $childrenIdList = json_decode($order->childrenId);
                    foreach ($childrenIdList as $id ){
                        $orderIdList[] = $id;
                    }
                } else {
                    //否则直接拿物流订单号查取
                    $orderIdList[] = $order->logisticsid;
                }

                $postData['orderIdList'] = json_encode($orderIdList);
                $url = 'http://www.iganghao.com/logistics/getLogisticInfo';
                $datas = $this->curl($postData, $url);
                //如果没有查到物流信息，则显示物流信息为空
                if (count($datas) == 0){
                    $order->logisticsInfo = [];
                }else {
                    foreach ($datas as $data){
                        //如果查取的订单号等于总订单号，则直接保存
                        if ($data->orderId == $order->logisticsid){
                            $order->logisticsInfo = [$data->orderId=>json_decode($data->infos)];
                        }
                        //如果是在子订单的，则汇总到tempList
                        if (in_array($data->orderId, $childrenIdList)){
                            $tempList[$data->orderId] = json_decode($data->infos);
                        }
                    }

                    //如果子订单数组长度大于1，那么则把汇总的tempList保存
                    if (count($childrenIdList) > 1){
                        $order->logisticsInfo = $tempList;
                    }

                    //最后，将json的childrenId解为list,以便之后调用
                    $order->childrenId = json_decode($order->childrenId, true);
                }
                $order->product = json_decode($order->product, true);
                $order->product = $this->fliterArray( $order->product);

                return view('frontend.Order.done', [
                    'order' => $order,
                    'products' => $order->product,
                    'currentUser' => $currentUser
                    ]);
            }

        }
    }


    /**
     * 未支付订单再次支付
     */
    public function unpay(Request $request)
    {
        $orderid = $request->input('id');
        // 获取订单row
        $orderRow = Order::where('id',$orderid)->first();

        $uuid = $orderRow->uuid;

        $payRow = Pay::where('out_trade_no',$uuid)->first();

        $prepay_id =  $payRow->prepay_id;
        Log::info('prepay_id  = '. $prepay_id);
        $config = $this->wechat->payment->configForJSSDKPayment($prepay_id);

        return $config;
    }


    /**
     *  取消订单
     */
    public function cancelOrder(Request $request)
    {
        $oid  = $request->input('id');

        $order =  Order::find($oid);
        $order -> status = 2;
        $order -> save();

        return [
            'message' => 'Order created.',
            'data'    => '',
            'status'  => true
        ];
    }

    /**
     * 申请退款
     */
    public function applyRefund(Request $request)
    {
        // 获取数据
        $openid = $request->input('openid');
        $messages = $request->input('message');
        $orderid = $request->input('orderid');

        $order = Order::where('orderid', $orderid)->first();
        $order -> status = 3; // 退款申请
        $affect = $order -> save();
        if($affect){
            $l = new \App\Models\Log();
            $l->type = 1;
            $l->uid = $order->userid;
            $l->pkey = $orderid;
            $l->pval = "已申请退款";
            $l->save();

            Queue::push(new Message((object) [
                'MsgType' => 'text',
                'ToUserName' => env('WECHAT_ORIGINALID', 'gh_d30a13af0bc7'),
                'FromUserName'=> $openid,
                'MsgId' => $this->uuid(),
                'MediaId' => '',
                'Content' => $messages
            ]));
        }

        return 1;
    }
}
