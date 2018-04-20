<?php
/**
 * Created by PhpStorm.
 * User: Aaron
 * Date: 2017/7/10
 * Time: 10:57
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{

    /**
     * 更新三大平台的cookies
     * 调用接口更新cookies
     * @param Request $request
     * @return mixed
     */
    public function updateCookies(Request $request){
        $jdCookies = $request->input('jdCookies', '');
        $yhdCookies = $request->input('yhdCookies', '');
        $tmallCookies = $request->input('tmallCookies', '');
        $url = 'http://www.iganghao.com/logistics/updateCookies';
        $post_data = ['jdCookies'=>$jdCookies, 'yhdCookies'=>$yhdCookies, 'tmallCookies'=>$tmallCookies];
        $result = $this->curl($url, $post_data);
    }


    /**
     * 根据传入的订单号爬取物流信息
     * @param Request $request
     * @return string
     */
    public function spiderLogisticsFromWeb(Request $request){
        $jd = $request->input('jd', array());
        $yhd = $request->input('yhd', array());
        $tmall = $request->input('tmall', array());
        $list = ['jd'=>$jd, 'yhd'=>$yhd, 'tmall'=>$tmall];
        foreach($list as $key=>$item){
            if (count($item) == 0) unset($list[$key]);
        }

        if (count($list) == 0) return '0';

        $url = 'http://www.iganghao.com/logistics/spider';
        $post_data = ['list'=>json_encode($list)];
        $result = $this->curl($url, $post_data);
    }


    /**
     * 通过post传入数组，返回json格式数据
     * @param Request $request
     * @return mixed
     */
    public function getLogisticsInfoByOrderId(Request $request){
        $orderIdList = $request->input('orderIdList', []);
        $url = 'http://www.iganghao.com/logistics/getLogisticInfo';
        $post_data = ['orderIdList'=>json_encode($orderIdList)];
        $result = $this->curl($url, $post_data);
        return $result;
    }



    private function curl($url, $post_data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}