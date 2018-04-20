<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Catalog;
use App\Http\Traits\ProductsTrait;
use App\Models\Record;
use Carbon\Carbon;
class ShoppingController extends Controller
{
    use ProductsTrait;

    public function scan(Request $request)
    {
        $config = $this->wechat->js->config(array('scanQRCode'),false,false,false);
        return view('frontend.Shopping.index',[
            'title' => '扫描条码',
            'debug' => 'false',
            'appId' => $config['appId'],
            'nonceStr' => $config['nonceStr'],
            'timestamp' => $config['timestamp'],
            'url' => $config['url'],
            'signature' => $config['signature'],
            'jsApiList' => json_encode($config['jsApiList']),
        ]);
    }



    /**扫码后，加入到购物车(实际上保存到session)
     * @param Request $request
     * @param $barcode
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shoppingCart(Request $request, $barcode){

        $config = $this->wechat->js->config(array('scanQRCode'),false,false,false);//扫码配置参数
        $currentUser = $this->currentUser;
        //如果是通过前端页面重新刷新进来的，直接显示页面，不添加商品到session

        if ($barcode == 1){
            $products = $request->session()->get('products');
            return view('frontend.Shopping.shoppingCart', [
                'products' => $products,
                'title' => '购物车',
                'currentUser' => $currentUser,
                'appId' => $config['appId'],
                'nonceStr' => $config['nonceStr'],
                'timestamp' => $config['timestamp'],
                'signature' => $config['signature'],
                'jsApiList' => json_encode($config['jsApiList'])
            ]);
        }

        if (!$request->session()->has('products')) {
            $request->session()->put('products', []);
        }
        $products = $request->session()->get('products');

        $product = Product::where('barcode', $barcode)->first();    // 查询该条码是否在产品库中存在
        if($product) {
            // 库中存在并且是否已经有记录
            $rRow = Record::where([
                'userid'=>$currentUser['id'],
                'deleted_at'=>null
            ])->where('productid',$product->id)->first();
            if($rRow){
                $rRow->updated_at = Carbon::now();
                $rRow->save();
            }else{
                $r = new Record();
                $r->userid = $currentUser['id'];
                $r->productid = $product->id;
                $r->save();
            }

            if($product['name']) {
                //查取分类
                $catalog = Catalog::where('id', $product->catid)->first();

                //加入到session
                if (!in_array($product, $products)) {
                    $products[] = $product;
                    $request->session()->put('products', $products);
                } else{
                    //否则将已经在购物车的记录提到最顶上
                    foreach ($products as $key=>$prod){
                        if (isset($prod['barcode']) && $prod['barcode'] == $product['barcode']){
                            unset($products[$key]);
                            $products[] = $product;
                            $request->session()->put('products', $products);
                        }
                    }
                }

                $products = array_reverse($products);

                return view('frontend.Shopping.shoppingCart', [
                    'products' => $products,
                    'product' => $product,
                    'catalog' => $catalog,
                    'barcode' => $barcode,
                    'title' => '立刻购买',
                    'currentUser' => $currentUser,
                    'appId' => $config['appId'],
                    'nonceStr' => $config['nonceStr'],
                    'timestamp' => $config['timestamp'],
                    'signature' => $config['signature'],
                    'jsApiList' => json_encode($config['jsApiList'])
                ]);
            }else{
                $this->addUserToProduct($product, $currentUser, 1);

                //加入到session

                if (!in_array($barcode, $products)) {
                    $products[] = $barcode;
                    $request->session()->put('products', $products);
                }else{
                    //否则将已经在购物车的记录提到最顶上
                    foreach ($products as $key=>$prod){
                        if ($prod == $barcode){
                            unset($products[$key]);
                            $products[] = $barcode;
                            $request->session()->put('products', $products);
                        }
                    }
                }
                $products = array_reverse($products);
                return view('frontend.Shopping.shoppingCart', [
                    'products' => $products,
                    'barcode' => $barcode,
                    'title' => '立刻购买',
                    'currentUser' => $currentUser,
                    'appId' => $config['appId'],
                    'nonceStr' => $config['nonceStr'],
                    'timestamp' => $config['timestamp'],
                    'signature' => $config['signature'],
                    'jsApiList' => json_encode($config['jsApiList'])
                ]);
            }
        }else {
            $this->insertProduct($barcode, $currentUser, 1);
            //加入到session

            if (!in_array($barcode, $products)) {
                $products[] = $barcode;
                $request->session()->put('products', $products);
            }

            return view('frontend.Shopping.shoppingCart', [
                'products' => $products,
                'barcode' => $barcode,
                'title' => '立刻购买',
                'currentUser' => $currentUser,
                'appId' => $config['appId'],
                'nonceStr' => $config['nonceStr'],
                'timestamp' => $config['timestamp'],
                'signature' => $config['signature'],
                'jsApiList' => json_encode($config['jsApiList'])
            ]);
        }
    }



    public function delete(Request $request){
        $id = $request->input('id');
        $products = $request->session()->get('products');
        $length = count($products);
        foreach ($products as $key => $product){
            if (isset($product['id']) && $product['id'] == $id){
                unset($products[$key]);
            } else if ($product == $id){
                unset($products[$key]);
            }
        }


        $products = $request->session()->put('products', $products);
        if (count($products) == $length){
            $returnType = 0;
        } else {
            $returnType = 1;
        }

        return json_encode($returnType);
    }
}
