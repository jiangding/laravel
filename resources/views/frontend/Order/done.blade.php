<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>订单详情</title>
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">
    <!-- No Baidu Siteapp-->
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <!--<meta name="format-detection" content="telephone = no" />-->
    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="订单详情" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-TileColor" content="#090a0a">
    <link rel="stylesheet" href="{{ URL::asset('css/css_style.css')}}">
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?46c92c2addc1f87804bb84524ac9e3a3";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>
<body>
<div class="order_header">
    <i class="left_icon"></i>
    <span><a href="/order" style="color: #5c5c5c;">我的订单</a> </span>
    <b>
        @if( $order->status == 0)
            未付款
        @elseif($order->status == 1)
            已支付
        @elseif($order->status == 6)
            已下单
        @elseif($order->status == 2)
            已取消
        @elseif($order->status == 3)
            退款申请
        @elseif($order->status == 4)
            退款中
        @elseif($order->status == 5)
            已退款
        @endif
    </b>
</div>
<div class="order_infobox">
    <ul class="o_list">
        <li><span>发货方：</span><p>
                @if( $order->platform == 'TMALL' )
                    天猫超市(自营)
                @elseif( $order->platform == 'JD' )
                    京东自营
                @else
                    一号店自营
                @endif
            </p></li>
        <li><span>订单号：</span><p>{{ $order->orderid }}</p></li>
        <li><span>订单时间：</span><p>{{ $order->created_at }}</p></li>
        @if($order->status != 2)
        <li><span>支付时间：</span><p>{{ $order->updated_at }}</p></li>
        <li><span>支付金额：</span><p>￥{{ $order->total }}</p></li>
        @else
        <li><span>订单金额：</span><p>￥{{ $order->total }}</p></li>
        @endif
    </ul>
    <ul class="o_list">
            <li><span>收货人：</span><p>{{ $order->address_name }}</p></li>
            <li><span>电话：</span><p>{{ $order->address_phone }}</p></li>
            <li><span>收货地址：</span><p>{{ $order->address }}</p></li>
    </ul>
    {{--<div class="o_invoice">--}}
        {{--<p>发票类型：{{ $order->invoice_type }}</p>--}}
        {{--<p>发票内容：{{ $order->invoice_content }}</p>--}}
        {{--<p>发票抬头：{{ $order->invoice_name }}</p>--}}
    {{--</div>--}}
</div>
<div class="goods_box">
    <div class="goods_header">订单商品</div>
    <ul class="goods_list">
        {{-- */$arrPrice = 0/*  --}}
        @foreach($products as $product)
            {{-- */$arrPrice +=  $product['price']*$product['num'] /* --}}
            {{-- */ $t =  bcmul($product['price'],$product['num'],1) /* --}}
            <li>
                <h2>{{ $product['name'] }}</h2>
                @if($t == 0)
                    <span style="font-size: 1.3rem;">缺货</span>
                @else
                    <span>￥{{ $t }}</span>
                    <b>x{{ $product['num'] }}</b>
                @endif
            </li>
        @endforeach

    </ul>
    <ul class="goods_list">
        <li><font>商品</font> <span>￥{{ $arrPrice }}</span> </li>
        <li><font>运费</font> <span>￥{{ bcsub($order->total,$arrPrice,1)}}</li>
        <li><font>合计</font> <span>￥{{ $order->total }} </li>
    </ul>

    <div class="goods_header">物流详情</div>
    @if (count($order->logisticsInfo) > 0)
        @foreach($order->logisticsInfo as $key=>$infos)
            <ul class="goods_list">
                @if (count($order->childrenId) > 0)
                    <li><font>主单号</font> <span>{{ $order->logisticsid }}</span> </li>
                    <li><font>子单号</font> <span>{{ $key }}</span> </li>
                @else
                    <li><font>单号</font> <span>{{ $key }}</span> </li>
                @endif

                @foreach($infos as $info)
                    <span class='logisticInfos'>{{$info[0]->time}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$info[0]->info_mesg}}</span>
                @endforeach
            </ul>
        @endforeach
    @else
        <ul class="goods_list text-center">
            <span style="font-size: 1.3rem">暂时还没有物流信息</span>
        </ul>
    @endif
</div>
@if($order->status == 1 || $order->status == 6 )
    <div class="ch_op" style="margin-bottom:25px;">
        <input id="return" type="button" value="申请退款" class="abtn_gray_large">
    </div>

@endif
{{--<div class="goods_op"><b><a href="/pay/reward/{{ $order->orderid }}" class="abtn_yellow_large">打 赏</a></b></div>--}}
<div class="layerBy">
    <!--补货弹窗start-->
    <div class="alert_box" id="alert_box_1">
        <p>确认退款吗？</p>
        <div class="alert_op">
            <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="确认" class="abtn_gray" onclick="verify('{{$order->orderid}}');" /></span>
        </div>
    </div>
    <!--补货弹窗end-->

    <!--补货提交出错弹窗start-->
    <div class="alert_box" id="alert_box_2">
        <p>你的退款申请已提交<br />稍后客服将会联系你</p>
        <div class="alert_op">
            <span><input type="button" value="知道了" class="abtn_gray" onclick="finishx()" /></span>
        </div>
    </div>
</div>
<script type="text/javascript" src="{{ URL::asset('plugins/jQuery/jquery-1.11.1.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        //点击申请退款
        $("#return").on('touchstart', function(){
            $(".layerBy").show();
            $("#alert_box_1").show();
        });
    });

    function verify(){
        $("#alert_box_2").show();
        $("#alert_box_1").hide();
        $.ajax({
            type: 'POST',
            url: '{{ url('order/applyRefund') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                openid : '{{ $currentUser['openid'] }}',
                message: '收到 {{ $currentUser['nickname'] }} 订单({{$order->orderid}})退款申请。',
                orderid: '{{ $order->orderid }}'
            },
            // type of data we are expecting in return:
            dataType: 'json',
            timeout: 300,
            context: $('body'),
            success: function(data){
                window.location.reload();
            },
            error: function(xhr, type){

            }
        });

    }

    function finishx()
    {
        $.ajax({
            type: 'POST',
            url: '{{ url('stock/sendMessage') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                openid : '{{ $currentUser['openid'] }}',
                message: '{{ $currentUser['nickname'] }}，你的退款申请已收到，我们会尽快联系你。'
            },
            // type of data we are expecting in return:
            dataType: 'json',
            timeout: 300,
            context: $('body'),
            success: function(data){
                window.location.reload();
                $('.layerBy').hide();
            },
            error: function(xhr, type){

            }
        });
    }
</script>
</body>
</html>

