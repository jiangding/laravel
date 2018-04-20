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
<a class="mem_info_abox" href="/user/info">
    <i class="m_pic"><img src="{{ $currentUser->avatar }}" /></i>
    <p><b>{{ $currentUser->name or $currentUser->nickname }}</b><span>{{ $currentUser->mobile or ''}}</span></p>
    <b class="right_icon"></b>
</a>
<dl class="mlist_box">
    <dt><a href="/feedback"><b>提交反馈</b><span>我们想为你做得更好</span><i class="right_icon"></i></a></dt>
    <dd>
        <a href="/order"><b>我的订单</b><i class="right_icon"></i></a>
        <a href="/address"><b>地址管理</b><i class="right_icon"></i></a>
    </dd>
</dl>
{{--<div class="mday_box">--}}
    {{--<p>你希望提前多少天收到提醒补货？</p>--}}
    {{--<ul class="mday_list clearfix">--}}
        {{--<li><span data="5" @if ($currentUser->remind == 5) class="aon" @endif>5天</span></li>--}}
        {{--<li><span data="7" @if ($currentUser->remind == 7) class="aon" @endif>7天</span></li>--}}
        {{--<li><span data="10" @if ($currentUser->remind == 10) class="aon" @endif>10天</span></li>--}}
        {{--<li><span data="30" @if ($currentUser->remind == 30) class="aon" @endif>30天</span></li>--}}
    {{--</ul>--}}
    {{--<div class="mday_op">--}}
        {{--<input type="button" value="保存" class="abtn_gray_large" id="commit" />--}}
    {{--</div>--}}
{{--</div>--}}
<!--去补货效果提示-->
<div class="layerBy">
    <!--补货成功弹窗start-->
    <div class="alert_box"><p>保存成功</p></div>
    <!--补货成功弹窗end-->
</div>
<script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script>
    $(function () {
        $(".mday_list span").click(function () {
            $(".mday_list span").removeClass("aon");
            $(this).addClass("aon");
        });

        $(document).on('click', '.mday_list li', function() {
            var that = this;
            $.ajax({
                type: 'GET',
                url: '/user/remind',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    remind: $(that).find('span').attr('data')
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
                        $('.layerBy').show();
                        $(".alert_box").show();
                        setTimeout(function () {
                            $('.layerBy').hide();
                            $(".alert_box").hide();
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
