<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Product;
use Config;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Catalog;
use App\Http\Traits\ProductsTrait;
use Queue;
use Log;
use App\Jobs\Message;
use App\Jobs\Template;

class StockController extends Controller
{
    use ProductsTrait;
    public function index(Request $request)
    {
        // 获取当前用户
        $currentUser = $this->currentUser;
        $title = '存货清单';

        // 搜索
        $keyword = $request->input('keyword');

        if($keyword){
            // 匹配产品名模糊搜索
            $stockData = Stock::orderBy('lastday','asc')->where('userid', $currentUser['id'])
                ->with(['product'=>function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                }])->get();

        }else{
            $stockData = Stock::orderBy('lastday','asc')->where('userid', $currentUser['id'])->with('product')->get();
        }
        $stocks = array();
        $stocks['7day'] = array();
        $stocks['14day'] = array();
        $stocks['30day'] = array();
        $stocks['60day'] = array();
        $stocks['long'] = array();
        // 计算当前剩余天数所在范围
        foreach ( $stockData as $item => $value)
        {
            if(!$value->product) continue;

            // 获取分类
            $value->catalog = Catalog::where('id', $value->product->catid)->first();
            //
            if($value->lastday != 0){
                $value->a = intval(bcdiv($value->lastday,$value->cycle,2));
            }else{
                $value->a = 0;
            }

            if($value->cycle){
                $value->b = bcmul($value->cycle,$value->a,1);
            }else{
                $value->b = 0;
            }


            if($value->lastday <= 7)
            {
                array_push($stocks['7day'],$value);
            }
            elseif ($value->lastday > 7 && $value->lastday <= 14)
            {
                array_push($stocks['14day'],$value);
            }
            elseif ($value->lastday > 14 && $value->lastday <= 30)
            {
                array_push($stocks['30day'],$value);
            }
            elseif ($value->lastday > 30 && $value->lastday <= 60)
            {
                array_push($stocks['60day'],$value);
            } elseif ($value->lastday > 60)
            {
                array_push($stocks['long'],$value);
            }
        }
        $stock = $stocks;
        $config = $this->wechat->js->config(array('scanQRCode'), false, false, false);
        return view('frontend.Stock.index', [
            'title' => $title,
            'currentUser' => $currentUser,
            'stock' => $stock,
            'appId' => $config['appId'],
            'nonceStr' => $config['nonceStr'],
            'timestamp' => $config['timestamp'],
            'signature' => $config['signature'],
            'jsApiList' => json_encode($config['jsApiList']),
        ]);
    }


    /**
     * 扫码结束处理页
     * @param Request $request
     * @param $barcode
     * @param $number
     * @return v
     */
    public function scan(Request $request,$number, $barcode)
    {
        $currentUser = $this->currentUser;
        $config = $this->wechat->js->config(array('scanQRCode'), false, false, false);
        // 查询该条码是否在产品库中存在
        $product = Product::where('barcode', $barcode)->first();
        //$product = $this->getProductbyBarcode($barcode);
        if($product)
        {//扫码配置参数

            if($product['name'])
            {
                // 查看当前用户是否已经添加了
                $stock = Stock::where([
                    'productid' => $product->id,
                    'userid' => $currentUser['id']
                ])->first();


                if(count($stock) == 0)
                {
                    // 扫码

                    return view('frontend.Stock.create', [
                        'title' => '添加存货',
                        'product' => $product,
                        'appId' => $config['appId'],
                        'nonceStr' => $config['nonceStr'],
                        'timestamp' => $config['timestamp'],
                        'signature' => $config['signature'],
                    ]);
                }
                else
                {
                    return $this->update($request,$product->id);
                }
            }else{

                $this->addUserToProduct($product, $currentUser);
                return view('frontend.Scan.error', [
                    'title' => '交给我们吧',
                    'barcode' => $barcode,
                    'number' => $number,
                    'appId' => $config['appId'],
                    'nonceStr' => $config['nonceStr'],
                    'timestamp' => $config['timestamp'],
                    'signature' => $config['signature'],
                    'jsApiList' => json_encode($config['jsApiList'])
                ]);
            }
        }
        else
        {
            $this->insertProduct($barcode, $currentUser);
            return view('frontend.Scan.error', [
                'title' => '交给我们吧',
                'barcode' => $barcode,
                'number' => $number,
                'appId' => $config['appId'],
                'nonceStr' => $config['nonceStr'],
                'timestamp' => $config['timestamp'],
                'signature' => $config['signature'],
                'jsApiList' => json_encode($config['jsApiList'])
            ]);
        }
    }


    /**
     * 创建个stock记录
     */
    public function create(Request $request,$barcode)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {

                // 先查找有没有当前记录, 如果没有就创建
                $stock = Stock::where(array(
                    'userid' => $currentUser['id'],
                    'productid' => $this->getProductbyBarcode($barcode)->id,
                    'status' => 1
                ))->first();

                if(empty($stock)){
                    $data = array(
                        'userid' => $currentUser['id'],
                        'productid' => $this->getProductbyBarcode($barcode)->id,
                        'quantity' => $request->input('quantity'),
                        'cycle' => $request->input('cycle'),
                        'last' => $request->input('last'),
                        'lastday' => bcmul(bcadd($request->input('quantity'),$request->input('last'),2),$request->input('cycle'),2),
                        'status' => 1
                    );
                    Stock::create($data);
                }

                $response = [
                    'message' => 'Stock create.',
                    'data'    => '',
                    'retcode' => 0
                ];
            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            return response()->json($response);
        }
        else
        {
            $title = '商品详情';
            return view('frontend.Stock.update', compact('currentUser','title','stock','scenes'));
        }

    }

    /**
     * 更新商品
     * @param Request $request
     * @param $pid
     * @return x
     */
    public function update(Request $request,$pid)
    {
        $currentUser = $this->currentUser;

        // 获取当前数据
        $stock = Stock::where(['userid' => $currentUser['id'], 'productid' => $pid ])->with('product')->first();

        if ($request->ajax())
        {
            try {
                $data = $request->all();
                // 计算剩余天数
                $data['lastday'] = bcmul(bcadd($request->input('quantity'),$request->input('last'),2),$request->input('cycle'),2);
                $data['is_send'] = 0;
                $stock = Stock::where('id',$stock->id) ->update($data);
                $response = [
                    'message' => 'Stock updated.',
                    'data'    => '',
                    'retcode' => 0
                ];
            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            return response()->json($response);
        }
        else
        {
            $title = '商品详情';
            $stock->catalog = Catalog::where('id', $stock->product->catid)->first();
            // 处理数据
            if((integer) $stock->cycle <= 31)
            {
                $stock->month = 0;
                $stock->day = (integer) $stock->cycle;
            }
            else
            {
                $stock->month = intval(bcdiv($stock->cycle,31));
                $stock->day = intval(bcsub($stock->cycle,($stock->month * 31)));
            }
            return view('frontend.Stock.update', compact('currentUser','title','stock'));
        }

    }

    public function new(Request $request,$pid)
    {
        $currentUser = $this->currentUser;

        $stock = Stock::where([
                'productid' => $pid,
                'userid' => $currentUser['id']
        ])->first();
        if ($request->ajax())
        {
            try {
                $data = array();
                if($stock->quantity != 0)
                {
                    $data['quantity'] = $stock->quantity - 1;
                    $stock->last = 1.0;
                }
                else
                {
                	$data['quantity'] = 0;
                    $stock->last = 0.0;
                }
                $stock->quantity = $data['quantity'];
                $stock->lastday = bcmul(bcadd($data['quantity'],$stock->last,2),$stock->cycle,2);
                $stock->save();
                $response = [
                    'message' => 'Stock updated.',
                    'data'    => '',
                    'retcode' => 0
                ];
            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            return response()->json($response);
        }
        else
        {
            return $this->index();
        }
    }


    /**
     * 删除
     * @param Request $request
     * @param $pid
     * @return View
     */
    public function delete(Request $request,$sid)
    {

        if ($request->ajax())
        {
            try {
                Stock::where('id', $sid)->delete();
                $response = [
                    'message' => 'Stock delete.',
                    'retcode' => 0
                ];
            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            return response()->json($response);
        }

    }

    /**
     * 去比价，发送给用户一个消息
     */
    public function sendMessage(Request $request)
    {
        // 获取数据
        $openid = $request->input('openid');
        $messages = $request->input('message');

        // 发消息提醒用户
        $message = array('touser' => 1);
        $message['appid'] = env('WECHAT_ORIGINALID');
        $message['from'] = '2';
        $message['to'] = $openid;
        $message['MsgId'] = $this->uuid();
        $message['message'] = $messages;
        Queue::push(new Message((object)$message));

        return 1;
    }

    /**
     * 客服消息
     */
    public function receiveMessage(Request $request)
    {
        // 获取数据
        $openid = $request->input('openid');
        $messages = $request->input('message');

        Queue::push(new Message((object) [
            'MsgType' => 'text',
            'ToUserName' => env('WECHAT_ORIGINALID', 'gh_d30a13af0bc7'),
            'FromUserName'=> $openid,
            'MsgId' => $this->uuid(),
            'MediaId' => '',
            'Content' => $messages
        ]));
    }


    function a(){
        // 发消息提醒用户
        $message = array('touser' => 1);
        $message['appid'] = env('WECHAT_ORIGINALID');
        $message['from'] = '2';
        $message['to'] = 'oy7KgwETSAlnoqnd2QT5Or2ZBGoo';
        $message['MsgId'] = $this->uuid();
        $message['message'] = "Jason 贼神, 你想买的商品比价完成了, 去看看吧。 \n\n <a href='".env('APP_URL')."/pay/order/'>详情</a>";
        Queue::push(new Message((object)$message));
    }

    function b(){
        // 发消息提醒用户
        $message = array('touser' => 1);
        $message['appid'] = env('WECHAT_ORIGINALID');
        $message['from'] = '2';
        $message['to'] = 'oy7KgwETSAlnoqnd2QT5Or2ZBGoo';
        $message['MsgId'] = $this->uuid();
        $message['message'] = "Jason 贼神, 抱歉你想买的商品各商家都缺货, 如果还想买别的请拍照发在这里吧。 \n\n <a href='".env('APP_URL')."/pay/order/'>详情</a>";
        Queue::push(new Message((object)$message));
    }

    function c(){
        $message = (object)[
            'MsgType' => 'STOCKNOTENOUGH',
            'data' => [
                'first' => '家里有商品库存不足',
                'keyword1' => '刚好俠',
                'keyword2' => "益达木糖醇无糖口香糖草本精华56g等4件商品",
                'keyword3' => '小于7天',
                'remark'   => '如有疑问请联系客服'
            ],
            'url' => env('APP_URL').'/stock',
            'openid' => "oy7KgwETSAlnoqnd2QT5Or2ZBGoo"
        ];
        Queue::push(new Template($message));
    }

    function d(){
        $message = (object)[
            'MsgType' => 'STOCKNOTENOUGH',
            'data' => [
                'first' => '家里有商品用完了',
                'keyword1' => '刚好俠',
                'keyword2' => "益达木糖醇无糖口香糖草本精华56g等4件商品",
                'keyword3' => '已用完',
                'remark'   => '如有疑问请联系客服'
            ],
            'url' => env('APP_URL').'/stock',
            'openid' => "oy7KgwETSAlnoqnd2QT5Or2ZBGoo"
        ];
        Queue::push(new Template($message));
    }
}
