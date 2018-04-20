<?php

namespace App\Http\Traits;
use Log;
use Cache;
use Queue;
use Config;
use App\Jobs\Message;
use Carbon\Carbon;
use App\Jobs\CallProductInfobyBarcode;
use App\Models\Product;
use Ramsey\Uuid\Uuid;
use App\Models\Record;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
trait ProductsTrait
{
    /**
     * 获取商品barcodes
     * @param array $barcodes
     * @return string
     */
    public function barcodetojson_indetail(array $barcodes)
    {
//        $ProductsRepository = app(ProductsRepository::class);
//        $products = $ProductsRepository->findWhereIn('barcode',$barcodes);
        $products = Product::whereIn('barcode',$barcodes)->get();
        $json = array();
        $json['TMALL'] = array();
        $json['JD'] = array();
        $json['YHD'] = array();
        foreach ($products as $product)
        {
            $json['TMALL'][] = array('id' => $product->id,'num' => 1);
            $json['JD'][] = array('id' => $product->id,'num' => 1);
            $json['YHD'][] = array('id' => $product->id,'num' => 1);
        }

        return json_encode($json);

    }

    /**
     *  根据barcodes判断产品是否爬取了价格
     */
    public function isSpiderPrice(array $barcodes)
    {
        $products = Product::whereIn('barcode',$barcodes)->get();

        $client = new Client();
        // 数目
        foreach($products as $product){
            $arrUrl = (array)json_decode($product->url, true);
            if($arrUrl['TMALL']) {
                $uri = 'http://sapi.manmanbuy.com/searchAPI.ashx?method=searchapi_proinfobyurl&AppKey=5PLE95QXih6Vo9ZI&url=' . urlencode($arrUrl['TMALL']);
                $request = new Request('GET', $uri);
                $response = $client->send($request);
                Log::info($product->id);
                Log::info("INTERFACE RETURN:".$response->getBody());
                #$arr = json_decode(iconv("gb2312", "UTF-8", $response->getBody()), true);
                #$stb = json_decode($arr['result'], true);
                #$price =  round($stb['proprice'],2);
                $arr = explode("proprice", $response->getBody());
                if(isset($arr[1])){
                    $price = preg_replace('/[^\.0123456789]/s', '', $arr[1]);
                    Log::info($price);
                    if($price){
                        $p = Product::find($product->id);
                        $spider = json_decode($p->spider,true);
                        $spider['TMALL']["price"] = $price;
                        $p->spider = json_encode($spider);
                        Log::info($p->save());
                    }
                }
            }
        }
    }

    /**
     * 检查条码价格情况
     */
    public function checkPriceBybarcodes($barcodes)
    {
        $c = 0;
        $products = Product::whereIn('barcode',$barcodes)->get();
        // 数目
        foreach($products as $product) {
            $arrSpider = (array)json_decode($product->spider, true);
            if($arrSpider['TMALL']["price"] < 0.01 && $arrSpider['JD']["price"] < 0.01 && $arrSpider['YHD']["price"] < 0.01){
                $c = 1;
            }
        }
        Log::info("ccccc=".$c);
        return $c;
    }

    /**
     * 根据条码查产品
     */
    public function getProductbyBarcode($barcode)
    {

        $product = Product::where('barcode', $barcode)->first();
        if($product)
        {
            return $product;
        }
        else
        {
            return false;
        }

    }

    /**
     * 新增一个product
     */
    public function insertProduct($barcode , $user, $t = 0)
    {
        $product = new Product();
        $product->barcode = $barcode;

        $product->catid = 1; // 默认是1
        $product->url = '{"JD": "", "YHD": "", "TMALL": ""}';
        $product->spider = '{"JD": {"price": "0", "stock": "1", "title": ""}, "YHD": {"price": "0", "stock": "1", "title": ""}, "TMALL": {"price": "0", "stock": "1", "title": ""}}';
        $product->status = -1;
        if($t == 1){
            $product->t = $t;
        }else{
            $product->userid = $user['id'];
        }
        $affect = $product->save();
        if($affect){
            // 添加数据到扫码足迹
            $productRow = Product::where('barcode',$barcode)->first();
            if($productRow){
                $r = new Record();
                $r->userid = $user['id'];
                $r->productid = $productRow->id;
                $r->save();
            }

            Queue::push(new Message((object) [
                'MsgType' => 'text',
                'ToUserName' => env('WECHAT_ORIGINALID', 'gh_d30a13af0bc7'),
                'FromUserName'=> $user['openid'],
                'MsgId' => str_replace('-','',(string) Uuid::uuid4()),
                'MediaId' => '',
                'Content' => "『".$user['nickname']."』扫描的条码 ".$barcode." 没有匹配"
            ]));
        }
    }

    /**
     *  添加用户到扫码失败的产品
     */
    public function addUserToProduct($product, $user, $t = 0)
    {
        $curUserId = $product->userid;

        // 如果没扫过
        if(strpos((string)$curUserId, (string)$user['id']) === false){
            if($t == 1){
                $product->t = $t;
            }else{
                // 没有用户
                if($curUserId == 0){
                    $product->userid = $user['id'];
                }else{
                    $product->userid = $curUserId.','.$user['id'];
                }

            }
            $affect = $product->save();
            if($affect){
                Queue::push(new Message((object) [
                    'MsgType' => 'text',
                    'ToUserName' => env('WECHAT_ORIGINALID', 'gh_d30a13af0bc7'),
                    'FromUserName'=> $user['openid'],
                    'MsgId' => str_replace('-','',(string) Uuid::uuid4()),
                    'MediaId' => '',
                    'Content' => "『".$user['nickname']."』扫描的条码 ".$product->barcode." 没有匹配"
                ]));
            }
        }

    }
}