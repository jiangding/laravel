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
                <li>品类管理</li>
                <li class="active">修改品类</li>
            </ol>
            <div class="panel-body">
                <form id="userForm" class="form-horizontal" role="form" method="POST" action="{{ url('/admin/catalog/update') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $json->id }}">
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">品类代码</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="shortcode" value="{{ $json->shortcode }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">品类名</label>
                        <div class="col-md-5">
                            <input type="name" class="form-control" name="name" value="{{ $json->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">关键词</label>
                        <div class="col-sm-5">
                            <textarea rows="15" class="form-control" name="keyword" value="">{{ $json->keyword }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">图片</label>
                        <div class="col-sm-5">
                            <input id="pic" type="file" name="pic" class="layui-upload-file">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <button id="submit" type="submit" class="btn btn-primary">提交</button>
                            <a href="{{ url('admin/catalog') }}" type="button" class="btn btn-default" >返回</a>
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
                url: '{{ url('/admin/catalog/update') }}',
                dataType: 'json',
                cache: false,
                data:  $form.serialize(),
                success: function(result) {
                        // 跳转链接
                    window.location.href = "/admin/catalog";

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
                shortcode: {
                    validators: {
                        notEmpty: {
                            message: '品类代号不能为空~'
                        },
                    }
                },

                name: {
                    validators: {
                        notEmpty: {
                            message: '品类名不能为空~'
                        },
                    }
                },
                keyword: {
                    validators: {
                        notEmpty: {
                            message: '关键字不能为空~'
                        },
                    }
                },
            }
        });
});
</script>
@endsection
