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
<div class="address_box">
    <div class="address_add_b">
        <p>添加新地址</p>
        <a href="/address/create"><span>添加</span><b>新地址</b><i class="right_icon"></i></a>
    </div>
    <div class="address_edit_list">
        <h2>编辑地址</h2>
        <ul class="addr_edit_list">
          @foreach($addresses as $row)
          <li>
                <h2>{{ $row->name }}<span>电话：{{ $row->phone }}</span></h2>
                <p>{{ $row->area }}</p>
                <p>{{ $row->address }}</p>
                @if($row->label)<strong class="addr_tag">{{ $row->label }}</strong>@endif
                <div class="addr_op">
                    <span class="del_icon" id="{{ $row->id }}">删除</span>
                    <a href="/address/update/{{ $row->id }}" class="edit_icon">编辑</a>
                </div>
            </li>
          @endforeach
        </ul>
    </div>
</div>
<!--去补货效果提示-->
<div class="layerBy">
    <!--补货弹窗start-->
    <div class="alert_box" id="alert_box_1">
        <p>确认删除该地址吗？</p>
        <div class="alert_op">
            <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="确认" class="abtn_yellow" /></span>
        </div>
    </div>
    <!--补货弹窗end-->
    <!--补货成功弹窗start-->
    <div class="alert_tips">已提交删除</div>
    <!--补货成功弹窗end-->
    <!--补货提交出错弹窗start-->
    <div class="alert_box" id="alert_box_2">
        <p>提交过程出了点小差错......</p>
        <div class="alert_op">
            <span><input type="button" value="联系客服" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="重新提交" class="abtn_yellow" /></span>
        </div>
    </div>
    <!--补货提交出错弹窗end-->
</div>
<!--去补货效果提示-->
<script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script>
    $(function () {
        var id,that;
        $('.addr_edit_list').on("click", 'span.del_icon', function (e) {
            e.preventDefault();
            $('.layerBy').show();
            $("#alert_box_1").show();
            $(".alert_tips").hide();
            that = $(this);
            id = that.attr("id");
        });

        $('.layerBy').on("click", 'input.abtn_yellow', function (e) {
            $.ajax({
                type: 'POST',
                url: '/address/delete',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
                        that.parent().parent().remove();
                        $('.layerBy').hide();
                        $("#alert_box_1").hide();
                    }

                },
                error: function(xhr, type){

                }
            });
        });
    });
</script>
@include('frontend.Layouts.footer')