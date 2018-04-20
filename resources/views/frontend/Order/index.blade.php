<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>我的订单</title>
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
    <meta name="apple-mobile-web-app-title" content="我的订单" />
    <meta name="msapplication-TileColor" content="#090a0a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ URL::asset('css/css_style.css') }}">
    <script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
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
<div class="order_tab clearfix">
    <span><a href="{{ url('order/index') }}?nopay=1" @if($navStatus == 'unpay') class="aon" @endif data="unpaid">未付款</a></span>
    <span><a href="{{ url('order/index') }}" @if($navStatus == 'all') class="aon" @endif data="all">全部订单</a></span>
</div>
<ul class="order_list">
    @if(count($orders) > 0)
        @foreach($orders as $order)
            <li @if($order->pay_id == 0)data="unpaid"@endif>
                <a href="/pay/order/{{ $order->id }}">
                    <i class="shop_icon">
                        @if($order->platform == 'TMALL')
                            天猫
                        @elseif($order->platform == 'JD')
                            京东
                        @else
                            一号店
                        @endif
                    </i>
                    <p>
                        {{-- */$arrProducts = $order->product;/* --}}
                        {{-- */$productCount = count($arrProducts) /* --}}
                        @if($productCount > 1)
                            <b class="price">{{ $arrProducts[0]['name'] }}等{{ $productCount }}件商品</b>
                        @else
                            <b class="price">{{ $arrProducts[0]['name'] }}</b>
                        @endif

                        <span class="gm">总价:{{ $order->total }} 元</span>
                        <span class="time">{{ $order->created_at}}</span>
                    </p>
                    @if( $order->status == 0)
                        <span class="unpaid_span">未付款</span>
                    @elseif($order->status == 1)
                        <span class="Inbound_span">已支付</span>
                    @elseif($order->status == 6)
                        <span class="Inbound_span">已下单</span>
                    @elseif($order->status == 2)
                        <span class="Inbound_span">已取消</span>
                    @elseif($order->status == 3)
                        <span class="Inbound_span">退款申请</span>
                    @elseif($order->status == 4)
                        <span class="Inbound_span">退款中</span>
                    @elseif($order->status == 5)
                        <span class="Inbound_span">已退款</span>
                    @endif
                </a>
                {{--@if($order->status == 2)--}}
                    {{--<a href="Inquire.html" class="Inquire_purple">查询物流</a>--}}
                {{--@endif--}}
            </li>
        @endforeach
    @else
        <div style="text-align:center; padding-top:20px">暂时没有订单</div>
    @endif
</ul>
</body>
</html>
<script type="text/javascript">
    $(".order_tab a").click(function () {
        $(".order_tab a").removeClass("aon");
        $(this).addClass("aon");
        var data = $(this).attr("data");
        if (data == "unpaid") {
            $(".order_list li").hide();
            $(".order_list li[data='" + data + "']").show();
        }
        else {
            $(".order_list li").show();
        }
    });
</script>