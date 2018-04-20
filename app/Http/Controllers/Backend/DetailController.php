<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Detail;
use App\Models\Product;
use App\Http\Controllers\Backend;
use App\Http\Traits\SpiderTrait;
use Illuminate\Support\Facades\Log;
use App\Models\components\cusResponse;
use Queue;
use App\Jobs\Message;
use App\Http\Traits\AddressTrait;
use App\Http\Traits\ProductsTrait;

class DetailController extends Controller
{
    use SpiderTrait,AddressTrait,ProductsTrait;
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 清单list
     */

    public function index(Request $request){
        // 获取数据
        $uuid = trim($request->input('uuid'));
        $type = $request->input('type');
        $status = $request->input('status');

        $detailWhere = Detail::orderBy('id','desc');

        if($uuid){
            $detailWhere->where('uuid', 'LIKE', '%'.$uuid.'%');
        }

        // 类型
        if($type != 'all' && isset($type)){
            $detailWhere->where('type', $type);
        }

        // 状态
        if($status != 'all' && isset($status)){
            $detailWhere->where('status', $status);
        }

        // 获取数据
        $data = $detailWhere->with('user')->paginate(15);
        // 分页追加参数
        $appends = [
            'uuid' => $uuid,
            'type'=>$type,
            'status'=>$status
        ];
        return view('backend.detail', [ 'data' => $data, 'appends'=>$appends ]);
    }

    /**
     * 清单添加
     */
    public function add(Request $request,$openid){

        // 如果
        $uid = $request->input('uuid');

        // 找出当前用户
        $user = User::where('openid', $openid)->first();

        if($uid == 1) {
            // 用户id
            $userId = $user->id;
            // 当前用户
            $curDetail = Detail::where('userid', $userId)->where('type', 2)->where('status', 0)->first();
            if(empty($curDetail)) {
                $uuid = $this->uuid();
                // 生成清单
                $data = array(
                    'uuid' => $uuid,
                    'userid' => $userId,
                    'kfid' => 0,
                    'addressid' => $this->getUserDefaultAddress($userId),
                    'invoice_name' => '',
                    'invoice_type' => '',
                    'invoice_content' => '',
                    'type' => 2,
                    'postage' => '{"TMALL":20,"JD":6,"YHD":10}',
                    'product' => '{}',
                );
                Detail::create($data);
                // 赋值
                $uid = $uuid;
            }else{
                $uid = $curDetail->uuid;
            }
        }
        // 获取当前数据
        $row =  Detail::where('uuid',$uid)->first();
        if($row){
            // 邮费
            $postage = json_decode($row['postage'],'true');

            // 产品
            $product = json_decode($row['product'], 'true');

            // 遍历获取当前清单所有的产品id
            $pidArr = array();
            if(isset($product['JD'])){
                foreach($product['JD'] as $k=>$v){
                    $pidArr[] = $v['id'];
                }
            }
            // 查找所有产品信息
            $products = Product::whereIn('id', $pidArr)->get();

            // 日志
            $logs = \App\Models\Log::orderBy('created_at','desc')->where('type',0)->where('pkey',$row->uuid)->with('user')->with('admin')->get();

            return view('backend.detail-add', [
                'user' => $user,
                'postage' => $postage,
                'products' => $products,
                'row' => $row,
                'logs' => $logs
            ]);
        }


    }

    /**
     * 清单产品添加
     */
    public function productAdd(Request $request)
    {
        $uuid = $request->input('uuid');
        $productNo = trim($request->input('productNo'));

        // 清单
        $d = Detail::where('uuid', $uuid)->first();

        // 当前产品
        $curProduct  =json_decode($d['product'], 'true');

        // 产品
        $p = Product::where('barcode', $productNo)->first();
        if(!empty($p)){
            $pid = $p['id'];
        }else{
            // 新增
            $data['barcode'] = $productNo;
            $data['cateid'] = 1;
            $data['status'] = -1;
            $pMod = Product::create($data);
            $pid = $pMod->id;
        }
        // 组合
        $pArr = array('id' => $pid,'num' => 1);
        $curProduct['TMALL'][] = $pArr;
        $curProduct['JD'][] = $pArr;
        $curProduct['YHD'][] = $pArr;

        // 更新
        $d->product = json_encode($curProduct);
        $affect = $d->save();

        $cusResponse = new cusResponse();
        if($affect){
            $cusResponse->status = 200;
            $cusResponse->message = "success";
        }else{
            $cusResponse->status = 500;
            $cusResponse->message = "failed";
        }
        return $cusResponse->toJson();
    }

    /**
     * 清单更新
     */
    public function update(Request $request){

        $uuid = $request->input('uuid');
        $userid = $request->input('userid');
        $arrBarcodes = $request->input('barcode');

        $arrBars = array();
        // barcode
        foreach($arrBarcodes as $v){
            if($v){
                $product = Product::where('barcode', $v)->first();
                if(!$product){
                    $p = new Product();
                    $p->barcode = $v;
                    $p->catid = 1; // 默认是1
                    $p->url = '{"JD": "", "YHD": "", "TMALL": ""}';
                    $p->spider = '{"JD": {"price": "0", "stock": "1", "title": ""}, "YHD": {"price": "0", "stock": "1", "title": ""}, "TMALL": {"price": "0", "stock": "1", "title": ""}}';
                    $p->status = -1;
                    $p->save();
                }
                array_push($arrBars, $v);
            }
        }

        $data = array(
            'uuid' => $uuid,
            'userid' => $userid,
            'kfid' => 0,
            'addressid' => $this->getUserDefaultAddress($userid),
            'invoice_name' => '',
            'invoice_type' => '',
            'invoice_content' => '',
            'type' => 1,
            'postage' => '{"TMALL":20,"JD":6,"YHD":10}',
            'product' => $this->barcodetojson_indetail($arrBars)
        );

        $affect = Detail::create($data);

        $cusResponse = new cusResponse();
        if($affect){
            $cusResponse->status = 200;
            $cusResponse->message = "success";
        }else{
            $cusResponse->status = 500;
            $cusResponse->message = "未获取到服务器数据";
        }
        return $cusResponse->toJson();
    }

    /**
     * 清单详情
     */
    public function detail($did){

        // 获取当前点击数据
        $row =  Detail::where('id',$did)->with(['user'=>function($q){
            $q->select('id','nickname','openid','avatar');
        }])->first();
        if($row){
            // 邮费
            $postage = json_decode($row['postage'],'true');

            // 产品
            $product  =json_decode($row['product'], 'true');

            // 遍历获取当前清单所有的产品id
            $pidArr = array();
            foreach($product['JD'] as $k=>$v){
                $pidArr[] = $v['id'];
            }

            // 查找所有产品信息
            $products = Product::whereIn('id', $pidArr)->with(['category'=>function($query){
                $query->select('id','name','icon');
            }])->get();

            // 日志
            $logs = \App\Models\Log::orderBy('created_at','desc')->where('type',0)->where('pkey',$row->uuid)->with('user')->with('admin')->get();

            return view('backend.detail-detail', compact('row', 'postage', 'products', 'logs'));
        }

    }

    /**
     *  清单价格
     */
    public function priceEdit($id)
    {
        // 获取产品数据
        $row =  Product::where('id',$id)->first();
        if($row) {
            // 价格
            $price = json_decode($row['spider'],'true');

            return view('backend.detail-price-edit', compact('price', 'id'));
        }
    }

    /**
     *  清单价格更改
     */
    public function priceUpdate(Request $request)
    {
        $id = $request->input('id');
        $jd = $request->input('jd');
        $tmall = $request->input('tmall');
        $yhd = $request->input('yhd');

        // 组合数据
        $spiderArr = array(
            'JD' => array(
                "price" => $jd,
                "stock" => (int)$request->input('jd_min_count'),
                "title" => ""
            ),
            'YHD' => array(
                "price" => $yhd,
                "stock" => (int)$request->input('yhd_min_count'),
                "title" => ""
            ),
            'TMALL' => array(
                "price" => $tmall,
                "stock" => (int)$request->input('tmall_min_count'),
                "title" => ""
            ),
        );

        // 更新数据
        $product = Product::find($id);
        if(!$product){
            return ;
        }
        $product->spider = json_encode($spiderArr);
        $affected = $product->save();

        // 返回数据
        $cusResponse =  new cusResponse();
        if($affected){
            Log::info('手动价格更新成功!'.$affected);
            $cusResponse->status = 0;
            $cusResponse->message = '成功';
        }else{
            Log::info('手动价格更新失败!'.$affected);
            $cusResponse->status = 500;
            $cusResponse->message = '更新失败';
        }
        return $cusResponse->toJson();
    }

    /**
     *  清单邮费
     */
    public function postageEdit($id)
    {
        // 获取当前点击数据
        $row =  Detail::where('id',$id)->first();
        if($row) {
            // 邮费
            $postage = json_decode($row['postage'],'true');

            return view('backend.detail-postage-edit', compact('postage', 'id'));
        }
    }

    /**
     *  清单邮费更改
     */
    public function postageUpdate(Request $request)
    {

        $id = $request->input('id');
        $jd = $request->input('jd');
        $tmall = $request->input('tmall');
        $yhd = $request->input('yhd');

        // 组合数据
        $detailArr = array(
            'JD' => $jd,
            'TMALL' => $tmall,
            'YHD' => $yhd,
        );

        // 更新数据
        $detail = Detail::find($id);
        if(!$detail){
            return ;
        }
        $detail->postage = json_encode($detailArr);
        $affected = $detail->save();

        // 返回数据
        $cusResponse =  new cusResponse();
        if($affected){
            Log::info('邮费更新成功!'.$affected);
            $cusResponse->status = 0;
            $cusResponse->message = '成功';
        }else{
            Log::info('邮费更新失败!'.$affected);
            $cusResponse->status = 500;
            $cusResponse->message = '更新失败';
        }
        return $cusResponse->toJson();
    }

    /**
     *  清单 爬取
     */

    public function spider(Request $request)
    {
        $jd = $request->input('jd');
        $tmall = $request->input('tmall');
        $yhd = $request->input('yhd');
        $ids = $request->input('id');

        // 批量爬取
        foreach($jd as $k => $v) {

            // 获取爬取价格
            $jsonPrice = $this->spiders($jd[$k], $yhd[$k], $tmall[$k]);
            Log::info('spider ' . $jsonPrice);

            $jsonArr = json_decode($jsonPrice, true);

            // 默认为0
            if ($jsonArr['jd'] <= 0) {
                $jsonArr['jd'] = 0;
            }

            if ($jsonArr['yhd'] <= 0) {
                $jsonArr['yhd'] = 0;
            }

            if ($jsonArr['tmall'] <= 0) {
                $jsonArr['tmall'] = 0;
            }
            $spiderArr = array(
                'JD' => array(
                    "price" => $jsonArr['jd'],
                    "stock" => "1",
                    "title" => ""
                ),
                'YHD' => array(
                    "price" => $jsonArr['yhd'],
                    "stock" => "1",
                    "title" => ""
                ),
                'TMALL' => array(
                    "price" => $jsonArr['tmall'],
                    "stock" => "1",
                    "title" => ""
                ),
            );

            // 更新数据
            $product = Product::find($ids[$k]);
            if (!$product) {
                continue;
            }
            $product->spider = json_encode($spiderArr);
            $affected = $product->save();

            sleep(4);
        }
        // 返回数据
        $cusResponse =  new cusResponse();
        if($affected){
            $cusResponse->status = 0;
            $cusResponse->message = '成功';
        }else{
            $cusResponse->status = 500;
            $cusResponse->message = '更新失败';
        }
        return $cusResponse->toJson();
    }

    /**
     * 推送清单比价结果给用户
     */
    public function toUser(Request $request)
    {
        if($request->ajax()){

            $cusResponse = new cusResponse();

            $id = $request->input('id');
            $openid = $request->input('openid');

            // 查找当前清单
            $detail = Detail::find($id);
            if(!$detail){
                $cusResponse->status = 500;
                $cusResponse->message = "没有找到当前清单";
                return $cusResponse->toJson();exit;
            }

            // 是否20分钟之内推送过
            $end_at = $detail['end_at'];
            if(Carbon::now() < $end_at){
                $cusResponse->status = 500;
                $cusResponse->message = "该清单8小时之内已被推送!";
                return $cusResponse->toJson();exit;
            }
            // 计算当前是否包邮  京东99 天猫88 一号店68
            // 邮费
            $postage = json_decode($detail['postage'],'true');

            // 产品
            $product  =json_decode($detail['product'], 'true');

            // 遍历获取当前清单所有的产品id
            $pidArr = array();
            foreach($product['JD'] as $k=>$v){
                $pidArr[] = $v['id'];
            }

            // 查找所有产品信息
            $products = Product::whereIn('id', $pidArr)->get(['id', 'spider']);
            $jd = 0;
            $yhd = 0;
            $tmall = 0;
            foreach($products as $kk=>$vv){
                // 一个产品三大平台价格
                $arrPrice = json_decode($vv['spider'], 'true');
                $jd += $arrPrice['JD']['price'];
                $yhd += $arrPrice['YHD']['price'];
                $tmall += $arrPrice['TMALL']['price'];
            }
            if($jd > 99){
                $postage['JD'] = 0;
            }
            if($tmall > 88){
                $postage['TMALL'] = 0;
            }
            if($yhd > 68){
                $postage['YHD'] = 0;
            }
            // 客服
            $admin = $request->session()->get('gh_admin');

            // 修改清单状态
            $detail->status = 1;
            $detail->kfid = $admin->id;
            $detail->postage = json_encode($postage);
            $detail->end_at = Carbon::now()->addHours(8);

            $affect = $detail->save();

            if($affect){

                // 发消息提醒用户
                $message = array('touser' => 1);
                $message['appid'] = env('WECHAT_ORIGINALID');
                $message['from'] = $admin->id;
                $message['to'] = $openid;
                $message['MsgId'] = $this->uuid();

                // 用户
                $user = User::where('openid', $openid)->first();

                if($detail->type){
                    $message['message'] = $user['nickname']."，你想买的商品比价完成了，去看看吧。 \n\n <a href='".env('APP_URL')."/detail/price/".$detail->uuid."'>详情</a>";
                }else{
                    $message['message'] = $user['nickname']."，你的比价完成了，请在20分钟内支付吧。 \n\n <a href='".env('APP_URL')."/detail/price/".$detail->uuid."'>详情</a>";
                }
                Log::info($message);
                Queue::push(new Message((object)$message));

                $cusResponse->status = 200;
                $cusResponse->message = "ok";

                // 记录日志
                $l = new \App\Models\Log();
                $l->mid = $admin->id;
                $l->pkey = $detail->uuid;
                $l->pval = "推送清单";
                $l->save();
                return $cusResponse->toJson();

            }

        }
    }


    /**
     * 删除清单产品
     */
    public function productDelete(Request $request)
    {
        $uuid = $request->input('uuid');
        $pid = $request->input('pid');

        // 清单
        $d = Detail::where('uuid', $uuid)->first();

        // 清单产品
        $curProduct  =json_decode($d['product'], 'true');

        foreach($curProduct['JD'] as $k=>$v){
            if($v['id'] == $pid){
                unset($curProduct['JD'][$k]);
                unset($curProduct['YHD'][$k]);
                unset($curProduct['TMALL'][$k]);
            }
        }

        $d->product = json_encode($curProduct);
        $affect = $d->save();

        $cusResponse = new cusResponse();
        if($affect){
            $cusResponse->status = 200;
            $cusResponse->message = "success";
        }else{
            $cusResponse->status = 500;
            $cusResponse->message = "failed";
        }
        return $cusResponse->toJson();
    }
}

