@include('frontend.Layouts.header')
<body>
<div class="register_box">
    <div class="register_title">{{ $title }}</div>
    <div class="register_Form">
        <div class="input_box"><input id="wname" type="text" placeholder="{{ $currentUser->nickname }}" class="input_full" readonly="readonly" /></div>
        <div class="input_box_phone">
            <input id="mobile" name="mobile" type="text" placeholder="请输入手机号码" class="input_full" value="{{ $currentUser->mobile or '' }}" />
            <span><input  type="button" value="获取验证码" class="btn_code" onclick="settime(this)" /></span>
        </div>
        <div class="input_box"><input id="verifyCode" name="verifyCode" type="text" placeholder="请输入验证码" class="input_full" /></div>
    </div>
    <div class="register_op"><input id="Verify_btn" type="button" value="验证并注册" class="Verify_btn" disabled="disabled"   /></div>
    <p class="register_tips">只需要几秒钟，就可以拥有你的个人购物助理</p>
</div>

<div class="layerBy">
    <div class="alert_tips">验证失败，<br />请核对手机号和验证码</div>
</div>
@include('frontend.Layouts.js')
<script src="{{ URL::asset('js/laravel-sms.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $('#mobile').mobiscroll().numpad({
        theme: 'ios',
        lang: 'zh',
        template: 'ddddddddddd',
        allowLeadingZero: true,
        placeholder: '-',
        validate: function (event, inst) {
            return {
                invalid: event.values.length != 11
            };
        }
    });
    $('#verifyCode').mobiscroll().numpad({
        theme: 'ios',
        lang: 'zh',
        template: 'dddd',
        allowLeadingZero: true,
        placeholder: '-',
        validate: function (event, inst) {
            return {
                invalid: event.values.length != 4
            };
        }
    });
    $(".btn_code").click(function () {
        $("#Verify_btn").removeAttr("disabled");
        // ajax 请求发送
        $.ajax({
            type: 'POST',
            url: '/user/sms',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                mobile: $("#mobile").val(),
            },
            dataType: 'json',
            timeout: 300,
            context: $('body'),
            success: function(data){

            },
            error: function(xhr, type){

            }
        });
    });

    var countdown=60;
    function settime(obj) {
        if (countdown == 0) {
            obj.removeAttribute("disabled");
            obj.value="获取验证码";
            countdown = 60;
            return;
        } else {
            obj.setAttribute("disabled", true);
            obj.value= countdown + " 秒后再次发送";
            countdown--;
        }
        setTimeout(function() {
             settime(obj) }
        ,1000)
    }

    //    $('.btn_code').sms({
//        //laravel csrf token
//        token       : $('meta[name="csrf-token"]').attr('content'),
//        //请求间隔时间
//        interval    : 60,
//        //请求参数
//        requestData : {
//            //手机号
//            mobile : function () {
//                return $('#mobile').val();
//            },
//            //手机号的检测规则
//            mobile_rule : 'check_mobile_unique|check_mobile_exists'
//        },
//        alertMsg    : function (msg, type) {
//            console.debug(type);
//            $("#Verify_btn").attr('disabled','disable');
//        }
//    });
    $('#Verify_btn').click(function () {
        $.ajax({
            type: 'POST',
            url: '/user/register',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                mobile: $("#mobile").val(),
                verifyCode: $("#verifyCode").val()
            },
            dataType: 'json',
            timeout: 300,
            context: $('body'),
            success: function(data){
                if(data.retcode == 0)
                {
                    location.href = '/user/info';
                }
                else
                {
                    $('.layerBy').show();
                    $(".alert_tips").show();
                    setTimeout(function () {
                        $('.layerBy').hide();
                        $(".alert_tips").hide();
                    },1000);
                }
            },
            error: function(xhr, type){
                $('.layerBy').show();
                $(".alert_tips").show();
                setTimeout(function () {
                    $('.layerBy').hide();
                    $(".alert_tips").hide();
                },1000);
            }
        });
    });
</script>
@include('frontend.Layouts.footer')
