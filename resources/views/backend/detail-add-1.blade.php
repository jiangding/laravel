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
                <li>清单列表</li>
                <li class="active">添加清单</li>
            </ol>
            <div class="panel-body">
                <form id="userForm" class="form-horizontal" role="form">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="firstname" class="col-sm-2 control-label">清单号</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="uuid" value="{{ $uuid }}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">用户</label>
                        <div class="col-md-3">
                            <input class="form-control" name="userid" placeholder="用户id" value="" style="width:200px;" >                      </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">产品条码</label>
                        <div class="col-md-3">
                            <input class="form-control" name="barcode[]" placeholder="请输入条码" value="" style="width:200px;" >                      </div>
                    </div>
                    <div class="form-group" id="click">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-3">
                            <a onclick="add_input()" style="text-decoration: none;cursor: pointer" class="fa fa-2x fa-plus-square"></a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <button id="submit" type="submit" class="btn btn-primary">添加</button>
                            <a href="{{ url('admin/detail') }}" type="button" class="btn btn-default" >返回</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function add_input()
{
    var p = $('#click');
    var id = parseInt(10000*Math.random());
    p.before('<div class="form-group" id= '+id+'><label class="col-md-2 control-label">产品条码</label><div class="col-md-3"><input class="form-control" name="barcode[]" placeholder="请输入条码" value=""  style="width:200px;display: inline"><a onclick="delete_input('+id+')" style="text-decoration: none;cursor: pointer;padding-left:5px" class="fa fa-minus-square"></a></div></div>');
}

function delete_input(id)
{
    $("#"+id).remove();
}
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
                url: '{{ url('/admin/detail/update') }}',
                dataType: 'json',
                cache: false,
                data:  $form.serialize(),
                success: function(result) {
                    // 跳转链接
                    window.location.href = "/admin/detail";

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
                userid: {
                    validators: {
                        notEmpty: {
                            message: '用户id不能为空~'
                        },
                        numeric: {
                            message: 'id只能是数字'
                        }
                    }
                }
            }
        });
});
</script>
@endsection
