<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ $title }}</title>
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
    <link rel="stylesheet" href="{{ URL::asset('plugins/mobiscoll/mobiscroll.jquery.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/css_style.css') }}">
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
    <span><a href="/order/{{ $uuid }}" style="color: #5c5c5c;">订单详情</a></span>
</div>
<div class="reward_box">
    <ul class="reward_price clearfix">
        <li class="text-left"><span data="5" class="aon">5元</span></li>
        <li class="text-center"><span data="10">10元</span></li>
        <li class="text-right"><span data="15">15元</span></li>
    </ul>
    <div class="reward_price_box">
        <span>随意：</span>
        <input id="price" type="text" class="input_full" value="" />
        <b>元</b>
    </div>
</div>
<div class="goods_op"><b><a href="#" class="abtn_yellow_large" id="submit">打 赏</a></b></div>
</body>
</html>
<script src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.jquery.min.js') }}" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    var decimal = '.',
        thousands = ',',
        numpad = mobiscroll.numpad('#price', {
        theme: 'mobiscroll',
        lang: 'zh',
        display: 'bottom',
        min: 0.01,
        max: 99999999,
        allowLeadingZero:false,
        preset: 'decimal',
        decimalSeparator:'.',
        prefix: '￥',
    });
    $(".reward_price span").click(function () {
        $(".reward_price span").removeClass("aon");
        $(this).addClass("aon");
        $("#price").val($(this).attr('data'));
        numpad.setVal($(this).attr('data'));
    });
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
            if($('#price').val() > 0)
            {
                $.ajax({
                    type: 'POST',
                    url: '/pay/reward/{{ $uuid }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        price: $('#price').val()
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
                                    console.debug(res);
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
            }

        });
    })();
        wx.error(function(res) {
            console.debug(res);
        });
    });
</script>

