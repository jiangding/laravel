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
    <meta name="apple-mobile-web-app-title" content="{{ $title }}" />
    <meta name="msapplication-TileColor" content="#090a0a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
<!--去补货效果提示-->
<div class="layerBy">
    <!--补货弹窗start-->
    <div class="alert_box" id="alert_box_1">
        <p>确认取消订单吗？</p>
        <div class="alert_op">
            <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="确定" class="abtn_yellow" onclick="toCancel({{ $order->id }})" /></span>
        </div>
    </div>
</div>
<div class="order_header">
    <i class="left_icon"></i>
    <span><a href="/order" style="color: #5c5c5c;">我的订单</a></span>
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
                    天猫超市自营
                @elseif( $order->platform == 'JD' )
                    京东自营
                @else
                    一号店自营
                @endif
            </p></li>
        <li><span>订单号：</span><p>{{ $order->orderid }}</p></li>
        <li><span>订单时间：</span><p>{{ $order->created_at }}</p></li>
        <li><span>订单金额：</span><p>￥{{ $order->total }}</p></li>
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
        @foreach($products as $product)
            <li>
                <h2>{{ $product['name'] }}</h2>
                @if($product['price']*$product['num'] == 0)
                    <span style="font-size: 1.3rem;">缺货</span>
                @else
                    <span>￥{{ $product['price']*$product['num']}}</span>
                    <b>x{{ $product['num'] }}</b>
                @endif
            </li>
        @endforeach
    </ul>
</div>
<div class="goods_op">
    <span><button onclick="cancelOrder()" class="abtn_gray_large">取消订单</button></span>
    <span><button id="submit" class="abtn_yellow_large">支付</button></span>
</div>
<script>
    // 弹框
    function cancelOrder() {
        $('.layerBy').show();
        $("#alert_box_1").show();
    }

    function toCancel(oid){
        $.ajax({
            type: 'POST',
            url: '/order/cancelOrder',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: oid
            },
            dataType: 'json',
            timeout: 3000,
            context: $('body'),
            success: function(data){
                location.reload();

            },
            error: function(xhr, type){

            }
        });
    }
</script>
</body>
</html>
{{--支付--}}
<script src="{{ URL::asset('plugins/jQuery/jquery-1.11.1.min.js') }}"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: {{ $debug }},
        appId: '{{ $appId }}',
        timestamp: '{{ $timestamp }}',
        nonceStr: '{{ $nonceStr }}',
        signature: '{{ $signature }}',
        url: '{{ $url }}',
        jsApiList: {!! $jsApiList !!}
    });
    wx.ready(function() { (function() {
        $("#submit").click(function () {
            $.ajax({
                type: 'POST',
                url: '/pay/unpay',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: {{ $order->id }}
                },
                dataType: 'json',
                timeout: 3000,
                context: $('body'),
                success: function(data){
                    if(data)
                    {
                        wx.chooseWXPay({
                            timestamp: data.timestamp,
                            nonceStr: data.nonceStr,
                            package: data.package,
                            signType: data.signType,
                            paySign: data.paySign,
                            success: function (res) {
                                location.reload();
                            }
                        });
                    }
                    else
                    {

                    }
                },
                error: function(xhr, type){

                }
            });
        });
    })();
        wx.error(function(res) {
            console.debug(res);
        });
    });
</script>