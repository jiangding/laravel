<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>再次比价</title>
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="msapplication-TileColor" content="#090a0a">
    <link rel="stylesheet" href="{{ URL::asset('css/css_style.css') }}">
    <script src="{{URL::asset('js/jquery.min.js')}}"></script>
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

        });

        function verify() {
            var arr = new Array();
            $(".productBarcode").each(function(){
                arr.push($(this).attr('data-id'));
            });

            $.ajax({
                type: 'POST',
                url: '{{ url('detail/create') }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    barcode: JSON.stringify(arr)
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
                        $(".layerBy").show();
                        $("#alert_box_3").show();

                    }else if(data.retcode == 2){
                        var uurl = "{{ url('detail/price/') }}/"+data.uuid+" ";
                        console.log(uurl);
                        location.href= uurl;
                    }else{

                    }
                },
                error: function(xhr, type){
                    console.log(xhr);
                    console.log(type);
                }
            });
        }
    </script>
</head>
<body>
<div class="detail_fail_info">
    <div class="detail_text">点击"重新比价"，<br />
        几分钟后查收最近比价结果
    </div>

    <div class="detail_divide"></div>
    <div class="detail_product">
        @foreach($detail->products['JD'] as $k => $v)
            <div class="detailparity_txt">
                <ul>
                    <li style="list-style: disc">
                        @if($v->name != '')
                            <span>{{ $v->name }}</span>
                        @else
                            <span>{{ $v->barcode }}</span>
                        @endif
                        <input type="hidden" class='productBarcode' data-id="{{$v->barcode}}">
                    </li>
                </ul>
            </div>
        @endforeach
    </div>

</div>
<div class="c_btn_box" style="padding: 1rem 30% 0 30%;">
    <input id="goParity" type="button" value="重新比价" class="abtn_gray_large" onclick="verify();">
</div>


<!--去补货效果提示-->
<div class="layerBy">
    <!--补货成功弹窗start-->
    <div class="alert_box" id="alert_box_3">
        <p>比价结果稍后在微信对话窗返回，先忙别的吧</p>
        <div class="alert_op">
            <span><input type="button" value="好的" class="abtn_yellow" onclick="WeixinJSBridge.call('closeWindow');"/></span>
        </div>
    </div>
    <!--补货提交出错弹窗end-->
</div>
<!--去补货效果提示-->

</body>
</html>