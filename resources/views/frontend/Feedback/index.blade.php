<!DOCTYPE html>
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
<div class="feedback_box">
    <h2>意见反馈</h2>
    <div class="feed_box">
        <textarea id="feedback" name="feedback" rows="2" cols="20" class="textarea_txt" ></textarea>
    </div>
    <div  id="Button1" class="order_addr order_addr_button">
        <p align="center">提交反馈</p>
    </div>
    {{--<input type="button" value="goBack" onclick="WeixinJSBridge.call('closeWindow');" />--}}
</div>
<!--去补货效果提示-->
<div class="layerBy">
    <!--补货成功弹窗start-->
    <div class="alert_box"><p>已成功提交，感谢您的反馈</p></div>
    <!--补货成功弹窗end-->
</div>
<!--去补货效果提示-->
<script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script>
    $(function () {
        $('#Button1').click(function(){
            $.ajax({
                type: 'POST',
                url: '/feedback',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    userid: {{ $currentUser->id }},
                    type: 'USERFEEDBACK',
                    value: $("#feedback").val()
                },
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
                        $('.layerBy').show();
                        $(".alert_box").show();
                        setTimeout(function () {
                            location.href = '/user/index';
                        },500);
                    }
                },
                error: function(xhr, type){

                }
            });
        });
    });
</script>
@include('frontend.Layouts.footer')
