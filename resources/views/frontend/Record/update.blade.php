<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>商品详情</title>
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
    <meta name="apple-mobile-web-app-title" content="商品详情" />
    <meta name="msapplication-TileColor" content="#090a0a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ URL::asset('css/css_style.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('plugins/mobiscoll/mobiscroll.jquery.min.css') }}">
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
   <div style="padding-top: 36px;background-color: white;padding-bottom: 36px;">
           <li>
               <div class="shopping-cart-pic"><img src="{{ URL::asset($records->catalog['icon'])}}" /></div>
               @if ($records->product['name'] != '')
                   <h2 class="shopping-cart-title">{{ $records->product['name'] }}</h2>
               @else
                   <h2 class="shopping-cart-title">商品无法识别</h2>
               @endif
               <h3 class="shopping-cart-barcode">条形码: {{$records->product->barcode}}</h3>
           </li>
   </div>

   <div class="div_switch_box">
       <div style="float: left;font-size: 1.4rem;font-weight: bold;margin-top: 2px;">补货提醒</div>
       <div class="div_switch" id="switch">
           <img src="/images/switch_no.png"  class="switch_left" id="switch_orgi">
       </div>
   </div>
   <div class="div_goods_info_box" style="height: 60px;">
       <div class="edit_remind hidden">
           <lable for="remind_date" style="margin-top: 3px">设定提醒日期</lable>
           <input value='{{$records->remind_at or ''}}'   type="text"
                  id="remind_date" class="remind_date" readonly="readonly"
                  style="border: 1px solid #e0e0e0;padding: 4px 3px;border-radius: 6px;text-align: center;color:#36007c;font-size:1.5rem;margin-bottom: 3px; width: 50%;">
       </div>
        <div class="infos" style="font-size: 1.2rem;text-align: center;padding-top: 15px;margin: 0 8% 0 8%;">
            预估补货的日期，刚好会在当天晚上9点发微信消息提醒你
        </div>
   </div>


   <div class="pro_op" style="border:none;width:90%;margin-left: 5%;margin-top: 36px;padding-top:0">
       <button id="delete" type="button" class="abtn_gray_large" style="width:76%;color:#36007c"/>删除商品</button>
   </div>


   <div class="layerBy">
       <div class="alert_box" id="alert_box_1">
           <p>确认删除吗？</p>
           <div class="alert_op">
               <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
               <span><input type="button" value="确认" class="abtn_yellow" onclick="deleted();" /></span>
           </div>
       </div>
   </div>
</body>
</html>
<script type="text/javascript" src="{{ URL::asset('plugins/jQuery/jquery-1.11.1.min.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.jquery.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){
//        $('body').css('background-image', 'linear-gradient(-1deg, #D2D1D1 2%, #F9F9F9 100%)');
        $('body').css('background-image', 'linear-gradient(-180deg, #FAEA73 0%, #F4D03F 100%)');

        checkifHaveRemindTime();//检查是否已经有remind_at数据

        //切换
        $("#switch").click(function(){
            if ($(this).find('#switch_orgi').hasClass('switch_left')){
                setTimeout(function(){
                    $(".edit_remind").removeClass('hidden');
                }, 300);

                $(".div_goods_info_box").animate({height:"128px"},'swing');
                switchChangeToRight();
                updateStatusToOne();

            }else {
                setTimeout(function(){
                    $(".edit_remind").addClass('hidden');
                }, 300);
                $(".div_goods_info_box").animate({height:"60px"},'swing');
                switchChangeToLeft();
                updateStatusToZero();

            }
        });

        //设置提醒时间
        var now = new Date(),
                max = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        $("#remind_date").mobiscroll().date({
            theme: 'ios',
            mode: 'scroller',
            display: 'center',
            defaultValue: '',
            max: max,
            lang:'zh',
            dateFormat:'yy-mm-dd',
            dateOrder:'yymmdd',
            dayText: '日', monthText: '月', yearText: '年',
            onSet:function(event, inst){
                var date = event.valueText;
                var url = '/record/update/remind/{{$records->id}}/' + date;
                $.ajax({
                    url: url,
                    type: 'get',
                    async: true,
                    dataType: 'text',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(returnMessage){

                    },
                    error:function(xhr, type){
                    }
                });
            }
        });

        //删除商品
        $('#delete').click(function(){
            $(".layerBy").show();
            $('#alert_box_1').show();

        });

    });

    function updateStatusToOne(){
        var url = '/record/update/updateRemind/1/{{$records->id}}';
        $.ajax({
            url: url,
            type: 'get',
            async: true,
            dataType: 'text',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(returnMessage){
            },
            error:function(xhr, type){
            }
        });
    }

    function updateStatusToZero(){
        var url = '/record/update/updateRemind/0/{{$records->id}}';
        $.ajax({
            url: url,
            type: 'get',
            async: true,
            dataType: 'text',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(returnMessage){
            },
            error:function(xhr, type){
            }
        });
    }




    function deleted(){
        var url = '/record/delete/{{$records->id}}';
        location.href = url;
    }


    function switchChangeToRight(){
//        $("#switch").removeClass('left').addClass('right');
        $("#switch_orgi").attr('src', '/images/switch_yes.png').removeClass('switch_left').addClass('switch_right');
        $('#switch').css('background-color', '#36007C');
    }

    function switchChangeToLeft(){
//        $("#switch").removeClass('right').addClass('left');
        $("#switch_orgi").attr('src', '/images/switch_no.png').removeClass('switch_right').addClass('switch_left');
        $('#switch').css('background-color', 'white');
    }


    function checkifHaveRemindTime(){
        if ('{{$records->status}}' != '0'){
            switchChangeToRight();
            setTimeout(function(){
                $(".edit_remind").removeClass('hidden');
            }, 300);
            $(".div_goods_info_box").animate({height:"128px"},'swing');
        }
    }

    function changeColorOfSwitch(color){
        $(".switchOrigin").css('background-color',color);
        $("#div_smallBoll").css({'border' : '1px solid ' + color});
    }

</script>