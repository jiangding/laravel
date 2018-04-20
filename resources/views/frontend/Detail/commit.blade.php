<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>确认订单</title>
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
    <link rel="stylesheet" href="{{ URL::asset('css/mobiscroll.scroller.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('css/mobiscroll.scroller.android-ics.css') }}">
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
<div id="step01">

    @if(!$detail->address)
        <div class="order_add_address" onclick="newAddress();">
            <p>添加新地址</p>
        </div>
        <div class="new_address_strip"></div>
    @else
        <div class="order_addr" onclick="selectAddress();">
            <h2>{{ $detail->address->name or '' }}<span>{{ $detail->address->phone or '' }}</span></h2>
            <p>{{ $detail->address->area or '' }}</p>
            <p>{{ $detail->address->address or '' }}</p>
            {{--<strong class="addr_tag">{{ $detail->address->label or '' }}</strong>--}}
        </div>
        <div class="new_address_strip"></div>
    @endif
    <div class="order_par_list clearfix">

        <div class="par_box" id="JD">
            <div class="base">
                <h2>
                    京东<br />
                    <span class="total-text" ></span><font  class="total" initPrice=""></font>
                </h2>
                <div class="par_info">
                    <span class="postage_text">满99包邮</span>
                    <div class="tips"><span class="pull-left small-text">运费</span>
                        @if ($is_new)
                            <b class="pull-right small-text">0</b>
                        @else
                            <b class="pull-right small-text">{{ $detail->postage->JD }}</b>
                        @endif
                    </div>
                    <div class="tips"><span class="pull-left small-text">商品</span><span class="pull-right small-text price"></span></div>
                </div>
            </div>
        </div>
        <div class="par_box" id="TMALL">
            <div class="base">
                <h2>
                    天猫超市<br />
                    <span class="total-text" ></span><font  class="total" initPrice=""></font>
                </h2>
                <div class="par_info">
                    <span class="postage_text">满88包邮</span>
                    <div class="tips"><span class="pull-left small-text">运费</span>
                        @if ($is_new)
                            <b class="pull-right small-text">0</b>
                        @else
                            <b class="pull-right small-text">{{ $detail->postage->TMALL }}</b>
                        @endif
                    </div>
                    <div class="tips"><span class="pull-left small-text">商品</span><span class="pull-right small-text price"></span></div>
                </div>
            </div>
        </div>
        <div class="par_box" id="YHD">
            <div class="base">
                <h2>
                    一号店<br />
                    <span class="total-text" ></span><font  class="total" initPrice=""></font>
                </h2>
                <div class="par_info">
                    <span class="postage_text">满68包邮</span>
                    <div class="tips"><span class="pull-left small-text">运费</span>
                        @if ($is_new)
                            <b class="pull-right small-text">0</b>
                        @else
                            <b class="pull-right small-text">{{ $detail->postage->YHD }}</b>
                        @endif
                    </div>
                    <div class="tips"><span class="pull-left small-text">商品</span><span class="pull-right small-text price"></span></div>
                </div>
            </div>
        </div>

        <div style="text-align: center;font-size: 1.2rem;margin-top: 45%;">在平台没有自营商品的情况下为你挑选了销量较高的非自营商品</div>
    </div>
    @if ($is_new)
        <div class="no_postage">
            <img src="{{ URL::asset('images/nopostage.gif') }}">
        </div>
        <div class="detail_nopostage_line"></div>
    @endif
    <input type="hidden" value="{{$is_new}}" id="noPostage">



    <div class="order_pro_box">
        <ul class="order_pro_list">
            {{-- 自营有货 --}}
            @foreach($detail->products as $key => $value)
                @foreach($value as $k => $v)
                    {{-- */$replace_urls = json_decode($v->replace_url, true);/* --}}
                    {{-- */$urls = json_decode($v->url, true);/* --}}
                    @if ($v->name != '' && $v->price != 0 && isset($urls[$key]) && $urls[$key])
                        <li data-role="{{ $key }}">
                            <i><input data='{{ $v->id }}' type="checkbox" class='hidden' name='check-1' checked="checked" /><label for="check-2" class='hidden'>&nbsp;</label></i>
                            <div class="parity_txt">
                                @if($urls[$key])
                                    <a href="{{ $urls[$key] }}"><h2>{{ $v->name }}</h2></a>
                                @elseif($replace_urls[$key])
                                    <a href="{{ $replace_urls[$key] }}"><h2>{{ $v->name }}</h2></a>
                                @else
                                    <h2>{{ $v->name }}</h2>
                                @endif
                                <h3 data="{{ $v->price }}">￥{{ $v->price }}</h3>
                            </div>
                            <div class="quantity-form">
                                <a class="icon_lower"></a>
                                <input type="text" class="input_quantity" value="{{ $v->num }}" readonly="readonly" max="999" data-num="{{ $v->num }}"/>
                                <a class="icon_plus"></a>
                            </div>
                            @if ($v->num > 1)
                                <span class="order_min_num">{{ $v->num }}件起购</span>
                            @endif
                            <em class="del_link">删除</em>
                        </li>
                    @endif
                @endforeach
            @endforeach


            {{-- 非自营有货 --}}
            @foreach($detail->products as $key => $value)
                @foreach($value as $k => $v)
                    {{-- */$replace_urls = json_decode($v->replace_url, true);/* --}}
                    {{-- */$urls = json_decode($v->url, true);/* --}}

                    @if ($v->name != '' && $v->price != 0 && isset($replace_urls[$key]) && $replace_urls[$key])
                        <li data-role="{{ $key }}">
                            <i><input data='{{ $v->id }}' type="checkbox" class='hidden' name='check-1' checked="checked" /><label for="check-2" class='hidden'>&nbsp;</label></i>
                            <div class="parity_txt">
                                @if($urls[$key])
                                    <a href="{{ $urls[$key] }}"><h2>{{ $v->name }}</h2></a>
                                @elseif($replace_urls[$key])
                                    <a href="{{ $replace_urls[$key] }}"><h2>{{ $v->name }}</h2></a>
                                @else
                                    <h2>{{ $v->name }}</h2>
                                @endif
                                <h3 data="{{ $v->price }}">￥{{ $v->price }}</h3>
                            </div>
                            <div class="quantity-form">
                                <a class="icon_lower"></a>
                                <input type="text" class="input_quantity" value="{{ $v->num }}" readonly="readonly" max="999" data-num="{{ $v->num }}"/>
                                <a class="icon_plus"></a>
                            </div>
                            <span class="commit_label">非自营</span>
                            @if ($v->num > 1)
                                <span class="order_min_num">{{ $v->num }}件起购</span>
                            @endif
                            <em class="del_link">删除</em>
                        </li>
                    @endif
                @endforeach
            @endforeach


            {{-- 无货，包括自营和非自营 --}}
            @foreach($detail->products as $key => $value)
                @foreach($value as $k => $v)
                    {{-- */$replace_urls = json_decode($v->replace_url, true);/* --}}
                    {{-- */$urls = json_decode($v->url, true);/* --}}
                    @if ($v->name != '' && $v->price == 0)
                        <li data-role="{{ $key }}">
                            <div class="parity_txt" style="opacity: 0.5">
                                <h2>{{ $v->name }}</h2>
                                <h3 data="{{ $v->price }}" class="hidden">￥{{ $v->price }}</h3>
                            </div>
                            <div class="quantity-form hidden">
                                <a class="icon_lower"></a>
                                <input type="text" class="input_quantity" value="{{ $v->num }}" readonly="readonly" max="999" data-num="{{ $v->num }}"/>
                                <a class="icon_plus"></a>
                            </div>
                            <span class="commit_no_goods">平台无货</span>
                        </li>
                    @endif
                @endforeach
            @endforeach


            {{-- 无法识别 --}}
            @foreach($detail->products as $key => $value)
                @foreach($value as $k => $v)
                    {{-- */$replace_urls = json_decode($v->replace_url, true);/* --}}
                    {{-- */$urls = json_decode($v->url, true);/* --}}
                    @if ($v->name == '')
                        <li data-role="{{ $key }}">
                            <div style="margin-top: 12px;opacity:0.5">
                                <span style="font-size: 1.4rem">商品无法识别<br />{{ $v->barcode }}</span>
                                <h3 data="{{ $v->price }}" class="hidden">￥{{ $v->price }}</h3>
                            </div>
                            <div class="quantity-form hidden">
                                <a class="icon_lower"></a>
                                <input type="text" class="input_quantity" value="{{ $v->num }}" readonly="readonly" max="999" data-num="{{ $v->num }}"/>
                                <a class="icon_plus"></a>
                            </div>
                        </li>
                    @endif
                @endforeach
            @endforeach


        </ul>
        {{--<div style="padding:2px 0px 2px 0px;background:#e0e0e0"></div>--}}
    </div>



    <div class="footer_fixed order_fixed clearfix">
        <span><a href="javascript:" class="abtn_gray_large" id="canel">取消 </a></span>
        {{--<span><a href="javascript:" class="abtn_gray_large"  id="end"></a>--}}
        <span>
              <button class="abtn_gray_large" onclick="Replenishment()">支付 (￥<font id="money" class="pay hidden"></font>)</button>
        </span>
    </div>
</div>


<!--设置选择快递地址 start-->
<div id="select_address_container" style="display:none;">
    <div class="select_address_box" id="select_address_mybox">
        <ul class="select_addr_list">
            @if (count($addresses))
                @foreach($addresses as $k=>$address)
                    <li data="{{ $address->id }}" style="width: 85%;" style="border-bottom: none;">
                        <h2><span class="select_addr_name">{{ $address->name }}</span> <span class="select_addr_phone">电话号码：<span class="select_addr_phone_no">{{ $address->phone }}</span></span></h2>
                        {{--<span class='select_addr_label'  style="position: absolute;top:1rem;right:-20px;width:55px;text-align:center;font-size:13px; border:1px solid #e0e0e0;padding:5px 7px 2px 7px;color:#e0e0e0;border-radius: 6px;">{{ $address->label }}</span>--}}
                        <p><b>@if($address->default == 1)[默认地址] @endif</b>{{ $address->area }}</p>
                        <p>{{ $address->address }}</p>
                    </li>
                    <a style="color:#471a8f;float:right;margin-top:-60px;margin-right:10px;border-radius: 6px;background-image: linear-gradient(to bottom, rgba(249,249,249,0.80) 0%, rgba(240,240,240,0.80) 100%);
                    background-image:-webkit-linear-gradient(to bottom, rgba(249,249,249,0.80) 0%, rgba(240,240,240,0.80) 100%);" class="addr_tag edita" data="{{$address}}">编辑</a>
                    <div style="margin-top:10px;border-bottom: 1px solid #e0e0e0; width:100%;"></div>
                @endforeach
            @endif
        </ul>
        <div class="order_addr order_addr_button" onclick="newAddress();">
            <p align="center">添加新地址</p>
            {{--<i class="right_icon"></i>--}}
        </div>
        <div class="order_addr order_addr_button" onclick="goBack();">
            <p align="center">返回</p>
            {{--<i class="right_icon"></i>--}}
        </div>
    </div>
</div>
<!--设置快递地址 start-->
<div id="new_address_container" style="display:none;">
    <div class="address_box">
        <div class="city_select_box">
            {{--<h2>{{ $title }}</h2>--}}
            <div class="select_input_box">
                <input id="area" placeholder="请选择所在地区" areaid="10078 10213 11945" readonly="readonly" class="area_input">
            </div>
        </div>
        <div class="addr_form_box">
            <div class="addr_input_box"><input id="name" name="name" type="text" placeholder="姓名" class="addr_input"/></div>
            <div class="addr_input_box"><input id="phone" name="phone" type="tel" placeholder="联系电话" class="addr_input" /></div>
            {{--<div class="addr_input_box"><input id="zip" name="zip" type="text" placeholder="邮编" class="addr_input" /></div>--}}
            <div class="addr_input_box"><textarea id="address" name="address" rows="2" cols="20" placeholder="详细地址" class="addr_textarea"></textarea></div>
        </div>
        <input id="label" type="hidden" name="label" value="家" />
        {{--<div class="addr_tag_box">--}}
        {{--<h3><input id="label" name="label" type="text" placeholder="地址标签" class="tag_input"/></h3>--}}
        {{--<p class="tag_span">--}}
        {{--<span>家</span>--}}
        {{--<span>公司</span>--}}
        {{--<span>父母</span>--}}
        {{--<span>速递易</span>--}}
        {{--</p>--}}
        {{--</div>--}}
        <div  id="Button1" class="order_addr order_addr_button">
            <p align="center">确认新增</p>
        </div>
        <div class="order_addr order_addr_button" onclick="goBack2();">
            <p align="center">返回</p>
        </div>
    </div>

</div>

<div id="edit_address_container" style="display:none;">
    <div class="address_box">
        <div class="city_select_box">
            {{--<h2>{{ $title }}</h2>--}}
            <div class="select_input_box">
                <input id="edit_area" placeholder="请选择所在地区" areaid="10078 10213 11945" readonly="readonly" class="area_input">
            </div>
        </div>
        <div class="addr_form_box">
            <div class="addr_input_box"><input id="edit_name" name="name" type="text" placeholder="姓名" class="addr_input" value="1"/></div>
            <div class="addr_input_box"><input id="edit_phone" name="phone" type="tel" placeholder="联系电话" class="addr_input" /></div>
            {{--<div class="addr_input_box"><input id="zip" name="zip" type="text" placeholder="邮编" class="addr_input" /></div>--}}
            <div class="addr_input_box"><textarea id="edit_address" name="address" rows="2" cols="20" placeholder="详细地址" class="addr_textarea"></textarea></div>
        </div>
        <input id="edit_label" type="hidden" name="label" value="家" />
        {{--<div class="addr_tag_box">--}}
        {{--<h3><input id="edit_label" name="label" type="text" placeholder="地址标签" class="tag_input"/></h3>--}}
        {{--<p class="tag_span">--}}
        {{--<span>家</span>--}}
        {{--<span>公司</span>--}}
        {{--<span>父母</span>--}}
        {{--<span>速递易</span>--}}
        {{--</p>--}}
        {{--</div>--}}
        <div id="edit_Button1" class="order_addr order_addr_button">
            <p align="center">确认修改</p>
        </div>
        <div class="order_addr order_addr_button" onclick="goBack3();">
            <p align="center">返回</p>
        </div>
    </div>
</div>
<!--去补货效果提示-->
<div class="layerBy">
    <!--补货弹窗start-->
    <div class="alert_box" id="alert_box_1">
        <p class="nopostage_tips hidden">此次订单已达到商家包邮标准，我们会为你保留免邮券，请留意使用有效期</p>
        <p>确认支付吗？</p>
        <div class="alert_op">
            <span><input type="button" value="取消" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="确认" class="abtn_yellow" id="to_pay" /></span>
        </div>
    </div>

    <div class="alert_box" id="alert_box_loading"><p>正在支付...</p></div>
    <!--补货成功弹窗end-->
    <!--补货提交出错弹窗start-->
    {{--<div class="alert_box" id="alert_box_2">--}}
    {{--<p>提交过程出了点小差错......</p>--}}
    {{--<div class="alert_op">--}}
    {{--<span><input type="button" value="联系客服" class="abtn_gray" onclick="$('.layerBy').hide();" /></span>--}}
    {{--<span><input type="button" value="重新提交" class="abtn_yellow" onclick="verify();" /></span>--}}
    {{--</div>--}}
    {{--</div>--}}
    <!--补货提交出错弹窗end-->
    <div class="alert_box" id="alert_box_success">
        <p id="aaa">支付成功</p>
    </div>
    <div class="alert_box" id="alert_box_2">
        <p>确认删除该商品吗？</p>
        <div class="alert_op">
            <span><input type="button" value="取消" class="abtn_gray" id = 'delete_no' onclick="$('.layerBy').hide();" /></span>
            <span><input type="button" value="确认" class="abtn_yellow" id="delete_yes" /></span>
        </div>
    </div>
</div>
<!--去补货效果提示-->

</body>
</html>
<script src="{{ URL::asset('plugins/jQuery/jquery-1.11.1.min.js') }}"></script>
<script src="{{ URL::asset('js/jquery.fly.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('js/zepto.min.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.zepto.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.core.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.scroller.js') }}" type="text/javascript"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.area.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/mobiscroll.scroller.android-ics.js') }}"></script>
<script src="{{ URL::asset('plugins/mobiscoll/i18n/mobiscroll.i18n.zh.js') }}"></script>
<script src="{{ URL::asset('js/jquery.fly.min.js') }}"></script>
<script type="text/javascript">
    var total = new Array();
    var address_id = {{ $detail->address->id or 0 }};
    var platform = null;

    // 获取运费
    var jd_postage,
            tmall_postage,
            yhd_postage,
            jd_postage_default,
            tmall_postage_default,
            yhd_postage_default;
    //判断是否有首次免邮券
    if ($("#noPostage").val() == 1){
        jd_postage = 0;
        tmall_postage = 0;
        yhd_postage = 0;
        jd_postage_default = 0;
        tmall_postage_default = 0;
        yhd_postage_default = 0;
    } else {
        jd_postage = "{{ $detail->postage->JD }}";
        tmall_postage = "{{ $detail->postage->TMALL }}";
        yhd_postage = "{{ $detail->postage->YHD }}";
        jd_postage_default = 6;
        tmall_postage_default = 20;
        yhd_postage_default = 10;
    }

    total['JD'] = moneybegin('JD') + parseFloat(jd_postage);
    total['TMALL'] = moneybegin('TMALL') + parseFloat(tmall_postage);
    total['YHD'] = moneybegin('YHD') + parseFloat(yhd_postage);
    $(document).ready(function(){
        sum_money();//初始化每个平台的总价
        //刷新页面，判断哪个平台有货的数量最多，默认选中数量最多的平台
        checkNumOfProduct();
    });
    //选择平台样式
    $(".order_par_list .par_box").on('touchstart',function(){
        var base = $(this).find('.base');
        $('.base').removeClass('beforeChoose');
        $('.base').removeClass('choose');
        base.addClass('choose');
    });

    $(".order_par_list .par_box").on('touchend', function(){
        var base = $(this).find('.base');
        if (base.hasClass('choose')){
            base.removeClass('choose').addClass('beforeChoose');
        }
        if ($(".order_pro_box").hasClass('hidden')){
            $(".order_pro_box").removeClass('hidden');
        }
        if ($("#money").hasClass('hidden')){
            $("#money").removeClass('hidden');
        }
    });

    //选择平台
    $(".order_par_list .par_box").click(function () {
        platform = $(this).attr("id");
        $(".order_pro_list li").hide();
        $(".order_pro_list li[data-role='" + platform + "']").show();
        $(".par_box").removeClass("on");
        $(this).addClass("on");

        $("#money").text(total[platform].toFixed(2));
        if ($(".order_pro_list li:visible").find("input[type='checkbox']").not("input:checked").length == 0) {
            $(".order_pro input[type='checkbox']").prop("checked", true);
        }
        else {
            $(".order_pro input[type='checkbox']").prop("checked", false);
        }
    });



    // 计算加减钱
    function sum_money(obj = null)
    {
        var money = 0;
        $(".order_pro_list li:visible input[type='checkbox']:checked").parent().parent().each(function () {
            var that = this;
            money = money + (parseFloat($(that).find('h3').attr("data"))*parseFloat($(that).find('.input_quantity').val()));
        });


        // 获取当前是哪个平台
        var platform = $(".par_box.on").attr('id');
        //更新邮费，计算免邮
        if(obj){
            if(platform == 'JD'){
                if(money >= 99){
                    $("#JD").find('b').text(0);
                } else {
                    $("#JD").find('b').text(jd_postage_default);
                }
            }else if(platform == 'TMALL'){
                if(money >= 88 ){
                    $("#TMALL").find('b').text(0);
                } else {
                    $("#TMALL").find('b').text(tmall_postage_default);
                }
            }else{
                if(money >= 68 ){
                    $("#YHD").find('b').text(0);
                } else{
                    $("#YHD").find('b').text(yhd_postage_default);
                }
            }
        }




        if(platform == 'JD'){
            jd_postage = parseFloat($("#JD").find('b').text());
            total_money = parseFloat(jd_postage) + money;
        }else if(platform == 'TMALL'){
            tmall_postage = parseFloat($("#TMALL").find('b').text());
            total_money = parseFloat(tmall_postage) + money;
        }else{
            yhd_postage = parseFloat($("#YHD").find('b').text());
            total_money = parseFloat(yhd_postage) + money;
        }
        $("#money").text(total_money.toFixed(2));

        if(obj)
        {
            var ID=$(obj).parent().parent().attr('data-role');
            $("#"+ID).find(".price").text(money.toFixed(2));
            $("#"+ID).find(".total").text("￥"+total_money.toFixed(2));
        } else{
            //刚进入页面，显示总价
            var initial_total_money_JD = parseFloat(jd_postage) + parseFloat($("#JD").find('.price').text());
            var initial_total_money_TMALL = parseFloat(tmall_postage) + parseFloat($("#TMALL").find('.price').text());
            var initial_total_money_YHD = parseFloat(yhd_postage) + parseFloat($("#YHD").find('.price').text());
            $("#JD").find(".total").text("￥"+initial_total_money_JD.toFixed(2)).attr('initprice', initial_total_money_JD.toFixed(2));
            $("#TMALL").find(".total").text("￥"+initial_total_money_TMALL.toFixed(2)).attr('initprice', initial_total_money_TMALL.toFixed(2));
            $("#YHD").find(".total").text("￥"+initial_total_money_YHD.toFixed(2)).attr('initprice', initial_total_money_YHD.toFixed(2));
        }

        total[ID] =  total_money;
    }

    $(".del_link").click(function () {
        $(".nopostage_tips").addClass('hidden');
        $(".layerBy").show();
        $("#alert_box_2").show();
        var thisLink = $(this);
        var ID=$(this).parent().attr('data-role');
        deleteProduct(ID, thisLink)
    });



    $(".quantity-form .icon_lower").click(function () {
        var num = parseInt($(this).next(".input_quantity").val());
        var minNum = $(this).next(".input_quantity").attr('data-num');
        if (num > minNum) {
            $(this).next(".input_quantity").val(num - 1);
        }
        sum_money(this);
    });
    $(".quantity-form .icon_plus").click(function () {
        var num =parseInt($(this).prev(".input_quantity").val());
        var max = parseInt($(this).prev(".input_quantity").attr("max"));
        if (num<max) {
            $(this).prev(".input_quantity").val(num + 1);
        }
        sum_money(this);
    });

    //全选效果
    //    $(".order_pro input[type='checkbox']").click(function () {
    //        var name = $(this).attr("name");
    //        $(".order_pro_list input[name='" + name + "']").prop('checked', $(this).prop('checked'));
    //        if ($(this).is(":checked")) {
    //            var money =0;
    //            $(".order_pro_list li:visible h3").each(function () {
    //                money=money+parseFloat($(this).attr("data"));
    //            });
    //            $("#money").text(money.toFixed(2));
    //        }
    //        else {
    //            $("#money").text(0);
    //        }
    //        if ($(".par_box.on").length == 1) {
    //            $(".par_box.on").find("p").text("￥" + $("#money").text());
    //        }
    //    });

    function checkNumOfProduct(){
        var JD = 0;
        var YHD = 0;
        var TMALL = 0;

        $(".order_pro_list li").each(function(){
            var noProdutText = $(this).find('span').text();
            var noSpecific = $(this).children('div').find('span').text();
            if ($(this).attr('data-role') == 'JD'){
                if ( noProdutText == '平台无货' || noSpecific == '商品无法识别'){
                    JD++;
                }
            }
            if ($(this).attr('data-role') == 'YHD'){
                if ( noProdutText == '平台无货' || noSpecific == '商品无法识别'){
                    YHD++;
                }
            }
            if ($(this).attr('data-role') == 'TMALL'){
                if ( noProdutText == '平台无货' || noSpecific == '商品无法识别'){
                    TMALL++;
                }
            }
        });

        var jdInitPrice = parseFloat($('#JD').find('.total').attr('initprice'));
        var yhdInitPrice = parseFloat($('#YHD').find('.total').attr('initprice'));
        var tamllInitPrice = parseFloat($('#TMALL').find('.total').attr('initprice'));

        var list = [JD, YHD, TMALL];
        var min = Math.min.apply(Math, list);
        var idName = '';

        var priceList = [jdInitPrice, yhdInitPrice, tamllInitPrice];
        var minPrice = Math.min.apply(Math, priceList);
        //如果商品数量都不相等，则以数量最多的默认显示（本代码min表示无货和无法识别的数量）
        if (JD != YHD && JD != TMALL && YHD != TMALL){
            if (min == JD) idName = 'JD';
            if (min == YHD) idName = 'YHD';
            if (min == TMALL) idName = 'TMALL';
        } else if (JD == YHD && JD == TMALL){
            //如果三大平台数量全部一样，则显示价格最低的
                if (minPrice == jdInitPrice) idName = 'JD';
                if (minPrice == yhdInitPrice) idName = 'YHD';
                if (minPrice == tamllInitPrice) idName = 'TMALL';
        } else {
        //如果三大平台有两大平台一样
            if (JD == YHD) {
                if (JD < TMALL) {
                    //如果京东一号店有效商品数量多于天猫
                    if (jdInitPrice < yhdInitPrice) {
                        idName = 'JD';
                    } else {
                        idName = 'YHD';
                    }
                } else {
                    idName = 'TMALL';
                }
            }

            if (JD == TMALL) {
                //如果三大平台有两大平台一样
                if (JD < YHD) {
                    //如果京东天猫有效商品数量多于一号店
                    if (jdInitPrice < tamllInitPrice) {
                        idName = 'JD';
                    } else {
                        idName = 'TMALL';
                    }
                } else {
                    idName = 'YHD';
                }
            }

            if (YHD == TMALL) {
                //如果三大平台有两大平台一样
                if (YHD < JD) {
                    //如果天猫一号店有效商品数量多于京东
                    if (yhdInitPrice < tamllInitPrice) {
                        idName = 'YHD';
                    } else {
                        idName = 'TMALL';
                    }
                } else {
                    idName = 'JD';
                }
            }
        }



        $(".order_par_list .par_box").each(function(){
           if ($(this).attr('id') == idName){
               platform = idName;
               //如果没有有效产品，则去支付价格为0
               var productTotalPrice = $('#'+platform).find('.price').text();

               $(".order_pro_list li").hide();
               $(".order_pro_list li[data-role='" + platform + "']").show();
               $(".par_box").removeClass("on");
               $(this).addClass("on");
               $(this).find('.base').addClass('beforeChoose');
               if (productTotalPrice > 0){
                   $("#money").removeClass('hidden').text(total[platform].toFixed(2));
               } else {
                   $("#money").removeClass('hidden').text('0.00');
               }


               if ($(".order_pro_list li:visible").find("input[type='checkbox']").not("input:checked").length == 0) {
                   $(".order_pro input[type='checkbox']").prop("checked", true);
               }
               else {
                   $(".order_pro input[type='checkbox']").prop("checked", false);
               }
           };
        });

    }





    function deleteProduct(ID, thisLink){
        $("#delete_yes").unbind('click');
        $("#delete_yes").click(function(){
            var money = 0;
            $(".layerBy").hide();
            $(thisLink).parent().remove();
            $(".order_pro_list li:visible input[type='checkbox']:checked").parent().parent().each(function () {
                var that = this;
                money = money + (parseFloat($(that).find('h3').attr("data"))*parseFloat($(that).find('.input_quantity').val()));
            });

            // 获取当前是哪个平台
            var platform = $(".par_box.on").attr('id');
            var total_money = 0;
            if(platform == 'JD'){
                if(money > 99){
                    $("#JD").find('b').text(0);
                } else {
                    $("#JD").find('b').text(jd_postage_default);
                }
                jd_postage = parseFloat($("#JD").find('b').text());
                total_money = parseFloat(jd_postage) + money;
            }else if(platform == 'TMALL'){
                if(money > 88 ){
                    $("#TMALL").find('b').text(0);
                } else {
                    $("#TMALL").find('b').text(tmall_postage_default);
                }
                tmall_postage = parseFloat($("#TMALL").find('b').text());
                total_money = parseFloat(tmall_postage) + money;
            }else{
                if(money > 68 ){
                    $("#YHD").find('b').text(0);
                } else{
                    $("#YHD").find('b').text(yhd_postage_default);
                }
                yhd_postage = parseFloat($("#YHD").find('b').text());
                total_money = parseFloat(yhd_postage) + money;
            }
            if (money.toFixed(2) > 0){
                $("#money").text(total_money.toFixed(2));
            } else {
                $("#money").text('0.00');
            }

            $("#"+ID).find(".price").text(money.toFixed(2));
            $("#"+ID).find(".total").text("￥"+total_money.toFixed(2));
            total[ID] =  total_money;
        });
    }



    function moneybegin(ID) {
        var money = 0;
        $("li[data-role='" + ID + "']").each(function () {
            var that = this;
            money = money + (parseFloat($(that).find('h3').attr("data"))*parseFloat($(that).find('.input_quantity').val()));
            $("#"+ID).find(".price").text(money.toFixed(2));
        });
        return money;
    }
    var min = Math.min(total['JD'],total['TMALL'],total['YHD']);
    $("#money").text(min.toFixed(2));
    @if($detail->platform != '')
        $("#{{ $detail->platform }}").trigger("click");
    @else
    if(total['JD'] == min)
    {
        $("#JD").trigger("click");
    }
    else if(total['TMALL'] == min)
    {
        $("#TMALL").trigger("click");

    }
    else if(total['YHD'] == min)
    {
        $("#YHD").trigger("click");
    }
    @endif
    //点击选择框添加购物车效果
    $(".order_pro_list input[type='checkbox']").click(function () {
        var this_money = parseFloat($(this).parent().parent().find("h3").attr("data"));
        var total_money = parseFloat($("#money").text());
        if ($(this).is(":checked")) {
            addCart($(this).parent());
            $("#money").text((total_money + this_money).toFixed(2));
            if ($(".order_pro_list li:visible").find("input[type='checkbox']").not("input:checked").length == 0) {
                $(".order_pro input[type='checkbox']").prop('checked', $(this).prop('checked'));
            }
        }
        if (!$(this).is(":checked")) {
            $("#money").text((total_money - this_money).toFixed(2));
            if ($(".order_pro_list li:visible").find("input[type='checkbox']").not("input:checked").length>0) {
                $(".order_pro input[type='checkbox']").prop('checked', $(this).prop('checked'));
            }
        }
        if ($(".par_box.on").length == 1) {
            var box = $(this).parent().parent().attr("data-role");
            $("#" + box).find("p").text("￥" + $("#money").text());
        }

    });


    //选择地址 start
    function selectAddress() {
        $("#step01").hide();
        $("#select_address_container").show();
    }
    function newAddress() {
        $("#step01").hide();
        $("#select_address_mybox").hide();
        $("#new_address_container").show();
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
                url: '/address/create',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    detailid : {{ $detail->id }},
                    userid: {{ $currentUser->id }},
                    name: $("#name").val(),
                    phone: $("#phone").val(),
                    zip: '',
                    address: $("#address").val(),
                    label: $("#label").val(),
                    area: $("#area").val(),
                    areaid: $("#area").attr("areaid")
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
                        window.location.href=updateUrl(window.location.href);
                    }
                },
                error: function(xhr, type){

                }
            });
        });
    }
    $(".select_addr_list li").click(function () {
        var address_id = $(this).attr('data');
        var name = $(this).find("h2 .select_addr_name").text();
        var phoneNo = $(this).find(".select_addr_phone_no").text();
        $(".order_addr h2").html(name + "<span>" + phoneNo + "</span>");
        $(".order_addr p:eq(0)").html($(this).find("p:eq(0)").text());
        $(".order_addr p:eq(1)").html($(this).find("p:eq(1)").text());
//        $(".order_addr .addr_tag").html($(this).find(".select_addr_label").text());
        $("#step01").show();
        $("#select_address_container").hide();
    });

    $(".edita").click(function () {

        var addrs = $(this).attr('data');
        var addrsss=JSON.parse(addrs);
        console.log(addrsss.label);
        $("#edit_name").val(addrsss.name);
        $("#edit_phone").val(addrsss.phone);
        $("#edit_address").val(addrsss.address);
        $("#edit_area").val(addrsss.area);
        $("#edit_area").attr('areaid',addrsss.areaid);
        $("#edit_label").val(addrsss.label);

        $("#select_address_mybox").hide();
        $("#edit_address_container").show();
        $('#edit_area').scroller('destroy').scroller({ preset: 'area', theme: 'android-ics light', display: 'bottom', valueo: addrsss.areaid });
        $(".tag_span span").click(function () {
            $(".tag_span span").removeClass("aon");
            $(this).addClass("aon");
            $("#label").val($(this).text());
        });
        var id = addrsss.id;
        $('#edit_Button1').click(function(){
            var area = $("#edit_area").val();
            if(area == ''){
                $("#edit_area").focus();
                $("#edit_area").attr('placeholder',"地区不能为空");
                return ;
            }
            var name = $("#edit_name").val();
            if(name == ''){
                $("#edit_name").focus();
                $("#edit_name").attr('placeholder',"姓名不能为空");
                return ;
            }
            var phone = $("#edit_phone").val();
            if(phone == ''){
                $("#edit_phone").focus();
                $("#edit_phone").attr('placeholder',"手机号不能为空");
                return ;
            }
            var address = $("#edit_address").val();
            if(address == ''){
                $("#edit_address").focus();
                $("#edit_address").attr('placeholder',"地址不能为空");
                return ;
            }
            $.ajax({
                type: 'GET',
                url: '/address/update/' + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    detailid : {{ $detail->id }},
                    userid: {{ $currentUser->id }},
                    name: $("#edit_name").val(),
                    phone: $("#edit_phone").val(),
                    zip: '',
                    address: $("#edit_address").val(),
                    label: addrsss.label,
                    area: $("#edit_area").val(),
                    areaid: $("#edit_area").attr("areaid")
                },
                // type of data we are expecting in return:
                dataType: 'json',
                timeout: 300,
                context: $('body'),
                success: function(data){
                    if(data.retcode == 0)
                    {
                        window.location.href=updateUrl(window.location.href);
                    }
                },
                error: function(xhr, type){

                }
            });
        });

    });
    //选择地址 end

    $(function () {
        var opt = {
            'default': {
                theme: 'default',
                mode: 'scroller',
                display: 'bottom',
                animate: 'fade'
            },
            'select': {
                preset: 'select'
            }
        };
        $('.demo-test-select').scroller($.extend(opt['select'], opt['default']));

        //编辑
        $(".invoice_input_box span").click(function () {
            edit($(this));
        });
        //保存
        $(".invoice_input_box strong").click(function () {
            save($(this));
        });
        //删除
        $(".invoice_input_box b").click(function () {
            del($(this));
        });
        $(".invoice_input_box i").click(function () {
            set($(this));
        });
    });
    function edit(obj) {
        obj.parent().find(".inv_input").removeAttr("disabled");
        obj.parent().find(".inv_input").focus();
        obj.hide();
        obj.parent().find("b,i").hide();
        obj.parent().find("strong").show();
    }

    $('#canel').click(function () {
        WeixinJSBridge.call('closeWindow');
    });

    // 弹框
    function Replenishment() {
        if ($(".pay").hasClass('hidden')){
            return false;
        }
        var money = 0;
        $(".order_pro_list li:visible input[type='checkbox']:checked").parent().parent().each(function () {
            var that = this;
            money = (parseFloat($(that).find('h3').attr("data"))*parseFloat($(that).find('.input_quantity').val()));
        });

        if(money == 0){
            $('.layerBy').show();
            $('.alert_box').hide();
            $("#aaa").html('当前商品都已售罄');
            $("#alert_box_success").show();
            setTimeout(function () {
                $('.layerBy').hide();
                $('.alert_box').hide();
            }, 1500);
            return ;
        }
        if(address_id == 0){
            $('.layerBy').show();
            $('.alert_box').hide();
            $("#aaa").html('请选择收货人地址');
            $("#alert_box_success").show();
            setTimeout(function () {
                $('.layerBy').hide();
                $('.alert_box').hide();
            }, 1500);
            return ;
        }
        if ($("#noPostage").val() == '1'){
            var platform = $(".par_box.on").attr('id');
            console.log(total[platform]+platform);
            if ((platform == 'JD' && total[platform] >= 99) || (platform == 'TMALL' && total[platform] >= 88) || (platform == 'YHD' && total[platform] >= 99)) {
                $(".nopostage_tips").removeClass('hidden');
            } else {
                $(".nopostage_tips").addClass('hidden');
            }
        }
        $('.layerBy').show();
        $("#alert_box_1").show();
        $("#alert_box_2").hide();
    }


    function updateUrl(url,key){
        var key= (key || 't') +'=';  //默认是"t"
        var reg=new RegExp(key+'\\d+');  //正则：t=1472286066028
        var timestamp=+new Date();
        if(url.indexOf(key)>-1){ //有时间戳，直接更新
            return url.replace(reg,key+timestamp);
        }else{  //没有时间戳，加上时间戳
            if(url.indexOf('\?')>-1){
                var urlArr=url.split('\?');
                if(urlArr[1]){
                    return urlArr[0]+'?'+key+timestamp+'&'+urlArr[1];
                }else{
                    return urlArr[0]+'?'+key+timestamp;
                }
            }else{
                if(url.indexOf('#')>-1){
                    return url.split('#')[0]+'?'+key+timestamp+location.hash;
                }else{
                    return url+'?'+key+timestamp;
                }
            }
        }
    }

    //添加或选择地址的页面返回按钮
    function goBack(){
        $("#step01").show();
        $("#select_address_container").hide();
    };


    function goBack2(){
        $("#step01").show();
        $("#select_address_mybox").show();
        $("#new_address_container").hide();
    }

    function goBack3(){
        $("#step01").hide();
        $("#select_address_mybox").show();
        $("#edit_address_container").hide();
    }
    //    $('#end').click(function () {
    //        var products = {};
    //        products['JD'] = new Array();
    //        products['TMALL'] = new Array();
    //        products['YHD'] = new Array();
    //        $("li[data-role='JD']").each(function(i,e){
    //            var tmp = {};
    //            var that = $(e).find("input");
    //            tmp['id'] = $(that[0]).attr('data');
    //            tmp['num'] = $(that[1]).val();
    //            products['JD'].push(tmp);
    //        });
    //        $("li[data-role='TMALL']").each(function(i,e){
    //            var tmp = {};
    //            var that = $(e).find("input");
    //            tmp['id'] = $(that[0]).attr('data');
    //            tmp['num'] = $(that[1]).val();
    //            products['TMALL'].push(tmp);
    //        });
    //        $("li[data-role='YHD']").each(function(i,e){
    //            var tmp = {};
    //            var that = $(e).find("input");
    //            tmp['id'] = parseInt($(that[0]).attr('data'));
    //            tmp['num'] = parseInt($(that[1]).val());
    //            products['YHD'].push(tmp);
    //        });
    //        $(".order_pro_list li:visible input[type='checkbox']:checked").parent().parent().each(function () {
    //            var that = this;
    //            money = money + (parseFloat($(that).find('h3').attr("data"))*parseFloat($(that).find('.input_quantity').val()));
    //        });

    {{--$.ajax({--}}
    {{--type: 'post',--}}
    {{--url: '{{ url('/detail/submit') }}',--}}
    {{--headers: {--}}
    {{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--},--}}
    {{--data: {--}}
    {{--uuid: '{{ $detail->uuid }}',--}}
    {{--address_id: address_id,--}}
    {{--platform:platform,--}}
    {{--product:JSON.stringify(products)--}}
    {{--},--}}
    {{--// type of data we are expecting in return:--}}
    {{--dataType: 'json',--}}

    {{--success: function(data){--}}
    {{--if(data.retcode == 0)--}}
    {{--{--}}
    {{--$('.layerBy').show();--}}
    {{--$("#alert_box_1").show();--}}
    {{--$("#alert_box_2").hide();--}}
    {{--setTimeout(function () {--}}
    {{--location.href = '/stock';--}}
    {{--},2000);--}}
    {{--}--}}
    {{--},--}}
    {{--error: function(xhr, type){--}}
    {{--}--}}
    {{--});--}}
    //    });
</script>

{{--支付--}}
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
    wx.config({
        debug: {{ $debug }},
        appId: '{{ $appId }}',
        timestamp: '{{ $timestamp }}',
        nonceStr: '{{ $nonceStr }}',
        signature: '{{ $signature }}',
        url: '{{ $url }}',
        jsApiList: {!! $jsApiList !!}
    });
    wx.ready(function () {
        // 在这里调用 API
        $("#to_pay").click(function () {
            $("#alert_box_1").hide();

            var products = {};
            products['JD'] = new Array();
            products['TMALL'] = new Array();
            products['YHD'] = new Array();
            $("li[data-role='JD']").each(function(i,e){
                var tmp = {};
                var that = $(e).find("input");
                tmp['id'] = $(that[0]).attr('data');
                tmp['num'] = $(that[1]).val();
                products['JD'].push(tmp);
            });
            $("li[data-role='TMALL']").each(function(i,e){
                var tmp = {};
                var that = $(e).find("input");
                tmp['id'] = $(that[0]).attr('data');
                tmp['num'] = $(that[1]).val();
                products['TMALL'].push(tmp);
            });
            $("li[data-role='YHD']").each(function(i,e){
                var tmp = {};
                var that = $(e).find("input");
                tmp['id'] = parseInt($(that[0]).attr('data'));
                tmp['num'] = parseInt($(that[1]).val());
                products['YHD'].push(tmp);
            });
            $(".order_pro_list li:visible input[type='checkbox']:checked").parent().parent().each(function () {
                var that = this;
                money = money + (parseFloat($(that).find('h3').attr("data"))*parseFloat($(that).find('.input_quantity').val()));
            });

            $.ajax({
                type: 'POST',
                url: '/pay/order/{{ $detail->uuid }}',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    uuid: '{{ $detail->uuid }}',
                    address_id: address_id,
                    platform:platform,
                    product:JSON.stringify(products)
                },
                dataType: 'json',
                timeout: 3000,
                context: $('body'),
                beforeSend: function(){
                    $('#alert_box_loading').show();
                },
                success: function(data){
                    wx.chooseWXPay({
                        timestamp: data.timestamp,
                        nonceStr: data.nonceStr,
                        package: data.package,
                        signType: data.signType,
                        paySign: data.paySign,
                        success: function (res) {
                            WeixinJSBridge.call('closeWindow');
                        },
                        cancel:function(res){
                            //支付取消
                            $("#aaa").html('已取消支付');
                            $("#alert_box_success").show();
                            $('#alert_box_loading').hide();
                            setTimeout(function () {
                                location.href = '/order';
                            },1000);
                        },
                        fail:function(res){
                            //支付失败
                            $("#aaa").html('支付失败');
                            $("#alert_box_success").show();
                            $('#alert_box_loading').hide();
                            setTimeout(function () {
                                location.href = '/order';
                            },1000);
                        }
                    });
                },
                error: function(xhr, type){
                    console.log(xhr);
                }
            });
        });
    });

</script>

