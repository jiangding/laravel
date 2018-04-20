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
<!--去补货效果提示-->
<div class="layerBy">
    <!--补货弹窗start-->
    <div class="alert_box" id="alert_box_1">
        <p>确认删除该商品吗？</p>
        <div class="alert_op">
            <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="确认" class="abtn_yellow"  id="del"  /></span>
        </div>
    </div>

    <div class="alert_box" id="alert_box_2">
        <p>你的修改已保存</p>
    </div>

    <div class="alert_box" id="notNull">
        <p>请填写上述产品相关信息</p>
    </div>
</div>
<div class="prowrap">
    <div class="proinfo">
        <div class="bottle_pic"><img src="{{ URL::asset($stock->catalog->icon) }}" /></div>
        <h2>{{ $stock->product->name }}</h2>
        <p>条形码：{{$stock->product->barcode}}</p>
    </div>
    <div class="proset_box">
        <div class="proset_header">
            <p>目前这件大概剩多少？</p>
        </div>
        <div class="proset_info">
            <div class="weui-slider">
                <div id="sliderInner" class="weui-slider__inner">
                    <div id="sliderTrack" style="width: 50%;" class="weui-slider__track"></div>
                    <div id="sliderHandler" style="left: 50%;" class="weui-slider__handler"><div class="div-slider_handler_inner"></div></div>
                    <div class="notice_word" id="notice_word"><span id="sliderValue" >50</span>%</div>
                </div>
            </div>
        </div>
    </div>
    <div class="proset_box">
        <div class="proset_header">
            <p>全新的一件大概能用多久？</p>
        </div>
        <div class="div_commonBox">
            <input type="tel" class="commonBox" data_unit="month"  id="cyclesTime" placeholder="0" onfocus="this.placeholder=''" onblur="this.placeholder='0'">
            <lable class="unitLable cyclesTime onfocus cyclesTime_left" data-value="day">天</lable>
            <lable class="unitLable cyclesTime cyclesTime_center" data-value="week">周</lable>
            <lable class="unitLable cyclesTime cyclesTime_right" data-value="month">月</lable>
        </div>

    </div>
    <div class="proset_box">
        <div class="proset_header">
            <p>除了目前这件，还有几件全新的没用过？</p>
        </div>
        <div class="div_commonBox">
            <input type="tel" class="commonBox" data_unit="month"  id="number" placeholder="0" onfocus="this.placeholder=''" onblur="this.placeholder='0'">
            <lable class="unitLable transparent_background black_text">件</lable>
        </div>
    </div>

    <div class="ch_op">
        <input id="edit" type="button" value="保存修改" class="abtn_gray_large" />
    </div>
    <div class="ch_c">
        <div class="c_btn_box"><input id="usenew" type="button" value="使用新一件" class="abtn_gray_large" /></div>
        <p>点击后新一件商品的剩余用量从100%开始计算，当前商品被替换</p>
        <div class="c_btn_box"><input onclick="deleteProduct()" type="button" value="删除该商品" class="abtn_gray_large" /></div>
        <p>点击后商品会从存货清单中消失</p>
    </div>
</div>
</body>
</html>
<script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/swiper.min.js') }}"></script>
<script type="text/javascript">
    // 弹框
    function deleteProduct() {
        $('.layerBy').show();
        $("#alert_box_1").show();
        $("#notNull").hide();
    }

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
                    var dist = startLeft + e.changedTouches[0].clientX - startX,
                            percent;
                    dist = dist < 0 ? 0 : dist > totalLen ? totalLen : dist;
                    percent =  parseInt(dist / totalLen * 100);
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
        sliderAction();//拉动条
        $(".cyclesTime").removeClass('onfocus');
        //每次进入页面，初始化各个参数
        var initialCycle =  {{ $stock->day}}+{{$stock->month}}*31;
        var initialQuantity = {{ $stock->quantity }};
        var intialLast = {{$stock->last}}*100;
        var cyclesTimeElments = document.getElementsByClassName("cyclesTime");
        if (initialCycle % 31 == 0){
            $(cyclesTimeElments[2]).addClass('onfocus');
            initialCycle = initialCycle/31;
        } else if (initialCycle % 7 == 0){
            $(cyclesTimeElments[1]).addClass('onfocus');
            initialCycle = initialCycle/7;
        } else {
            $(cyclesTimeElments[0]).addClass('onfocus');
        }

        $('#cyclesTime').val(initialCycle);
        $("#number").val(initialQuantity);
        $("#sliderValue").text(intialLast);
        $("#sliderTrack").css('width', intialLast +'%');
        $("#sliderHandler").css('left', intialLast +'%');
        $('#notice_word').css('left', intialLast +'%');


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

        $('#edit').click(function(){
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
                $("#alert_box_1").hide();
                setTimeout(function () {
                    $('.layerBy').hide();
                    $("#notNull").hide();
                },2000);
                return ;
            }

            $.ajax({
                type: 'POST',
                url: '{{ url('stock/update/'.$stock->productid) }}',
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
                success: function(data){
                    if(data.retcode == 0)
                    {
                        if(data.toscan == 1)
                        {
                            window.location.href = '/scan?v='+Math.random();
                            window.event.returnValue=false;
                        }
                        else
                        {
                            $(".layerBy").show();
                            $("#alert_box_2").show();
                            setTimeout(function(){
                                location.reload();
                            }, 1500);

                        }
                    }
                },
                error: function(xhr, type){

                }
            });
        });
        $('#usenew').click(function(){
            $.ajax({
                type: 'POST',
                url: '/stock/new/{{$stock->productid}}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
//                        window.location.href = '/stock?v='+Math.random();
                        window.location.reload();
                        window.event.returnValue=false;
                    }
                    elseif(data.retcode == 1 )
                    {
                        window.location.href = '/user?v='+Math.random();
                        window.event.returnValue=false;
                    }
                },
                error: function(xhr, type){

                }
            });
        });
        $('#del').click(function(){
            $.ajax({
                type: 'POST',
                url: '{{ url('stock/delete/'.$stock->id) }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
                        location.href = '{{ url('stock') }}';
                    }

                },
                error: function(xhr, type){

                }
            });
        });
    });
</script>