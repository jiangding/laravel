<?php

namespace App\Http\Controllers\Backend;
header("Content-type: text/html; charset=utf-8");
use App\Http\Traits\LogisticsTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Queue;
use Log;
use App\Jobs\Message;
use App\Models\components\cusResponse;
class LogisticsController extends Controller
{

    use LogisticsTrait;

    /**
     * 物流信息
     */
    public function index(Request $request)
    {
        $orderIdList = [];
        //首先从order表中查取logisticId
        $orders = Order::where('logisticsid','!=', 0)->paginate(15);
        foreach($orders as $order){
            //如果有子单号，则用子单号获取,现在只是针对京东，天猫和一号店还没有确定是否有拆单行为  TODO
            if($order->childrenId){
                $childrenIdList = json_decode($order->childrenId);
                foreach ($childrenIdList as $id ){
                    $orderIdList[] = $id;
                }
            } else {
                $orderIdList[] = $order->logisticsid;
            }
        }
        $postData['orderIdList'] = json_encode($orderIdList);
        $url = 'http://www.iganghao.com/logistics/getLogisticInfo';
        $datas = $this->curl($postData, $url);
        foreach ($orders as $key=>$order){
            $childrenIdList = $order->childrenId ? json_decode($order->childrenId) : array();
            $tempList = [];
            foreach ($datas as $data){
                if ($data->orderId == $order->logisticsid){
                    $orders[$key]->logisticsInfo = [$data->orderId=>json_decode($data->infos)];
                }
                //如果是在子订单的，则汇总
                if (in_array($data->orderId, $childrenIdList)){
                    $tempList[$data->orderId] = json_decode($data->infos);
                }
            }
            if (count($childrenIdList) > 1){
                $orders[$key]->logisticsInfo = $tempList;
                $orders[$key]->counts = count($childrenIdList);
            } else {
                $orders[$key]->counts = 1;
            }

            $orders[$key]->childrenId = json_decode($order->childrenId, true);
        }


        return view('backend.logistics', compact(['orders']));
    }


    /**
     * 根据订单Id列表爬取物流信息
     * 数组结构如：[
     *      'jd'=>['1233123','4444'],
     *      'yhd'=>['1231','1231']
     *      'tmall'=>['123123','123']
     * ]
     */
    public function spiderLogisticByAll(){
        $orderIdList = [];
        //首先从order表中查取logisticId
        $orders = Order::where('logisticsid','!=', 0)->get();
        foreach($orders as $order){
            //如果有子单号，则用子单号获取,现在只是针对京东，天猫和一号店还没有确定是否有拆单行为  TODO
            if ($order->childrenId){
                $childrenIdList = json_decode($order->childrenId);
                foreach ($childrenIdList as $childrenId){
                    $orderIdList[strtolower($order->platform)][] = $childrenId;
                }
            } else {
                $orderIdList[strtolower($order->platform)][] = $order->logisticsid;
            }

        }
        $postData['list'] = json_encode($orderIdList);
        $url = 'http://www.iganghao.com/logistics/spider';

        $result = $this->curl($postData, $url);

        return json_encode($result);
    }


    /**
     * 添加子单号
     * @param $logisticsid
     * @param Request $request
     * @return string
     */
    public function addChildrenId($logisticsid, Request $request){
        $childrenIdStr = $request->input('childrenIdStr', '');
        $returnMessage = [];
        $childrenIdList = [];
        if ($childrenIdStr){
            if (strpos($childrenIdStr, ',')){
                $childrenIdList = explode(',', $childrenIdStr);
                foreach($childrenIdList as $key=>$id){
                    $childrenIdList[$key] = trim($id);
                }
            } else{
                $childrenIdList[] = trim($childrenIdStr);
            }

            $childrenIdJson = json_encode($childrenIdList);

            $order = Order::where('logisticsid', $logisticsid)->first();
            $order->childrenId = $childrenIdJson;
            $result = $order->save();
            if ($result){
                $returnMessage['type'] = 1;
                $returnMessage['message'] = "添加成功";
            } else {
                $returnMessage['type'] = 0;
                $returnMessage['message'] = "添加失败，请联系攻城狮";
            }
        } else {
            $returnMessage['type'] = 0;
            $returnMessage['message'] = "添加失败，木有输入任何信息啊";
        }

        return json_encode($returnMessage);

    }


    public function updateCookies(Request $request){
        $jdCookies = $request->input('jd');
        $yhdCookies = $request->input('yhd');
        $tmallCookies = $request->input('tmall');

        $url = 'http://www.iganghao.com/logistics/updateCookies';
        $postData = ['jdCookies'=>$jdCookies, 'yhdCookies'=>$yhdCookies, 'tmallCookies'=>$tmallCookies];
        $result = $this->curl($postData, $url);


        return json_encode($result);
    }

}
