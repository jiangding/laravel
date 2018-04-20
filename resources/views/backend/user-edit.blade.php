@extends('backend.layouts.master')
@section('content')

<link rel="stylesheet" href="{{ URL::asset('/') }}src/dist/css/bootstrapValidator.min.css"/>
<script type="text/javascript" src="{{ URL::asset('/') }}src/dist/js/bootstrapValidator.min.js"></script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel">
            </div>
            <ol class="breadcrumb">
                <li>首页</li>
                <li>用户管理</li>
                <li class="active">修改 {{ $json->nickname }} 信息</li>
            </ol>
            <div class="panel-body">

                <div class="alert alert-danger" style="display: none">
                    <a href="#" class="close" data-dismiss="alert">
                        &times;
                    </a>
                    <span></span>
                </div>

                <form id="userForm" class="form-horizontal" role="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $json->id }}">
                    <div class="form-group">
                        <label for="email" class="col-md-2 control-label">Email</label>
                        <div class="col-md-5">
                            <input id="email" class="form-control" name="email" value="{{ $json->email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">自编码</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="selfcode" value="{{ $json->selfcode }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-md-2 control-label">姓名</label>
                        <div class="col-md-5">
                            <input id="email" type="name" class="form-control" name="name" value="{{ $json->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label">昵称</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="nickname" value="{{ $json->nickname }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label">手机号</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="mobile" value="{{ $json->mobile }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label">生日</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="birthday"
                                   value="{{ $json->birthday }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label">星座</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="constellation"
                                   value="{{ $json->constellation }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label">地区</label>
                        <div class="col-sm-5">
                            <input disabled type="text" class="form-control" name="area"
                                   value="{{ $json->country.$json->province.$json->city }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label">年龄</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="age"
                                   value="{{ $json->age }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label">提醒周期</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="remind"
                                   value="{{ $json->remind }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <button id="submit" type="submit" class="btn btn-primary">提交</button>
                            <a href="{{ url('admin/user') }}" type="button" class="btn btn-default" >返回</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#submit').click(function() {
            //开启验证
            $('#userForm').data('bootstrapValidator').validate();
            if(!$('#userForm').data('bootstrapValidator').isValid()){
                return ;
            }else{

                var $form = $('#userForm');
                console.log($form.serialize());
                // ajax 提交表单
                $.ajax({
                    type: "POST",
                    url: '{{ url('/admin/user/update') }}',
                    dataType: 'json',
                    cache: false,
                    data:  $form.serialize(),
                    success: function(result) {
                        if(result == null){
                            $('.alert-danger').show();
                            $('.alert-danger span').html('未知错误!');
                            setTimeout(function() {$('.alert-danger').hide();}, 5000);
                            return;
                        }

                        if(result.status != 200){
                            $('.alert-danger').show();
                            $('.alert-danger span').html(result.message);
                            setTimeout(function() {$('.alert-danger').hide();}, 5000);
                            return;
                        }else{
                            // 跳转链接
                            window.history.back();

                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                    }
                });

            }
        });
        $('#userForm').bootstrapValidator({
            fields: {
                email: {
                    validators: {
                        notEmpty: {
                            message: '邮箱不能为空~'
                        },
                        emailAddress: {
                            message: '请输入有效的邮箱地址'
                        }
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: '姓名不能为空~'
                        },
                    }
                },
                selfcode: {
                    validators: {
                        notEmpty: {
                            message: '自编码不能为空~'
                        },
                    }
                },
                nickname: {
                    validators: {
                        notEmpty: {
                            message: '昵称不能为空~'
                        },
                    }
                },
                name: {
                    validators: {
                        notEmpty: {
                            message: '姓名不能为空~'
                        },
                    }
                },
                mobile: {
                    validators: {
                        notEmpty: {
                            message: '手机不能为空'
                        },
                        phone: {
                            message: '请输入有效的手机号',
                            country: 'CN'
                        }
                    }
                },
                age: {
                    validators: {
                        lessThan: {
                            value: 100,
                            inclusive: true,
                            message: '年龄必须小于100岁'
                        },
                        greaterThan: {
                            value: 10,
                            inclusive: false,
                            message: '必须大于10岁'
                        }
                    }
                },
                remind: {
                    validators: {
                        notEmpty: {
                            message: '提醒周期不能为空~'
                        },
                        numeric: {
                            message: '提醒周期只能是数字',
                        }
                    }
                },
            }
        });
    });
</script>
@endsection