<?php

namespace App\Http\Traits;
use Log;


trait SpiderTrait
{
    public function spiders($jd_url, $yhd_url, $tmall_url)
    {
        set_time_limit(0);
        date_default_timezone_set("Asia/shanghai");
        header("Content-type:text/html;charset=UTF-8");

        $arrPrice = array();

        // 随机ip
        $ip = $this->get_rand_ip();

        if($jd_url){
            // 价格
            @$jdsku = $jd_url;
            $jdsku =  preg_replace('/\D/s', '', $jdsku);
            $jd = "http://p.3.cn/prices/mgets?skuIds=J_".$jdsku;
            $ch = curl_init();
            $c_url = $jd;
            curl_setopt($ch, CURLOPT_URL,$c_url);
            //curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if(!empty($ip)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $ip, 'CLIENT-IP:' . $ip));  //构造IP
            }
            curl_setopt($ch, CURLOPT_REFERER, "http://item.jd.com"); //构造来路
            $jdResult = curl_exec($ch);
            Log::info($jdResult);
            $arr = json_decode($jdResult, true);
            $arrPrice['jd'] = @(string)current($arr)['p'];
            curl_close ($ch);
            unset($ch);
        }else{
            $arrPrice['jd'] = '0';
        }


        if($yhd_url){
            // 1号店
            @$oneSku = $yhd_url;
            if(strpos($oneSku, '_') !== False){
                $oneArs = explode('_', $oneSku);
                $oneSku = $oneArs[1];
            }
            $oneArr = explode('?', $oneSku);
            $oneSku =  preg_replace('/\D/s', '', $oneArr[0]);

            $one = "http://gps.yhd.com/restful/detail?mcsite=1&provinceId=1&cityId=1&countyId=3&pmId=".$oneSku;
            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL,$one);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            if(!empty($ip)){
                curl_setopt($ch2, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $ip, 'CLIENT-IP:' . $ip));  //构造IP
            }
            curl_setopt($ch2, CURLOPT_REFERER, "http://item.yhd.com"); //构造来路
            $result2 = curl_exec($ch2);

            curl_close ($ch2);
            unset($ch2);
            $arr2 = json_decode($result2, true);
            $arrPrice['yhd'] = (string)$arr2['currentPrice'];
        }else{
            $arrPrice['yhd'] = '0';
        }

        // 天猫
        if($tmall_url){
            @$cat = $tmall_url;
            $price = $this->price($cat, $ip);
            $arrPrice['tmall'] = (string)$price;
        }else{
            $arrPrice['tmall'] = '0';
        }

        return json_encode($arrPrice);

    }

    public function price($url, $ip = "127.0.0.1"){

        preg_match("/id=(\d+)/i", $url, $id_arr);
        if(!empty($id_arr)){
            $id = $id_arr[1];
            $item_url='https://mdskip.taobao.com/core/initItemDetail.htm?itemId=' . $id;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $item_url);
            //设置来源链接，这里是商品详情页链接
            curl_setopt($ch,CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
            curl_setopt($ch,CURLOPT_HEADER,0);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if(!empty($ip)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $ip, 'CLIENT-IP:' . $ip));  //构造IP
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $result = curl_exec($ch);
            //Log::info($result);
            $info = curl_getinfo($ch);

            curl_close($ch);
            //去除回车、空格等
            $result=str_replace(array("\r\n","\n","\r","\t",chr(9),chr(13)),'',$result);
            //将json数据中，以纯数字为key的字段加上双引号，例如28523678201:{"areaSold":1}转为："28523678201":{"areaSold":1}，否则json_decode会出现错误
            $mode="#([0-9]+)\:#m";
            preg_match_all($mode,$result,$s);
            $s=$s[1];
            if(count($s)>0){
                foreach($s as $v){
                    $result=str_replace($v.':','"'.$v.'":',$result);
                }
            }

            //将字符编码转为utf-8，并且将中文转译，否则json_decode会出现错误
            $result=iconv('gbk','utf-8',$result);

            $str=array();
            $mode='/([\x80-\xff]*)/i';
            if(preg_match_all($mode,$result,$s)){
                foreach($s[0] as $v){
                    if(!empty($v)){
                        $str[base64_encode($v)]=$v;
                        $result=str_replace('"'.$v.'"','"'.base64_encode($v).'"',$result);
                    }
                }
            }

            preg_match_all('/({"areaSold).*?(sortOrder":)/is',$result,$sss);

            // 拼凑json
            $restr = @$sss[0][0].'0}';

            $restr=json_decode($restr,true);

            if(isset($restr['promotionList'])){
                foreach ($restr['promotionList'] as $k => $v) {
                    if(isset($v['amountRestriction'])){
                        $price = $v['price'];
                    }
                }
            }else{
                $price = $restr['price'];
            }

            return $price;

        }else{
            return 0;
        }
    }

    // 随机ip
    public function get_rand_ip(){
        $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
        $randarr= mt_rand(0,count($arr_1));
        $ip1id = @$arr_1[$randarr];
        $ip2id=  round(rand(600000,  2550000)  /  10000);
        $ip3id=  round(rand(600000,  2550000)  /  10000);
        $ip4id=  round(rand(600000,  2550000)  /  10000);
        return  $ip1id . "." . $ip2id . "." . $ip3id . "." . $ip4id;
    }

}