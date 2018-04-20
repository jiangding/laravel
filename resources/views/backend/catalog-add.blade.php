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
                <li class="active">添加品类</li>
            </ol>
            <div class="panel-body">
                <form id="userForm" class="form-horizontal" role="form" method="POST" action="{{ url('/admin/catalog/toAdd') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">品类代码</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="shortcode" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">品类名</label>
                        <div class="col-md-5">
                            <input type="name" class="form-control" name="name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">关键词</label>
                        <div class="col-sm-5">
                            <textarea rows="10" class="form-control" name="keyword" value=""></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">图片</label>
                        <div class="col-sm-5">
                            <img id="pic" src="" />
                            <input type="file" name="upload" class="layui-upload-file">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">添加</button>
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
    layui.use(['upload'], function() {
        layui.upload({
            url: '{{ url('admin/upload') }}',
            ext: 'jpg|png|gif',
            before:function (input) {
                console.log('上传中...');
            }
            ,success: function(res){
                console.log(res);
                $('#pic').attr('src', "{{ URL::asset('/') }}" + res.path);
            }
        });
    });


//    $('#userForm').bootstrapValidator({
//            fields: {
//                shortcode: {
//                    validators: {
//                        notEmpty: {
//                            message: '品类代号不能为空~'
//                        },
//                    }
//                },
//
//                name: {
//                    validators: {
//                        notEmpty: {
//                            message: '品类名不能为空~'
//                        },
//                    }
//                },
//                keyword: {
//                    validators: {
//                        notEmpty: {
//                            message: '关键字不能为空~'
//                        },
//                    }
//                },
//            }
//        });
});
</script>
@endsection
