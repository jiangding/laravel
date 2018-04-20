<?php

namespace App\Http\Traits;
use Log;
use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use App\Models\Detail;

trait OrderTrait
{
    /**
     * 新增订单
     * $plaform
     * @param $products
     * @param $addressid
     * @param $detailRow
     * @param $is_new
     * @return array
     */
    public function newOrder($plaform, $products, $addressid , $detailRow, $is_new)
    {
        // 地址
        $address  = Address::find($addressid);
        $products = (array)json_decode($products)->$plaform;

//        $postage = json_decode($detailRow->postage);
//
//        // 邮费
//        $post = $postage->$plaform;
        $total = 0;
        // 订单包含的商品信息
        $stuStr =  '刚好商品';
        foreach ($products as $key=>$product)
        {
//            if (!isset($product->id)) continue;

            if (!empty($product->id)) {
                $_product = Product::find($product->id);
                $spider = (array)json_decode($_product->spider);
//            $products[$key]->name = $spider[$plaform]->title;
                $products[$key]->name = $_product->name;
                $products[$key]->price = $spider[$plaform]->price;
                $products[$key]->spec = $_product->spec;
                $products[$key]->icon = $_product->category->icon;
                $total = bcadd($total, bcmul($spider[$plaform]->price, $products[$key]->num, 2), 2);

                // 取第一个
                if ($key == 0) {
                    $stuStr = $_product->name;
                }
            }
        }


        // 计算邮费
        switch($plaform){
            case "JD":
                if($total < 99 && !$is_new){
                    $total += 6;
                }
                break;
            case "TMALL":
                if($total < 88  && !$is_new){
                    $total += 20;
                }
                break;
            case "YHD":
                if($total < 68  && !$is_new ){
                    $total += 10;
                }
                break;
        }

        $uuid = $detailRow->uuid;
        $data = array(
            'orderid' => $this->order_no(),
            'uuid' => $uuid,
            'userid' => $detailRow->userid,
            'platform' => $plaform,
            'address' => $address->area.' '.$address->address,
            'address_name' => $address->name,
            'address_phone' => $address->phone,
            'product' => json_encode($products),
            'total' => $total,
            'payid' => '0',
            'logisticsid' => '0',
            'status' => 0,
            'type' => $detailRow->type
        );

        Log::info($data);
        $affect = Order::create($data);
        if($affect){
            // 更改清单的状态
           $detail =  Detail::find($detailRow->id);
           $detail -> status = 2; // 已结束
           $detail -> save();

            // 记录日志
            $l = new \App\Models\Log();
            $l->uid = $detailRow->userid;
            $l->pkey = $uuid;
            $l->pval = "已转订单";
            $l->save();
           return ['price'=>$total, 'product'=>$stuStr];
        }
    }


    /**
     * 生成订单id
     */
    public function order_no()
    {
        $order_f = date('ym');
        $order_m = date('dh');
        $order_e = rand(1111,9999);
        return $order_f.' '.$order_m.' '.$order_e;
    }

    /**
     * 过滤掉数组中的元素空数组
     * ['1'=>['id'=>null, ...]]和['1'=>[]...]
     * @param $array
     * @return array
     */
    public function fliterArray($array){

        $tempList = [];

        foreach ( $array as  $key=>$item){
            if (count($item) != 0){
                if ($item['id'] != null){
                    $tempList[] = $item;
                }
            }
        }
        return $tempList;
    }
}


