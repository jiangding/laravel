<?php

namespace App\Http\Controllers\Backend;

use App\Models\Catalog;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests;
use App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\components\cusResponse;
use App\Jobs\ProductImport;
use zgldh\UploadManager\UploadManager;
use Queue;
use App\Models\Stock;
use App\Jobs\Message;
use App\Models\User;
use Excel;
use App\Http\Traits\SpiderTrait;


class ProductController extends Controller
{
    use SpiderTrait;
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * 产品主页
     */
    public function index(Request $request){

        // 获取数据
        $barcode = $request->input('barcode');
        $name = $request->input('name');
        $trademark = $request->input('trademark');
        $status = $request->input('status');
        $type = $request->input('type');

        $productWhere = Product::orderBy('created_at', 'desc');
        // barcode
        if($barcode){
            $productWhere->where('barcode', 'like', "%".$barcode."%");
        }

        // 产品名
        if($name){
            $productWhere->where('name', 'like', "%".$name."%");
        }

        if($trademark){
            $productWhere->where('trademark', 'like', "%".$trademark."%");
        }

        // 状态
        if($status != 'all' && isset($status)){
            $productWhere->where('status', $status);
        }

        // type
        if($type != 'all' && isset($type)){
            $productWhere->where('t', $type);
        }
        // 查询
        $data = $productWhere->paginate(15);

        foreach($data as $kc => $vc){
            $userid = $vc['userid'];
            if(strpos($userid,',')){
                $astr = explode(',', $userid);
                $uid = $astr[0];
            }else{
                $uid = $userid;
            }
            $vc['user'] =  User::find($uid);
        }

        // 分页追加参数
        $appends = [
            'barcode' => $barcode,
            'name' => $name,
            'trademark' => $trademark,
            'status' => $status,
            'type' => $type,
        ];
        return view('backend.product', [
            'data' => $data,
            'appends' => $appends,
        ]);
    }

    /**
     * 产品编辑
     */
    public function edit(Request $request,$id,$did = 0){

        $openid = $request->input('openid');
        $uuid = $request->input('uuid');

        $row = Product::find($id);

        // 分类
        $cates = $data = Catalog::orderBy('id', 'desc')->get(['id', 'name']);
        return view('backend.product-edit', [ 'row' => $row, 'did' => $did , 'openid' => $openid, 'uuid' => $uuid, 'cates'=>$cates]);
    }

    /**
     * 爬取
     */
    public function spider(Request $request){

        $jd = $request->input('jd');
        $tmall = $request->input('tmall');
        $yhd = $request->input('yhd');

        $zjd = $request->input('zjd');
        $ztmall = $request->input('ztmall');
        $zyhd = $request->input('zyhd');
        // 获取爬取价格
        $jsonPrice = $this->spiders($jd, $yhd, $tmall);
        $jsonArr = json_decode($jsonPrice, true);
        // 获取爬取价格
        $jsonPricez = $this->spiders($zjd, $zyhd, $ztmall);
        $jsonArrz = json_decode($jsonPricez, true);
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

        if ($jsonArrz['jd'] <= 0) {
            $jsonArrz['jd'] = 0;
        }

        if ($jsonArrz['yhd'] <= 0) {
            $jsonArrz['yhd'] = 0;
        }

        if ($jsonArrz['tmall'] <= 0) {
            $jsonArrz['tmall'] = 0;
        }

        return ['z'=>$jsonArr,'f'=>$jsonArrz];
    }
    /**
     *  产品执行更新
     */
    public function update(Request $request){
        // 校验数据
        $validator = Validator::make($request->all(), [
            'name'		=>	'string',
//            'manufacturer'		=>	'string',
            'spec'		=>	'string',
            'trademark'		=>	'string',
            'jdUrl'		=>	'string',
            'tmallUrl'	=>	'string',
            'yhdUrl'	=>	'string',
            'zjdUrl'	=>	'string',
            'ztmallUrl'	=>	'string',
            'zyhdUrl'	=>	'string',
        ]);

        $cusResponse = new cusResponse();
        // 验证失败
        if ($validator->fails()) {
            // 返回jason
            $cusResponse->status = 5;
            $cusResponse->message = $validator->errors()->first();
            return $cusResponse->toJson();
        }

        $jdUrl = $request->input('jdUrl');
        $yhdUrl = $request->input('yhdUrl');
        $tmallUrl = $request->input('tmallUrl');
        $zjdUrl = $request->input('zjdUrl');
        $zyhdUrl = $request->input('zyhdUrl');
        $ztmallUrl = $request->input('ztmallUrl');
        // 状态
        if($jdUrl && $yhdUrl && $tmallUrl){
            $status = 2;
        }else{
            $status = 1;
        }
        if(!$jdUrl && !$yhdUrl && !$tmallUrl){
            $status = 0;
        }

        // 组合
        $arrUrl = array(
            'JD'=>$jdUrl,
            'YHD'=>$yhdUrl,
            'TMALL'=>$tmallUrl,
        );
        $zarrUrl = array(
            'JD'=>$zjdUrl,
            'YHD'=>$zyhdUrl,
            'TMALL'=>$ztmallUrl,
        );

        $id = $request->input('id');
        $product = Product::find($id);
        $product->name = $request->input('name');
        $product->spec = $request->input('spec');
//        $product->catid = $this->getCatalogsbyName($request->input('name'));
        $product->catid = $request->input('catid');
        $product->trademark = $request->input('trademark');
        $product->manufacturer = $request->input('manufacturer');
        $product->url = json_encode($arrUrl);
        $product->replace_url = json_encode($zarrUrl);
        $product->status = $status;
        $affect = $product->save();
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
     * 产品导入
     */

    public function import(Request $request)
    {
        set_time_limit(0);
        $manager = UploadManager::getInstance();
        $upload = $manager->upload($request->file('upload'));
        $upload->save();

        Excel::load('storage/app/public/'.$upload->path, function($reader) {
            $data = $reader->all();

            foreach($data as $item){
                $barcode = $item['条形码'];
                $name = $item['产品名'];
                $cname = trim($item['品类']);
                #$spec = $item['规格'];
                #$trademark = $item['品牌'];
                #$TMALL = $item['天猫超市链接']?$item['天猫超市链接']:'';
                #$JD = $item['京东链接']?$item['京东链接']:'';
                $YHD = $item['一号店链接']?$item['一号店链接']:'';
                $price = $item['价格'];
                if($cname){
                    $catRow = Catalog::where('name', $cname)->first();
                    $cid = $catRow->id;
                }else{
                    $cid = 1;
                }


                // 状态
//                if($JD && $YHD && $TMALL){
//                    $status = 2;
//                }else{
//                    $status = 1;
//                }
//                if(!$JD && !$YHD && !$TMALL){
//                    $status = 0;
//                }

                if($barcode){
                    $brow = Product::where("barcode", $barcode)->first();
                    if(!$brow){
                        $p = new Product;
                        $p->barcode = $barcode;
                        $p->name = $name;
                        $p->catid = $cid;
                        $p->status = 1;
                        $p->url = json_encode(['TMALL'=> '','JD'=> '','YHD'=> $YHD]);
                        $p->spider = json_encode([
                            'TMALL' => ['title' => '','stock' => '1','price' => '0.00'],
                            'JD' => ['title' => '','stock' => '1','price' => '0.00'],
                            'YHD' => ['title' => '','stock' => '1','price' => $price]
                        ]);
                        $p->save();
                    }
                }

//                Product::updateOrCreate([
//                    'barcode' => $barcode
//                ],[
//                    'name' => $name,
//                    //'manufacturer' => $manufacturer,
//                    'spec' => $spec,
//                    'trademark' => $trademark,
//                    'catid' => $cid,
////                    'catid'=>$this->getCatalogsbyName($name),
//                    'status'=>$status,
//                    'url' => json_encode(['TMALL'=> $TMALL,'JD'=> $JD,'YHD'=> $YHD]),
//                    'spider' => json_encode([
//                        'TMALL' => ['title' => '','stock' => '1','price' => '0.00'],
//                        'JD' => ['title' => '','stock' => '1','price' => '0.00'],
//                        'YHD' => ['title' => '','stock' => '1','price' => '0.00']
//                    ])
//                ]);
            }
        });

        return $upload;
    }

    /**
     * 产品导出
     */
    public function export()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1G');
        $cellData = array(
            array('条形码','产品名','品类','品牌','规格','状态','京东链接','天猫超市链接','一号店链接')
        );

        // 数据导出
        $productWhere = Product::orderBy('created_at', 'desc')->with('category');
        $data = $productWhere->get();

        $arr = [];
        foreach($data as $k=>$v){
            $arr[] = $v->barcode.' ';
            $arr[] = $v->name;
            $arr[] = $v->category->name;
            $arr[] = $v->trademark;
            $arr[] = $v->spec;
            switch($v->status){
                case "-1":
                    $arr[] = '异常';
                    break;
                case "0":
                    $arr[] = '未处理';
                    break;
                case "1":
                    $arr[] = '部分url';
                    break;
                case "2":
                    $arr[] = '完整url';
                    break;
            }
            // 平台url
            $url = json_decode($v->url);
            $arr[] = $url->JD;
            $arr[] = $url->TMALL;
            $arr[] = $url->YHD;

            $cellData[] = $arr;
            unset($arr);
        }

        Excel::create('产品列表',function($excel) use ($cellData){
            $excel->sheet('sheet', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }


    /**
     *  产品获取用户信息
     */
    public function getUser(Request $request)
    {
        if($request->ajax()) {
            $id = $request->input('id');
            $uidStr = $request->input('uid');

            $uidArr = explode(',',$uidStr);
            $retArr['pid'] = $id;
            foreach($uidArr as $uid){
                $retArr['user'][] = User::find($uid);
            }

            return $retArr;
        }
    }

    /**
     * 产品推送给用户
     */

    public function toUser(Request $request)
    {

        if($request->ajax()){

            $cusResponse = new cusResponse();

            $id = $request->input('id');
            $uidStr = $request->input('uid');

            $uidArr = explode(',',$uidStr);

            // 查找产品信息
            $product = Product::find($id);
            // 遍历
            foreach($uidArr as $u){
                // 查找商品条码
                $stock = Stock::where(['userid'=>$u, 'productid'=>$id])->first();
                if(!$stock){
//                    $cusResponse->status = 500;
//                    $cusResponse->message = "当前用户已推送该产品";
//                    return $cusResponse->toJson();exit;

                    if($product['name']) {
                        $data = array(
                            'userid' => $u,
                            'productid' => $id,
                            'quantity' => 0,
                            'cycle' => 0,
                            'last' => 0,
                            'lastday' => 0,
                            'status' => 1
                        );
                        Stock::create($data);
                    }

                    // 根据用户id 获取 openid
                    $user = User::find($u);

                    if($product['name']){
                        $msg = "".$user->nickname."，你扫描的商品条码 ".$product['barcode']."『".$product['name']."』已经添加到你的存货清单了，去看看吧。\n\n <a href='".env('APP_URL')."/stock/update/".$product['id']."'>详情</a>";
                    }else{
                        $msg = "".$user->nickname."，抱歉我们在平台上没有找到你刚才扫描码".$product['barcode']."对应的商品，有疑问请召唤客服吧。";
                    }

                    // 客服
                    $admin = $request->session()->get('gh_admin');

                    // 发消息提醒用户
                    $message = array('touser' => 1);
                    $message['appid'] = env('WECHAT_ORIGINALID');
                    $message['from'] = $admin->id;
                    $message['to'] = $user->openid;
                    $message['MsgId'] = $this->uuid();
                    $message['message'] = $msg;
                    Queue::push(new Message((object)$message));
                }
            }
            $cusResponse->status = 200;
            $cusResponse->message = "ok";
            return $cusResponse->toJson();
        }
    }


    public function updating(){

        $products = Product::orderBy('id', 'asc')->where('name','!=', '')->get();
        foreach($products as $p){
            $p->catid = $this->getCatalogsbyName($p->name);
            $p->save();
        }

    }

    private function getCatalogsbyName($name)
    {
        $catalogs = Catalog::orderBy('id', 'desc')->where('id', '!=', 1)->get(['id', 'keyword']);

        foreach ($catalogs as $k => $catalog)
        {
            $keywords = explode('|',$catalog['keyword']);
            foreach ($keywords as $keyword)
            {
                if(!empty($keyword))
                {
                    if(preg_match('/'.$keyword.'/i', $name))
                    {

                        return $catalog['id'];
                    }
                }
            }

        }
        return 1;
    }
}
