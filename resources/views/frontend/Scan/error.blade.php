<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>正在查询条码</title>
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
    <link rel="stylesheet" href="{{ URL::asset('css/css_style.css') }}">
    <script src="{{URL::asset('js/jquery.min.js')}}"></script>
    <script language="JavaScript" type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?46c92c2addc1f87804bb84524ac9e3a3";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('body').css('background-image', 'linear-gradient(-180deg, #FBEA73 0%, #F4D03F 100%)');

            //点击返回上一页
            $("#goBack").on('touchstart', function(){
                var number = {{$number}};
               if (number == 1){
                   WeixinJSBridge.call('closeWindow');
               } else if (number == 2){
                    window.history.back();
               } else if (number == 3){
                   location.href = '/shopping/shoppingCart/1';
               } else {
                   location.href = '/stock';
               }

            });


            //继续扫
            wx.config({
                debug: false,
                appId: '{{ $appId }}',
                timestamp: '{{ $timestamp }}',
                nonceStr: '{{ $nonceStr }}',
                signature: '{{ $signature }}',
                jsApiList: {!! $jsApiList !!},
            });
            wx.ready(function () {
                $('#scan').on('touchstart', function(){
                    wx.scanQRCode({
                        needResult: 1,
                        scanType: ["barCode"],
                        success: function (res) {
                            var number = '{{$number}}';
                            if (number == '3') {
                                location.href = '/shopping/shoppingCart/' + res.resultStr.split(",")[1];
                            } else {
                                location.href = '/stock/barcode/2/' + res.resultStr.split(",")[1];
                            }

                        }
                        });
                });
            });
        });

    </script>
</head>
<body>
<div class="fail_box">
    <div class="match_num text-center">
        <h3>正在为你查询条码</h3>

        <h3>{{$barcode}}</h3>
        <div class="fail-divide"></div>
        <span style="font-weight: bold; font-size: 1.5rem; opacity: 0.8">几分钟后在微信对话框返回相关信息<br />别等着，先忙别的吧</span>
    </div>
</div>
<div class="fail_button_box">
    <span id="goBack"><a href="javascript:void(0);">返回上一页</a></span>
    <span id="scan"><a href="javascript:void(0);">继续扫</a></span>
</div>
</body>
</html>
