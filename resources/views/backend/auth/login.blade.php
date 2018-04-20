<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>登录</title>

    <!-- Fonts -->
    {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" >--}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link href="{{ URL::asset('/') }}src/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

</head>
<body id="app-layout">
<nav class="navbar navbar-blue navbar-static-top">

</nav>

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">刚好后台登录 </div>
                <div class="alert">
                    <span></span>
                </div>
                <div class="panel-body">

                    <form class="form-horizontal" role="form" id="login_form">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="email" class="col-md-4 control-label">用户</label>

                            <div class="col-md-6">
                                <input  class="form-control" name="username" >

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<div class="col-md-6 col-md-offset-4">--}}
                                {{--<div class="checkbox">--}}
                                    {{--<label>--}}
                                        {{--<input type="checkbox" name="remember"> Remember Me--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </form>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button  class="btn btn-primary" id="submit">
                                <i class="fa fa-btn fa-sign-in"></i> 登录
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ URL::asset('/src/vendor/jquery/jquery-2.2.3.min.js') }}" ></script>
<script>
    $(document).ready(function(){
        $(function(){
            document.onkeydown = function(e){
                if(window.event.keyCode==13) {
                    $.ajax({
                        type: "POST",
                        url: '{{ url('/admin/doLogin') }}',
                        dataType: 'json',
                        cache: false,
                        data:  $('#login_form').serialize(),
                        success: function(result) {
                            console.log(result);

                            if(result.status != 200){
                                $('.alert').addClass('alert-danger');
                                $('.alert span').html(result.message);
                            }else{
                                // 跳转链接
                                location.href = "index";
                            }

                        }
                    });
                }
            }
        });
        $('#submit').click(function(){
            // ajax 提交表单
            $.ajax({
                type: "POST",
                url: '{{ url('/admin/doLogin') }}',
                dataType: 'json',
                cache: false,
                data:  $('#login_form').serialize(),
                success: function(result) {
                    console.log(result);

                    if(result.status != 200){
                        $('.alert').addClass('alert-danger');
                        $('.alert span').html(result.message);
                    }else{
                        // 跳转链接
                        location.href = "index";
                    }

                }
            });

        });
    });
</script>
</body>
</html>
