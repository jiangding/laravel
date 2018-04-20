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
    <link rel="stylesheet" href="{{ URL::asset('css/css_style.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/weui.min.css') }}">
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
    <div class="alert_box" id="alert_box_loading"><p>保存中...</p></div>

    <div class="alert_box" id="notNull">
        <p>请填写上述产品相关信息</p>
    </div>
</div>
<div class="prowrap">
    <div class="proinfo">
        <div class="bottle_pic"><img src="{{ $product->category->icon }}" /></div>
        <h2>{{ $product->name }}</h2>
        <p>条形码：{{$product->barcode}}</p>
    </div>
    <div class="proset_box">
        <div class="proset_header">
            <p>扫码这件大概剩多少？</p>
        </div>
        <div class="proset_info">
            <div class="weui-slider">
                <div id="sliderInner" class="weui-slider__inner">
                    <div id="sliderTrack" style="width: 0%;" class="weui-slider__track"></div>
                    <div id="sliderHandler" style="left: 0%;" class="weui-slider__handler"><div class="div-slider_handler_inner"></div></div>
                    <div class="notice_word" id="notice_word"><span id="sliderValue" >0</span>%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="proset_box">
        <div class="proset_header">
            <p>全新的一件大概能用多久？</p>
        </div>
        <div>
            <input type="tel" class="commonBox" data_unit="month"  id="cyclesTime" placeholder="0" onfocus="this.placeholder=''" onblur="this.placeholder='0'">
            <lable class="unitLable cyclesTime onfocus cyclesTime_left" data-value="day">天</lable>
            <lable class="unitLable cyclesTime cyclesTime_center" data-value="week">周</lable>
            <lable class="unitLable cyclesTime cyclesTime_right" data-value="month">月</lable>
        </div>

    </div>
    <div class="proset_box">
        <div class="proset_header">
            <p>除了扫码这件，还有几件全新的没用过？</p>
        </div>
        <div>
            <input type="tel" class="commonBox" data_unit="month"  id="number" placeholder="0" onfocus="this.placeholder=''" onblur="this.placeholder='0'">
            <lable class="unitLable transparent_background black_text">件</lable>
        </div>
    </div>
    <div class="pro_op">
        <p><input id="savescan" type="button" value="保存，扫下一个" class="abtn_gray_large  w48" /></p>
        <p><input id="save" type="button" value="保存，查看存货" class="abtn_gray_large w48" /></p>
    </div>
</div>
</body>
</html>
<script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/swiper.min.js') }}"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: false,
        appId: '{{ $appId }}',
        timestamp: '{{ $timestamp }}',
        nonceStr: '{{ $nonceStr }}',
        signature: '{{ $signature }}',
        jsApiList: ["scanQRCode"],
    });
</script>
<script type="text/javascript">
    //滑动杆
    function sliderAction(){

        var $sliderTrack = $('#sliderTrack'),
                $sliderHandler = $('#sliderHandler'),
                $sliderValue = $('#sliderValue'),
                $noticeWord = $('#notice_word');

        var totalLen = $('#sliderInner').width(),
                startLeft = 0,
                startX = 0;
        $sliderHandler.
                on('touchstart', function (e) {
                    startLeft = parseInt($sliderHandler.css('left')) * totalLen / 100;
                    startX = e.changedTouches[0].clientX;
                })
                .on('touchmove', function(e){
                    var dist = startLeft + e.changedTouches[0].clientX - startX;
                    dist = dist < 0 ? 0 : dist > totalLen ? totalLen : dist;
                    var percent =  parseInt(dist / totalLen * 100);
                    var divisor = parseInt(percent/10);
                    var remainder = percent%10;
                    if (remainder != 0){
                        percent = (divisor + 1)*10;
                    }
                    $noticeWord.css('left', percent + '%');
                    $sliderTrack.css('width', percent + '%');
                    $sliderHandler.css('left', percent + '%');
                    $sliderValue.text(percent);

                    e.preventDefault();
                });
    }

    $(document).ready(function () {
        sliderAction();//滑动杆

        $("#cyclesTime").click(function(){
            $(this).val('');
        });

        $("#number").click(function(){
            $(this).val('');
        });

        //点击天周月单位
        $(".cyclesTime").click(function(){
            $(".cyclesTime").removeClass('onfocus');
            $(this).addClass('onfocus');
        });
        $('#save').click(function(){
            var cycleUnitParams = {
                'day':1,
                'week':7,
                'month':31
            };
            var cycleUnit = $(".onfocus").attr('data-value');
            var cycle = $('#cyclesTime').val()*cycleUnitParams[cycleUnit];
            var quantity = $("#number").val();
            var last = $("#sliderValue").text();

            // 如果都为0
            if(cycle == 0 && quantity == 0 && last == 0){
                $('.layerBy').show();
                $("#notNull").show();
                $('#alert_box_loading').hide();
                setTimeout(function () {
                    $('.layerBy').hide();
                    $("#notNull").hide();
                },2000);
                return ;
            }
            $.ajax({
                type: 'POST',
                url: '/stock/create/{{$product->barcode}}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    quantity: quantity,
                    cycle: cycle,
                    last: last/100,
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                beforeSend: function(){
                    $('.layerBy').show();
                    $('#alert_box_loading').show();
                    $("#notNull").hide();
                },
                success: function(data){
                    if(data.retcode == 0)
                    {
                        location.href = '/stock';
                    }
                },
                error: function(xhr, type){
                    $('.layerBy').hide();
                    $('#alert_box_loading').hide();
                }
            });
        });
        $('#savescan').click(function(){
            var cycleUnitParams = {
                'day':1,
                'week':7,
                'month':31
            };
            var cycleUnit = $(".onfocus").attr('data-value');
            var cycle = $('#cyclesTime').val()*cycleUnitParams[cycleUnit];
            var quantity = $("#number").val();
            var last = $("#sliderValue").text();

            // 如果都为0
            if(cycle == 0 && quantity == 0 && last == 0){
                $('.layerBy').show();
                $('#alert_box_loading').hide();
                $("#notNull").show();
                setTimeout(function () {
                    $('.layerBy').hide();
                    $("#notNull").hide();
                },2000);
                return ;
            }
            $.ajax({
                type: 'POST',
                url: '/stock/create/{{$product->barcode}}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    quantity: quantity,
                    cycle: cycle,
                    last: last/100,
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                beforeSend: function(){
                    $('.layerBy').show();
                    $("#notNull").hide();
                    $('#alert_box_loading').show();
                },
                success: function(data){
                    $('.layerBy').hide();
                    $('#alert_box_loading').hide();
                    if(data.retcode == 0)
                    {
                        wx.ready(function () {
                                wx.scanQRCode({
                                    needResult: 1,
                                    scanType: ["barCode"],
                                    desc: 'scanQRCode desc',
                                    success: function (res) {
                                        location.href = '/stock/barcode/4/'+res.resultStr.split(",")[1];
                                    }
                                });
                        });
                    }
                },
                error: function(xhr, type){
                    $('.layerBy').hide();
                    $('#alert_box_loading').hidden();
                }
            });
        });
    });
</script>
