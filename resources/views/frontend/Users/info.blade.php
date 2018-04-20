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
    <style type="text/css">
        .mbsc-ios .mbsc-fr-btn-s .mbsc-fr-btn{
            font-weight: 400;
        }
    </style>
</head>

<body>
    <div class="mpic_box">
        <span class="m_pic"><img src="{{ $currentUser->avatar }}" /></span>
        <b>头像</b>
    </div>
    <ul class="mList">
        <li id="register"><span>{{ $currentUser->mobile or '' }}</span>手机号</li>
        <li class="p0">
            <div class="input_li">
                <input type="text" id="age" class="input_age" readonly="readonly" value="{{ $currentUser->birthday }}" />
            </div>
            <b>生日</b>
        </li>
        <li class="p0">
            <div class="input_li">
                <select id="sex-select" class="demo-test-select" data-role="none">
                    @if ( $currentUser->sex == 1 )
                        <option value="1" selected>男</option>
                        <option value="2">女</option>
                    @elseif( $currentUser->sex == 2 )
                        <option value="1">男</option>
                        <option value="2" selected>女</option>
                    @else
                    @endif
                </select>
            </div>
            <b>性别</b>
        </li>
    </ul>
    <script type="text/javascript" src="{{ URL::asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(function () {
        var now = new Date(),
            max = new Date(now.getFullYear() + 100, now.getMonth(), now.getDate());
        $('#age').mobiscroll().date({
            theme: 'ios',
            lang: 'zh',
            max: max,
            defaultValue: new Date(new Date()),
            dateFormat: 'yy-mm-dd',
            onSet: function (event, inst) {
                $.ajax({
                    type: 'POST',
                    url: '/user/profile',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        profile:JSON.stringify({ birthday: event.valueText.replace(/-/g, "") })
                    },
                    dataType: 'json',
                    timeout: 300,
                    context: $('body'),
                    success: function(data){
                        if(data.retcode == 0)
                        {

                        }
                    },
                    error: function(xhr, type){

                    }
                });
            }
        });
        $('.demo-test-select').mobiscroll().select({
                theme: 'ios',
                lang: 'zh',
                onSet: function (event, inst) {
                    $.ajax({
                        type: 'POST',
                        url: '/user/profile',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            profile:JSON.stringify({ sex: (event.valueText=='男'?1:2) })
                        },
                        dataType: 'json',
                        timeout: 300,
                        context: $('body'),
                        success: function(data){
                            if(data.retcode == 0)
                            {

                            }
                        },
                        error: function(xhr, type){

                        }
                    });
                }
        });

        $('#register').click(function(){

            window.location.href="/user/register";

        });
    });
</script>
@include('frontend.Layouts.footer')
