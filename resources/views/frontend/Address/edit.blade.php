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
    <link rel="stylesheet" href="{{ URL::asset('css/mobiscroll.scroller.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/mobiscroll.scroller.android-ics.css') }}">
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
<div class="layerBy">
    <div class="alert_box" id="alert_box_loading"><p>加载中...</p></div>
</div>
<div class="address_box">
    <div class="city_select_box">
        <h2>{{ $title }}</h2>
        <div class="select_input_box">
            <input id="area" placeholder="请选择所在地区" areaid="{{ $address->areaid }}" readonly="readonly" class="area_input" value="{{ $address->area }}">
        </div>
    </div>
    <div class="addr_form_box">
        <div class="addr_input_box"><input id="name" name="name" type="text" placeholder="姓名" class="addr_input" value="{{ $address->name }}"/></div>
        <div class="addr_input_box"><input id="phone" name="phone" type="text" placeholder="联系电话" class="addr_input" value="{{ $address->phone }}"/></div>
        <div class="addr_input_box"><input id="zip" name="zip" type="text" placeholder="邮编" class="addr_input" value="{{ $address->zip }}" /></div>
        <div class="addr_input_box"><textarea id="address" name="address" rows="2" cols="20" placeholder="详细地址" class="addr_textarea">{{ $address->address }}</textarea></div>
    </div>
    <div class="addr_tag_box">
        <h3><input id="label" name="label" type="text" placeholder="地址标签" class="tag_input" value="{{ $address->label }}"/></h3>
        <p class="tag_span">
            <span @if($address->label == '家') class="aon" @endif>家</span>
            <span @if($address->label == '公司') class="aon" @endif>公司</span>
            <span @if($address->label == '父母') class="aon" @endif>父母</span>
            <span @if($address->label == '速递易') class="aon" @endif>速递易</span>
        </p>
    </div>
</div>
<div class="goods_op"><input id="Button1" type="button" value="保存地址" class="Verify_btn" />
</div>
<script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.zepto.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.core.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.scroller.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.area.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.scroller.android-ics.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/i18n/mobiscroll.i18n.zh.js') }}"></script>
<script type="text/javascript">

    $(function () {
        $('#area').scroller('destroy').scroller({ preset: 'area', theme: 'android-ics light', display: 'bottom', valueo: $("#area").attr("areaid") });
        $(".tag_span span").click(function () {
            $(".tag_span span").removeClass("aon");
            $(this).addClass("aon");
            $("#label").val($(this).text());
        });
        $('#Button1').click(function(){
            var area = $("#area").val();
            if(area == ''){
                $("#area").focus();
                $("#area").attr('placeholder',"地区不能为空");
                return ;
            }
            var name = $("#name").val();
            if(name == ''){
                $("#name").focus();
                $("#name").attr('placeholder',"姓名不能为空");
                return ;
            }
            var phone = $("#phone").val();
            if(phone == ''){
                $("#phone").focus();
                $("#phone").attr('placeholder',"手机号不能为空");
                return ;
            }
            var address = $("#address").val();
            if(address == ''){
                $("#address").focus();
                $("#address").attr('placeholder',"地址不能为空");
                return ;
            }

            $.ajax({
                type: 'GET',
                url: '/address/update/{{ $id }}',
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                data: {
                    userid: {{ $currentUser->id }},
                    name: $("#name").val(),
                    phone: $("#phone").val(),
                    zip: $("#zip").val(),
                    address: $("#address").val(),
                    label: $("#label").val(),
                    area: $("#area").val(),
                    areaid: $("#area").attr("areaid"),
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                beforeSend: function(){
                    $('.layerBy').show();
                    $('#alert_box_loading').show();
                },
                success: function(data){
                    if(data.retcode == 0)
                    {
                        location.href = '/address';
                    }
                },
                error: function(xhr, type){

                }
            });
        });

    });
</script>

@include('frontend.Layouts.footer')