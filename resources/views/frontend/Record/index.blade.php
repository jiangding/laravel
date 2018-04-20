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

</head>
<body>
<div class="text-center" style="margin-top:16px;font-size:1.4rem">
    <h5><img src="/images/set_bg.png" style="width: 15px;margin-bottom: 4px;margin-right: 4px;">先勾选商品，再点击下方的『去比价』按钮吧</h5>
</div>
<div class="record_div">
    <table>
        <tbody>
            @foreach($records as $key => $value)
                <tr style="height: 80px;border-bottom:1px solid #f0f0f0" record_id="{{$value->id}}">
                    @if($value->product->name)
                        <td style="width: 10%;text-align: center;padding-left:1rem;padding-right:20px" class="recodr_checkbox_td detail">
                           <input type="checkbox" data-barcode="{{$value->product->barcode}}" style="display: none" class="checkbox" data-name="{{$value->product->name}}">
                           <img src="/images/checkbox_no.png"  class='recodr_checkbox' style="margin-left: -15px;width: 40px;margin-bottom: 4px;">
                        </td>
                    @else
                        <td></td>
                    @endif
                    <td style="width:60%;" class="record_td">
                        @if($value->product->name)
                            <span>{{$value->product->name or ''}}  </span>
                        @else
                            <span style="opacity:0.5">商品无法识别<br /><span style="font-size: 1rem">{{ $value->product->barcode or '' }}</span></span>
                        @endif
                        <br />
                        @if ($value->status)
                            <span class="record_label">快用完了</span>
                            <span class="record_remidDate">提醒日：{{$value->time}}</span>
                        @endif
                    </td>
                    <td class="record_td" style="width: 35%;padding-left:4%;opacity: 0.5;">{{$value->updated_at->format('Y-m-d')}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="pro_op" style="border:none; padding-bottom:0.3rem;position:fixed;width:90%;margin-left: 5%;bottom: 18px;">
    <button id="go" type="button" class="abtn_gray_large" style="width:76%;padding-bottom:0.3rem;color:#9b9b9b"/>
        <img src="/images/Shoppingcart_no.png" class="pic_size">
        去比价( <span id="num">0</span> )
    </button>
</div>

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

    <!--补货成功弹窗start-->
    <div class="alert_box" id="alert_box_3">
        <p>比价结果稍后在微信对话窗返回，先忙别的吧</p>
        <div class="alert_op">
            <span><input type="button" value="好的" class="abtn_yellow" onclick="ok_to_send();"/></span>
        </div>
    </div>
    <!--补货提交出错弹窗end-->
</div>

</body>
<script type="text/javascript" src="{{ URL::asset('plugins/jQuery/jquery-1.11.1.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('body').css('background-image', 'linear-gradient(-1deg, #F9F9F9 2%, #D2D1D1 100%)');

        checkifpointproduct();//页面刷新判断是否是通过模板消息进入

        //点击条目进入详情
        $(".record_td").on('click',function(){
            var tr = $(this).parent('tr');
            var record_id = tr.attr('record_id');
            var url = '/record/update/'+record_id;
            window.location.href = url;
        });


        //点击选择框
        $(".recodr_checkbox_td").click( function(){
            //触发checkbox
            var num = $("#num").text();
            var image = $(this).find('.recodr_checkbox');
            image.fadeOut(0);
            if (image.hasClass('checked')){
                num--;
                image.removeClass('checked')
                        .attr('src', '/images/checkbox_no.png')
                        .prev('input')
                        .removeAttr('checked');
            }else {
                num++;
                image.addClass('checked')
                        .attr('src', '/images/checkbox_yes.png')
                        .prev('input')
                        .attr('checked', 'true');
            }
            if (num >0){
                $('#go')
                        .css('color','#36007c')
                        .find('img')
                        .attr('src','/images/Shoppingcart_yes.png');

            } else {
                $('#go')
                        .css('color','#9b9b9b')
                        .find('img')
                        .attr('src','/images/Shoppingcart_no.png');
            }
            $("#num").text(num);
            image.fadeIn(200);
        });


        //点击去比价
        $("#go").click( function(){
           //获取checkbox的barcode
            var num = $('#num').text();
            if (num == 0){
                return false;
            } else {
                $('.layerBy').show();
                $("#alert_box_1").show();
            }
        });
    });

    function checkifpointproduct(){
        $('.record_div tr').each(function(){
            var id = $(this).attr('record_id');
            if (id == '{{$id}}'){
                $(this).addClass('nostock');
            }
        });
    }




    function verify() {
        $(".alert_box").hide();
        var arr = new Array();
        $('.checkbox').each(function(){
            if ($(this).attr('checked') == 'checked'){
                arr.push($(this).attr('data-barcode'));
            }
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
            context: $('body'),
            success: function(data){
                if(data.retcode == 0)
                {
                    $(".layerBy").show();
                    $("#alert_box_3").show();
                    // 直接支付
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
//                $("#alert_box_2").show();
            }
        });
    }


    function ok_to_send(){
        WeixinJSBridge.call('closeWindow');

        // 获取选中商品
        var arr = new Array();
        var str = '';
        $('.checkbox').each(function(i,e){
            if ($(this).attr('checked') == 'checked'){
                str = $(this).attr('data-name');
                arr.push($(this).attr('data-barcode'));
            }
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
</html>
