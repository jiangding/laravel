<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>存货清单</title>
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
<div class="search_warp">
    <div class="search_bg clearfix">
        <form id="searchForm" action="{{ url('stock') }}" method="get">
        <div class="search_box">
            <input id="search_txt" type="text" name="keyword" class="search_input" placeholder="搜索产品关键字" value="{{ isset($_GET['keyword']) ? $_GET['keyword'] : '' }}" />
            <span class="search_close" onclick="clearSearch()"></span>
            <i class="search_icon"></i>
        </div>
        {{--<input type="submit" class="scenes_box" value="筛 选">--}}
        <div class="scenes_box">刷 新</div>
        {{--<i class="scenes_layer"></i>--}}
        </form>
    </div>
    <div class="scenes_layer_box" id="scenes_select">
        <div class="scenes_sort">
            <div class="scenes_header">排序方式</div>
            <div class="sort_span clearfix">
                <span class="on">剩余天数</span>
                <span>最近添加</span>
            </div>
        </div>
        <dl class="scenes_dl">
            <dt>家中场景</dt>
            <dd class="clearfix">
                <span class="on" data="all">所有</span>
                {{--@foreach($scenes as $scene)--}}
                    {{--<span data="{{ $scene->name }}">{{ $scene->name }}</span>--}}
                {{--@endforeach--}}
            </dd>
        </dl>
        <dl class="scenes_dl">
            <dt>剩余用量</dt>
            <dd class="clearfix">
                <span data="all">所有</span>
                <span data="7">7天</span>
                <span data="14">14天</span>
                <span data="30">30天</span>
                <span data="60">60天</span>
            </dd>
            <i class="layer_dl"></i>
        </dl>
        <div class="scenes_op">
            <input type="button" value="保存" class="abtn_gray_large  w45" onclick="scenes_sort();" />
        </div>
    </div>
</div>

@if(count($stock['7day']) ==0 && count($stock['14day']) ==0 && count($stock['30day']) ==0 && count($stock['60day']) ==0 && count($stock['long']) ==0)
    <div class="no_stock"></div>
@else
<div class="parity_box">
    @if(count($stock['7day']) !=0)
    <div class="parity_listbox" data-role="7day">
        <div class="parity_title yellow_background">
            <b>
                剩余用量<font>不到7天</font>需要补货啦
            </b>
            <input id='check-7day' type="checkbox" name='check-7day'  />
            <label for="check-7day"></label>
        </div>

        <ul class="parity_list">
            @foreach($stock['7day'] as $userstock)
            <li>
                <i><input id='{{ $userstock->product->barcode }}' type="checkbox" data='product' name='check-7day' /><label for="{{ $userstock->product->barcode }}">&nbsp;</label></i>
                <div class="pic"><img src="{{ URL::asset($userstock->catalog->icon)}}" /></div>
                <a class="parity_txt" href="{{ Url('stock/update/'.$userstock->productid) }}">
                    <h4>{{ $userstock->catalog->name or '' }}</h4>
                    <h2>{{ $userstock->product->name or '' }}</h2>
                    {{--<h3>剩余 {{ $userstock->b }} 天 / 还有 {{ $userstock->a }} 件新的</h3>--}}
                    <h3>还有 {{ $userstock->a or 0 }} 件新的</h3>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @else
        <div class="parity_listbox parity_list_bottom" data-role="7day">
            <div class="parity_title black_text">
                <b>剩余用量<font>不到7天</font>时会自动在此显示</b>
                <input id='check-7day' type="checkbox" name='check-7day'  />
                <label for="check-7day"></label>
            </div>
        </div>
    @endif
    @if(count($stock['14day']) !=0)
        <div class="parity_listbox" data-role="14day">
            <div class="parity_title">
                <b>剩余用量<font>不到14天</font>可以勾选比价</b>
                <input id='check-14day' type="checkbox" name='check-14day'  />
                <label for="check-14day">&nbsp;</label>
            </div>
            <ul class="parity_list">
                @foreach($stock['14day'] as $userstock)
                    <li>
                        <i><input id='{{ $userstock->product->barcode }}' type="checkbox" data='product' name='check-14day' /><label for="{{ $userstock->product->barcode }}">&nbsp;</label></i>
                        <div class="pic"><img src="{{ URL::asset($userstock->catalog->icon)}}" /></div>
                        <a class="parity_txt" href="{{ Url('stock/update/'.$userstock->productid) }}">
                            <h4>{{ $userstock->catalog->name  or '' }}</h4>
                            <h2>{{ $userstock->product->name  or '' }}</h2>
                            <h3>还有 {{ $userstock->a  or 0 }} 件新的</h3>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(count($stock['30day']) !=0)
        <div class="parity_listbox" data-role="30day">
            <div class="parity_title">
                <b>剩余用量<font>不到30天</font>可以勾选比价</b>
                <input id='check-30day' type="checkbox" name='check-30day'  />
                <label for="check-30day">&nbsp;</label>
            </div>
            <ul class="parity_list">
                @foreach($stock['30day'] as $userstock)
                    <li>
                        <i><input id='{{ $userstock->product->barcode }}' type="checkbox" data='product' name='check-30day' /><label for="{{ $userstock->product->barcode }}">&nbsp;</label></i>
                        <div class="pic"><img src="{{ URL::asset($userstock->catalog->icon)}}" /></div>
                        <a class="parity_txt" href="{{ Url('stock/update/'.$userstock->productid) }}">
                            <h4>{{ $userstock->catalog->name  or '' }}</h4>
                            <h2>{{ $userstock->product->name  or ''}}</h2>
                            <h3>还有 {{ $userstock->a  or 0 }} 件新的</h3>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(count($stock['60day']) !=0)
        <div class="parity_listbox" data-role="60day">
            <div class="parity_title">
                <b>剩余用量<font>不到60天</font>可以勾选比价</b>
                <input id='check-60day' type="checkbox" name='check-60day'  />
                <label for="check-60day">&nbsp;</label>
            </div>
            <ul class="parity_list">
                @foreach($stock['60day'] as $userstock)
                    <li>
                        <i><input id='{{ $userstock->product->barcode }}' type="checkbox" data='product' name='check-60day' /><label for="{{ $userstock->product->barcode }}">&nbsp;</label></i>
                        <div class="pic"><img src="{{ URL::asset($userstock->catalog->icon)}}" /></div>
                        <a class="parity_txt" href="{{ Url('stock/update/'.$userstock->productid) }}">
                            <h4>{{ $userstock->catalog->name  or ''}}</h4>
                            <h2>{{ $userstock->product->name  or ''}}</h2>
                            <h3>还有 {{ $userstock->a  or 0}} 件新的</h3>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

        @if(count($stock['long']) !=0)
            <div class="parity_listbox" data-role="60day">
                <div class="parity_title">
                    <b>剩余用量<font>超过60天</font>可以勾选比价</b>
                    <input id='check-60day' type="checkbox" name='check-60day'  />
                    <label for="check-60day">&nbsp;</label>
                </div>
                <ul class="parity_list">
                    @foreach($stock['long'] as $userstock)
                        <li>
                            <i><input id='{{ $userstock->product->barcode }}' type="checkbox" data='product' name='check-60day' /><label for="{{ $userstock->product->barcode }}">&nbsp;</label></i>
                            <div class="pic"><img src="{{ URL::asset($userstock->catalog->icon)}}" /></div>
                            <a class="parity_txt" href="{{ Url('stock/update/'.$userstock->productid) }}">
                                <h4>{{ $userstock->catalog->name  or ''}}</h4>
                                <h2>{{ $userstock->product->name  or ''}}</h2>
                                <h3>还有 {{ $userstock->a  or 0}} 件新的</h3>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

</div>
@endif


<div class="footer_fixed clearfix">
    <ul class="foot_list clearfix">
        {{--<li><a href="/scan"><span><i class="scan_icon"></i>扫一扫</span></a></li>--}}
        <li><a href="javascript:" id="scan_at"><span>扫一扫</span></a></li>
        {{--<li><a href="javascript:" onclick="Replenishment();" id="replen_alink"><span>去补货(<font id="num">0</font>)<i class="arrow_right"></i></span></a></li>--}}
        <li><a href="javascript:" onclick="Replenishment();" id="replen_alink"><span>去比价 (<font id="num">0</font>)</span></a></li>
    </ul>
</div>
<!--去补货效果提示-->
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

    <!--补货提交出错弹窗start-->
    <div class="alert_box" id="alert_box_2">
        <p>提交过程出了点小差错......</p>
        <div class="alert_op">
            <span><input type="button" value="联系客服" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="重新提交" class="abtn_yellow" onclick="verify();" /></span>
        </div>
    </div>

    <!--补货成功弹窗start-->
    <div class="alert_box" id="alert_box_3">
        <p>比价结果稍后在微信对话窗返回，先忙别的吧</p>
        <div class="alert_op">
            <span><input type="button" value="好的" class="abtn_yellow" onclick="ok_to_send();"/></span>
        </div>
    </div>
    <!--补货提交出错弹窗end-->
</div>
<!--去补货效果提示-->

{{--提示信息--}}
<div class="div_lable">
    <div class="lable">
        <div class="lable_text">加入比价清单</div>
    </div>
</div>

</body>
</html>
<script type="text/javascript" src="{{ URL::asset('plugins/jQuery/jquery-1.11.1.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/jquery.fly.min.js') }}"></script>
<script type="text/javascript">
    function ok_to_send(){
        WeixinJSBridge.call('closeWindow');

        // 获取选中商品
        var arr = new Array();
        var str = '';
        $(".parity_box input[type='checkbox'][data='product']:checked").each(function(i,e){
            if(i == 0){
                str = $(this).parent().parent().find('h2').html();
            }
            arr.push($(e).attr('id'));
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

    function clearSearch()
    {
        $('#search_txt').val('');
        $('#searchForm').submit();
    }
    $(function () {

        var windowWidth = $(window).width();

        $('.no_stock').css('background-size', '90% '+ 0.35*windowWidth+ 'px');
        //点击添加第一个存货大按钮（其实是图片）
        $(".no_stock").click(function(){
            $("#scan_at").click();
        });
        //点击分类标题栏，全选
        $(".parity_title b").click(function(){
            var checkbox = $(this).next("input[type='checkbox']");
            checkbox.click();
        });

        //全选效果
        $(".parity_title input[type='checkbox']").click(function () {
            var name = $(this).attr("name");
            $(".parity_list input[name='" + name + "']").prop('checked', $(this).prop('checked'));
        });
        $(".parity_box input[type='checkbox']").click(function () {
            var name = $(this).attr("name");
            if ($(".parity_list input[type='checkbox']:checked").length > 0) {
                if ($(this).is(":checked")) {
                    addCart($(this).parent());
                }
                $("#replen_alink").addClass("on");
                $("#num").text($(".parity_list input[type='checkbox']:checked").length);
            }
            if ($(".parity_list input[type='checkbox']:checked").length ==0) {
                $("#replen_alink").removeClass("on");
                $("#num").text(0);
            }
            if ($(".parity_list input[name='" + name + "']").not("input:checked").length == 0) {
                $(".parity_title input[name='" + name + "']").prop("checked", true);
            }
            if ($(".parity_list input[name='" + name + "']").not("input:checked").length > 0) {
                $(".parity_title input[name='" + name + "']").prop("checked", false);
            }
        });


        function showLable(divTop, text){
            $(".div_lable").fadeIn().css('top', divTop+'px');
            $(".div_lable").children('.lable_text').text(text);
            setTimeout(function(){
                $(".div_lable").fadeOut();
            }, 1000);
        }
        //添加购物车效果
        function addCart(obj) {
            $(".u-flyer").remove();
            var offset = $('#num').offset();
            var src = $(obj).parent().find(".pic img").attr("src");
            var flyer = $('<img class="u-flyer" src="' + src + '" />');
            var divTop = $(obj).offset().top;
            var divLeft = $(obj).offset().left;
            flyer.fly({
                start: {
                    left: divLeft,
                    top: divTop
                },
                end: {
                    left: offset.left,
                    top: offset.top,
                    width: 0,
                    height: 0
                }
            });
            //显示加入比价清单标签
            showLable(divTop, '加入比价清单');
        }
        //场景选择效果start
//        $(".scenes_box").click(function () {
//            $(this).css("z-index", 5);
//            $(".scenes_box").addClass("scenes_box_on");
//            $(".scenes_layer,#scenes_select").show();
//        });

        $(".scenes_box").click(function () {
            window.location.reload();
        });
        $(".scenes_dl span").click(function () {
            alert($(this).text());
            $(this).parent().find("span").removeClass("on");
            $(this).addClass("on");
        });
        $(".sort_span span").click(function () {
            $(".sort_span span").removeClass("on");
            $(this).addClass("on");
            if ($(this).index() == 0) {
                $(".layer_dl").hide();
            }
            else {
                $(".scenes_dl").eq(1).find("span").removeClass("on");
                $(".layer_dl").show();
            }
        });
        $(".scenes_layer").click(function () {
            $(".scenes_box").removeClass("scenes_box_on");
            $(".scenes_layer,.scenes_layer_box").hide();
        });
        //场景选择效果end
    });
    //筛 选保存 效果
    function scenes_sort() {
        $(".scenes_box").removeClass("scenes_box_on");
        $(".scenes_layer,.scenes_layer_box").hide();
    }

    //去补货效果
    function Replenishment() {
        if ($("#num").text() != "0") {
            $('.layerBy').show();
            $("#alert_box_1").show();
            $("#alert_box_2").hide();
        }
    }
    function verify() {
        $(".alert_box").hide();
        var arr = new Array();
        $(".parity_box input[type='checkbox'][data='product']:checked").each(function(i,e){
            arr.push($(e).attr('id'));
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
                // 直接支付
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
//                $("#alert_box_2").show();
            }
        });
    }
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: false,
        appId: '{{ $appId }}',
        timestamp: '{{ $timestamp }}',
        nonceStr: '{{ $nonceStr }}',
        signature: '{{ $signature }}',
        jsApiList: {!! $jsApiList !!},
    });
    wx.ready(function () {
        document.querySelector('#scan_at').onclick = function () {
            wx.scanQRCode({
                needResult: 1,
                scanType: ["barCode"],
                success: function (res) {
                    location.href = '/stock/barcode/2/'+res.resultStr.split(",")[1];
                }
            });
        };
    });
</script>