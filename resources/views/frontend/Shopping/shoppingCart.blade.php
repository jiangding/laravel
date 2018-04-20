<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>立刻购买</title>
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
   <div class="shopping-cart-info">
           <div class="shopping-cart-car">
               <table style="width: 100%; font-size: 1.3rem;">
                   <tbody class="shopping-cart-tbody">
                   @if(count($products) == 0)
                       <tr><td style="height:3rem; text-align: center;margin-top:1rem">你的购物车饿了</td></tr>
                   @else
                       @foreach($products as $key=>$product)
                           @if ($key == 0)
                               @if(isset($product['name']))
                                   <tr class="shopping-cart-product fisrt_goods" data-barcode={{$product['barcode']}}>
                                       <td class='titletd title'>{{$product['name']}}</td>
                                       <td class="shopping-delete" data-id="{{$product['id']}}">删除</td>
                                   </tr>
                               @else
                                   <tr class="shopping-cart-product first_goods" data-barcode={{$product}}>
                                       <td class="titletd"><span class='title'>{{$product}}</span><br /><span style="opacity: 0.4;font-weight: normal">商品信息将在比价结果中显示</span></td>
                                       <td class="shopping-delete" data-id="{{$product}}">删除</td>
                                   </tr>
                               @endif
                           @else
                               @if(isset($product['name']))
                                   <tr class="shopping-cart-product" data-barcode={{$product['barcode']}}>
                                       <td class='titletd title'>{{$product['name']}}</td>
                                       <td class="shopping-delete" data-id="{{$product['id']}}">删除</td>
                                   </tr>
                               @else
                                   <tr class="shopping-cart-product" data-barcode={{$product}}>
                                       <td class="titletd"><span class='title'>{{$product}}</span><br /><span style="opacity: 0.4;font-weight: normal">商品信息将在比价结果中显示</span></td>
                                       <td class="shopping-delete" data-id="{{$product}}">删除</td>
                                   </tr>
                               @endif
                           @endif
                       @endforeach
                   @endif
                   </tbody>
               </table>
           </div>
   </div>


   <div class="footer_fixed order_fixed clearfix">
       <span>
           <button id="scan" type="button"  class="abtn_gray_large" style="padding-bottom:0.5rem;" >
               <img src="/images/scan_shopping.png" class="pic_size" style="width:25px;margin-bottom:3px">&nbsp;&nbsp;继续扫
           </button>
       </span>
       {{--<span><a href="javascript:" class="abtn_gray_large"  id="end"></a>--}}
       <span>
           <button id="go" type="button" class="abtn_gray_large" style="padding-bottom:0.3rem;">
               <img src="/images/Shoppingcart_yes.png" class="pic_size">&nbsp;&nbsp;去比价 ( {{count($products)}} )
           </button>
       </span>
   </div>

   {{--<div class="pro_op" style="border:none; padding-bottom:8rem;width:90%;margin-left: 5%;margin-top: 0;">--}}
       {{--<p><button id="scan" type="button"  class="abtn_gray_large" style="width:76%;padding-bottom:0.5rem;" ><img src="/images/scan_shopping.png" class="pic_size" style="width:25px;margin-bottom:3px">&nbsp;&nbsp;继续扫</button></p>--}}
   {{--</div>--}}

   {{--<div class="pro_op" style="border:none; padding-bottom:0.3rem;position:fixed;width:90%;margin-left: 5%;bottom: 18px;">--}}
       {{--<button id="go" type="button" class="abtn_gray_large" style="width:76%;padding-bottom:0.1rem;"><img src="/images/Shoppingcart_yes.png" class="pic_size">&nbsp;&nbsp;去比价</button>--}}
   {{--</div>--}}

   <div class="layerBy">
       <!--补货弹窗start-->
       <div class="alert_box" id="alert_box_1">
           <p>确认比价吗？</p>
           <div class="alert_op">
               <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
               <span><input type="button" value="确认" class="abtn_yellow" onclick="verify();" /></span>
           </div>
       </div>
       <!--补货弹窗end-->

       <div class="alert_box" id="alert_box_2">
           <p>确认删除吗？</p>
           <div class="alert_op">
               <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
               <span><input id="delete" type="button" value="确认" class="abtn_yellow" data-id="" onclick="deleteGoods(this)" /></span>
           </div>
       </div>


       <!--补货成功弹窗start-->
       <div class="alert_box" id="alert_box_3">
           <p>比价结果几分钟后在微信对话窗返回，先忙别的吧</p>
           <div class="alert_op">
               <span><input type="button" value="好的" class="abtn_yellow" onclick="ok_to_send();"/></span>
           </div>
       </div>
       <!--补货提交出错弹窗end-->
   </div>



</body>
</html>
<script type="text/javascript" src="{{ URL::asset('plugins/jQuery/jquery-1.11.1.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.fly.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('body').css('background-image', 'linear-gradient(-1deg, #F9F9F9 2%, #D2D1D1 100%)');
        $(".shopping-cart-product:last").css('border-bottom','none');//除去表格最下一格tr的下划线
        //删除
        $(".shopping-delete").click(function(){
           var id = $(this).attr('data-id');
            $("#delete").attr('data-id', id);
            $(".layerBy").show();
            $("#alert_box_2").show();
        });



        //去比价
        $("#go").on('touchend', function(){
            if ($('.shopping-cart-product').length != 0){
                $('.layerBy').show();
                $("#alert_box_1").show();
                $("#alert_box_2").hide();
            }
        });
    });


    //删除
    function deleteGoods(that){
        var id = $(that).attr('data-id');
        $.ajax({
            type: 'POST',
            url: '{{ url('shopping/delete') }}',
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
                if(data == 0)
                {
                    $(".layerBy").show();
                    $("#alert_box_3").show();
                }else{
                    location.href = '/shopping/shoppingCart/1';
                }
            },
            error: function(xhr, type){
                console.log(xhr);
                console.log(type);
            }
        });
    }






    function verify(){
        var barcodes = [];
        $(".shopping-cart-product").each(function(){
            barcodes.push($.trim($(this).attr('data-barcode')));
        });
        $.ajax({
            type: 'POST',
            url: '{{ url('detail/create') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                barcode: JSON.stringify(barcodes),
                type : 1
            },
            // type of data we are expecting in return:
            dataType: 'json',
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
                    alert("11111111");
                }
            },
            error: function(xhr, type){
                alert("222222222");
                console.log(xhr);
                console.log(type);
            }
        });
    }


    //关闭浏览器，并发送消息
    function ok_to_send(){
        WeixinJSBridge.call('closeWindow');

        // 获取选中商品
        var arr = new Array();
        var str = '';
        $(".title").each(function(i){
            if (i == 0){
                str = $(this).text();
            }
            arr.push($(this).text());
        });
        var count = arr.length;
        $.ajax({
            type: 'POST',
            url: '{{ url('stock/sendMessage') }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                openid : '{{ $currentUser['openid'] }}',
                message: '{{ $currentUser['nickname'] }}，『'+str+'』等'+count+'件商品的比价稍后在这里返回，先忙别的吧。'
            },
            // type of data we are expecting in return:
            dataType: 'json',
            timeout: 300,
            context: $('body'),
            success: function(data){

            },
            error: function(xhr, type){

            }
        });
    }
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    $(document).ready(function(){
        wx.config({
            debug: false,
            appId: '{{ $appId }}',
            timestamp: '{{ $timestamp }}',
            nonceStr: '{{ $nonceStr }}',
            signature: '{{ $signature }}',
            jsApiList: {!! $jsApiList !!}
    });
        wx.ready(function () {
            document.querySelector('#scan').onclick = function () {
                wx.scanQRCode({
                    needResult: 1,
                    scanType: ["barCode"],
                    success: function (res) {
                        location.href = '/shopping/shoppingCart/'+res.resultStr.split(",")[1];
                    }
                });
            };
        });
    });

</script>