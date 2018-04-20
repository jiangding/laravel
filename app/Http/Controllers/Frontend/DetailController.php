<?php

namespace App\Http\Controllers\Frontend;
use App\Models\Detail;
use App\Models\Address;
use App\Models\Product;
use Carbon\Carbon;
use Queue;
use App\Jobs\Message;
use App\Http\Traits\AddressTrait;
use App\Http\Traits\ProductsTrait;
use App\Http\Traits\InvoiceTrait;
use App\Http\Traits\DetailTrait;
use App\Http\Traits\SpiderTrait;
use Illuminate\Http\Request;
use Log;

class DetailController extends Controller
{
    use AddressTrait, ProductsTrait, InvoiceTrait, DetailTrait, SpiderTrait;

    /**
     * 获取比价结果
     * @param Request $request
     * @param $uuid
     * @return v
     */
    public function price(Request $request, $uuid)
    {

        // 获取清单
        $detail = Detail::where('uuid',$uuid)->first();

        // 如果当前清单已结束
        if($detail['status'] == 2){
            return view('frontend.Detail.end', [
                'title' => '比价单已结束',
            ]);
        }

        // 当前时间
        $now_at = Carbon::now();

        // 失效时间
        $exp_at = $detail['end_at'];

        $currentUser = $this->currentUser;
        $title = '确认订单';

        // 获取用户所有地址
        $addresses = $this->getUserAddress($currentUser['id']);

        // 发票暂时屏蔽
        // $invoices = $this->getUserInvoice($currentUser['id'],$this->InvoiceRepository,$this->InvoiceTransformer);


        // 如果有地址就获取
        if($detail['addressid'] > 0)
        {
            $detail->address = Address::where('id',$detail['addressid'])->first();
        }
        // 如果没获取到
        if(!$detail->address && count($addresses) != 0){
            $detail->address = Address::where('id',$addresses[0]->id)->first();
        }


        // 获取邮费
        $detail->postage = json_decode($detail['postage']);

        // 获取产品信息
        $products = json_decode($detail['product'], true);

        // 处理数据
        $_ret = array();
        foreach ($products as $key => $product)
        {
            isset($_ret[$key])?'':($_ret[$key] = array());

            foreach ($product as $value)
            {
                // 查询产品信息
                $_product = Product::where('id', $value['id'])->with('category')->first();

                // 获取产品三大平台url
                $arrUrls = json_decode($_product->url);
//
//                // 重新爬取价格
//                $jsonPrice =  $this->spiders($request ,$arrUrls->JD, $arrUrls->YHD, $arrUrls->TMALL);

                // 获取产品爬取价格
                $spider = (array)json_decode($_product->spider);

                // 复制
//                if($spider[$key]->stock)
//                {
                    $_product->title = $spider[$key]->title;
                    $_product->price = $spider[$key]->price;
                    $_product->num = $spider[$key]->stock;
                    array_push($_ret[$key],$_product);
//                }
            }
        }
        $detail->products = $_ret;

        // 准备支付信息
        $config = $this->wechat->js->config(array('chooseWXPay'), false, false, false);

        // 如果超过二十分钟在点进来就失效
        if($now_at > $exp_at){
            return view('frontend.Detail.fail', [
                'title' => '比价结果失效',
                'detail' => $detail
            ]);
        }

        return view('frontend.Detail.commit', [
            'currentUser' => $currentUser,
            'title' => $title,
            'addresses' => $addresses,
            'detail' => $detail,
            'is_new' => $currentUser['is_new'],
            'debug' => 'false',
            'appId' => $config['appId'],
            'nonceStr' => $config['nonceStr'],
            'timestamp' => $config['timestamp'],
            'url' => $config['url'],
            'signature' => $config['signature'],
            'jsApiList' => json_encode($config['jsApiList'])
        ]);
    }

    /**
     * 点击去比价执行
     * @param Request $request
     * @return v
     */
    public function create(Request $request)
    {
        $type = 0;
        //清除session的购物车商品
        if ($request->session()->has('products')){
            $request->session()->forget('products');
            $type = $request->input('type');
        }

        $currentUser = $this->currentUser;
        $barcodes = json_decode($request->input('barcode'));
        // 获取uuid
        $uuid = $this->uuid();

        if ($request->ajax())
        {
            $this->isSpiderPrice($barcodes);

            try {
                $data = array(
                    'uuid' => $uuid,
                    'userid' => $currentUser['id'],
                    'kfid' => 0,
                    'addressid' => $this->getUserDefaultAddress($currentUser['id']),
                    'invoice_name' => '',
                    'invoice_type' => '',
                    'invoice_content' => '',
                    'postage' => '{"TMALL":20,"JD":6,"YHD":10}',
                    'product' => $this->barcodetojson_indetail($barcodes),
                    'type' => $type, // 0是主线， 1 是专线
                );
                // 判断是否有价格
                $c = $this->checkPriceBybarcodes($barcodes);
                // 自动返回已推送
                if($c){
                    $id = $this->insertDetail($data);
                    if($id){
                        // 用户推送消息给刚好
                        Queue::push(new Message((object) [
                            'MsgType' => 'text',
                            'ToUserName' => env('WECHAT_ORIGINALID', 'gh_d30a13af0bc7'),
                            'FromUserName'=> $currentUser['openid'],
                            'MsgId' => $this->uuid(),
                            'MediaId' => '',
                            'Content' => '用户【'.$currentUser['nickname'].'】新增一个购买意向，清单号为：'.$uuid
                        ]));

                        // 客服比价
                        $response = [
                            'message' => 'Detail create.',
                            'uuid'    => $uuid,
                            'retcode' => 0
                        ];
                    }
                }else{
                    $data['type'] = 3;
                    $data['status'] = 1;
                    $data['end_at'] = Carbon::now()->addHours(8);
                    $id = $this->insertDetail($data);
                    if($id){

                        // 跳转去支付
                        $response = [
                            'message' => 'pay',
                            'uuid'    => $uuid,
                            'retcode' => 2
                        ];
                    }
                }

                Log::info("current detaiid = ". $id);

            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            Log::info(response()->json($response));
            return response()->json($response);
        }
        else
        {
            return $this->index();
        }
    }


    /**
     *  提交并且支付
     */
    public function submitPay(Request $request)
    {
        $currentUser = $this->currentUser;
        $uuid = $request->input('uuid');
        $id = Detail::where('uuid', $uuid)->first()->id;
        if ($request->ajax())
        {
            try {
                $data = array(
                    'addressid' => $request->input('address_id'),
                    'platform' => $request->input('platform'),
                    'product' => $request->input('product')
                );

                $response = [
                    'message' => 'Detail create.',
                    'uuid'    => $uuid,
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
     * 提交清单
     * @param Request $request
     * @return v
     */
    public function submit(Request $request)
    {

        $currentUser = $this->currentUser;
        $uuid = $request->input('uuid');
        $id = Detail::where('uuid', $uuid)->first()->id;
        if ($request->ajax())
        {
            try {
                $data = array(
                    'addressid' => $request->input('address_id'),
//                    'invoice_name' => $request->input('invoice_name'),
//                    'invoice_type' => $request->input('invoice_type'),
//                    'invoice_content' => $request->input('invoice_content'),
                    'platform' => $request->input('platform'),
                    'product' => $request->input('product')
                );

                //$stock = $this->DetailRepository->update($data,$id);
//                Queue::push(new Message((object) [
//                    'MsgType' => 'SYSTEM',
//                    'ToUserName' =>'gh_4ddb6dd173b5',
//                    'FromUserName'=> $currentUser['openid'],
//                    'MsgId' => $this->uuid(),
//                    'MediaId' => '',
//                    'Content' => '用户【'.$currentUser['nickname'].'】更新了一个购买意向，单号为：'.$uuid
//                ]));
                $response = [
                    'message' => 'Detail create.',
                    'uuid'    => $uuid,
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

}
