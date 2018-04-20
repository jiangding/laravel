<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Queue;
use Log;
use App\Jobs\Message;
use App\Models\components\cusResponse;
class OrderController extends Controller
{

    /**
     * 订单列表
     */
    public function index(Request $request)
    {

        // 获取数据
        $orderNo = trim($request->input('orderNo'));
        $uuid = trim($request->input('uuid'));
        $states = $request->input('states');
        $type = $request->input('type');

        $orderWhere = Order::orderBy('id','desc');

        if($uuid){
            $orderWhere->where('uuid', 'LIKE', '%'.$uuid.'%');
        }
        if($orderNo){
            $orderWhere->where('orderid', $orderNo);
        }

        // 状态
        if($states != 'all' && isset($states)){
            $orderWhere->where('status', $states);
        }
        // 类型
        if($type != 'all' && isset($type)){
            $orderWhere->where('type', $type);
        }

        $data = $orderWhere->with(['user'=>function($query){
            $query->select('id','nickname');
        }])->paginate(15);

        // 分页追加参数
        $appends = [
            'orderNo' => $orderNo,
            'uuid' => $uuid,
            'states' => $states,
            'type'=>$type,
        ];
        return view('backend.order', [
            'orders'=> $data,
            'appends'=> $appends,
        ]);
    }


    /**
     * 订单详情页
     */
    public function detail(Request $request, $id)
    {
        // 获取当前点击数据
        $row =  Order::where('id',$id)->with(['pay'=>function($q){
            $q->select('id', 'transaction_id');
        }])->with(['user'=>function($query){
            $query->select('id','avatar','nickname','openid');
        }])->first();
        if($row){

            // 获取订单商品中的url
            $products = json_decode($row->product);
            foreach($products as $pk=>$p){
                if(isset($p->id)){
                    $pro = Product::find($p->id);
                    if($pro && $pro->url){
                        $p->barcode = $pro->barcode;
                        $arrUrl = json_decode($pro->url,true);
                        $p->url = $arrUrl[$row->platform];
                        if(!$p->url){
                            unset($products[$pk]);
                        }
                    }else{
                        $p->url = '';
                        $p->barcode = '';
                    }
                }else{
                    unset($products[$pk]);
                }
            }
            $row->product = json_encode($products);

            // 日志
            $logs = \App\Models\Log::orderBy('created_at','desc')->where('type',1)->where('pkey',$row->orderid)->with('user')->with('admin')->get();
            return view('backend.order-detail', [
                'order'=>$row,
                'logs'=>$logs,
            ]);
        }
    }

    /**
     *  标记为异常
     */
    public function unusual(Request $request)
    {
        // 获取参数
        $orderid = $request->input('orderid');
        $t = $request->input('t');

        $o = Order::where('orderid',$orderid)->first();
        $o->unusual = $t;

        echo $o->save();
    }
    /**
     * 审核退款
     */
    public function refund(Request $request)
    {
        // 获取参数
        $orderid = $request->input('orderid');
        $t = $request->input('t');
        $openid = $request->input('openid');
        $messages = $request->input('message');

        $o = Order::where('orderid', $orderid)->first();
        $o -> status = $t;
        $affect = $o->save();

        // 客服
        $admin = $request->session()->get('gh_admin');
        switch($t)
        {
            case "6":
                $l = new \App\Models\Log();
                $l->type = 1;
                $l->mid = $admin->id;
                $l->pkey = $orderid;
                if($messages){
                    $l->pval = "不同意退款申请";
                }else{
                    $l->pval = "已在平台下单";
                }
                $l->save();
                break;
            case "4":
                $l = new \App\Models\Log();
                $l->type = 1;
                $l->mid = $admin->id;
                $l->pkey = $orderid;
                $l->pval = "已同意退款申请";
                $l->save();
                break;
            case "5":
                $l = new \App\Models\Log();
                $l->type = 1;
                $l->mid = $admin->id;
                $l->pkey = $orderid;
                $l->pval = "已在微信退款";
                $l->save();
                break;
        }


        if($messages){
            // 发消息提醒用户
            $message = array('touser' => 1);
            $message['appid'] = env('WECHAT_ORIGINALID');
            $message['from'] = '2';
            $message['to'] = $openid;
            $message['MsgId'] = $this->uuid();
            $message['message'] = $messages;
            Queue::push(new Message((object)$message));

            if($affect){
                return 1;
            }
        }else{
            return 1;
        }
    }


    /**
     * 下单
     */
    public function toOrder(Request $request){

        $oid = $request->input('oid');
        $orderNo = $request->input('orderNo');

        $o = Order::find($oid);
        $o->logisticsid = $orderNo;
        $o -> status = 6;
        $affect = $o->save();

        // 客服
        $admin = $request->session()->get('gh_admin');

        $cusResponse = new cusResponse();
        if($affect){
            $l = new \App\Models\Log();
            $l->type = 1;
            $l->mid = $admin->id;
            $l->pkey = $o->orderid;
            $l->pval = "已在平台下单单号".$orderNo;
            $l->save();
            $cusResponse->status = 200;
            $cusResponse->message = "success";
        }else{
            $cusResponse->status = 500;
            $cusResponse->message = "failed";
        }
        return $cusResponse->toJson();
    }
}
