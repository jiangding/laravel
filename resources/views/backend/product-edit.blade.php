@extends('backend.layouts.master')
@section('content')

<link rel="stylesheet" href="{{ URL::asset('/') }}src/dist/css/bootstrapValidator.min.css"/>
<script type="text/javascript" src="{{ URL::asset('/') }}src/dist/js/bootstrapValidator.min.js"></script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                    </div><!-- /.modal-content -->
                </div><!-- /.modal -->
            </div>
            <div class="panel">
            </div>
            <ol class="breadcrumb">
                <li>首页</li>
                <li>产品管理</li>
                <li class="active">修改产品</li>
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
                    <input type="hidden" name="id" value="{{ $row->id }}">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Barcode</label>
                        <div class="col-md-5">
                            <input class="form-control" name="email" value="{{ $row->barcode }}" readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">产品名</label>
                        <div class="col-md-5">
                            <input class="form-control" name="name" value="{{ $row->name }}"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">规格</label>
                        <div class="col-md-5">
                            <input class="form-control" name="spec" value="{{ $row->spec }}"  />
                        </div>
                    </div>
                    {{--<div class="form-group">--}}
                        {{--<label class="col-md-2 control-label">厂商</label>--}}
                        {{--<div class="col-md-5">--}}
                            {{--<input class="form-control" name="manufacturer" value="{{ $row->manufacturer }}"  />--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label class="col-md-2 control-label">品牌</label>
                        <div class="col-md-5">
                            <input class="form-control" name="trademark" value="{{ $row->trademark }}"   />
                        </div>
                    </div>
                    <div class="form-group">
                    <label class="col-md-2 control-label">分类</label>
                    <div class="col-md-5">
                        <select class="form-control" name="catid">
                            @foreach($cates as $it)
                                <option value="{{ $it->id }}" @if($it->id == $row->catid) selected  @endif >{{ $it->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    {{-- */$arr = json_decode($row->url, true);/* --}}
                    {{-- */$zarr = json_decode($row->replace_url, true);/* --}}
                    {{-- */$arrPrice = json_decode($row->spider, true);/* --}}
                    <div class="form-group">
                        <label class="col-md-2 control-label" style="line-height:60px">京东url</label>
                        <div class="col-md-7">
                            <input id="jdinput" class="form-control" name="jdUrl" value="{{ $arr["JD"] }}"  placeholder="自营" style="display:inline;width:418px;margin-bottom: 3px" />  ¥ <span id="jdspan" style="color:red">{{ $arrPrice['JD']['price'] }}</span> <a style="float:right" class="btn btn-sm btn-info" target="_blank" href="{{ $arr["JD"] }}">前往</a>
                            <input id="zjdinput" class="form-control" name="zjdUrl" value="{{ $zarr["JD"] }}"  placeholder="非自营" style="display:inline;width:418px" />  <a style="float:right" class="btn btn-sm btn-info" target="_blank" href="{{ $zarr["JD"] }}">前往</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" style="line-height:60px">天猫url</label>
                        <div class="col-md-7">
                            <input id="tmallinput" class="form-control" name="tmallUrl" value="{{ $arr["TMALL"] }}"  placeholder="自营" style="display:inline;width:418px;margin-bottom: 3px" /> ¥ <span id="tmallspan" style="color:red">{{ $arrPrice['TMALL']['price'] }}</span> <a style="float:right" class="btn btn-sm btn-info" target="_blank" href="{{ $arr["TMALL"] }}">前往</a>
                            <input id="ztmallinput" class="form-control" name="ztmallUrl" value="{{ $zarr["TMALL"] }}"  placeholder="非自营" style="display:inline;width:418px" /> <a style="float:right" class="btn btn-sm btn-info" target="_blank" href="{{ $zarr["TMALL"] }}">前往</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" style="line-height:60px">1号店url</label>
                        <div class="col-md-7">
                            <input id="yhdinput" class="form-control" name="yhdUrl" value="{{ $arr["YHD"] }}"  placeholder="自营" style="display:inline;width:418px;margin-bottom: 3px" /> ¥ <span id="yhdspan" style="color:red">{{ $arrPrice['YHD']['price'] }}</span> <a style="float:right" class="btn btn-sm btn-info" target="_blank" href="{{ $arr["YHD"] }}">前往</a>
                            <input id="zyhdinput" class="form-control" name="zyhdUrl" value="{{ $zarr["YHD"] }}"  placeholder="非自营" style="display:inline;width:418px" />  <a style="float:right" class="btn btn-sm btn-info" target="_blank" href="{{ $zarr["YHD"] }}">前往</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastname" class="col-sm-2 control-label"></label>
                        <div class="col-sm-5">
                            <button id="submit" type="submit" class="btn btn-primary">提交</button>
                            @if($did)
                                @if($openid)
                                <a href="{{ url('admin/detail/add/'.$openid.'?uuid='.$uuid) }}" type="button" class="btn btn-default" >返回</a>
                                @else
                                <a href="{{ url('admin/detail/detail/'.$did) }}" type="button" class="btn btn-default" >返回</a>
                                @endif
                            @else
                            <a href="{{ url('admin/product') }}" type="button" class="btn btn-default" >返回</a>
                            @endif
                            <span onclick="spider()" style="float:right;" class="btn btn-sm btn-danger">爬取价格</span>
                            <span onclick="price_change({{$row->id}})" style="float:right;margin-right:10px" class="btn btn-info btn-sm">修改价格</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function spider()
    {
        var jdurl = $("#jdinput").val();
        var tmallurl = $("#tmallinput").val();
        var yhdurl = $("#yhdinput").val();
        var zjdurl = $("#zjdinput").val();
        var ztmallurl = $("#ztmallinput").val();
        var zyhdurl = $("#zyhdinput").val();
        var data = {"jd": jdurl, "tmall": tmallurl, "yhd": yhdurl,"zjd": zjdurl, "ztmall": ztmallurl, "zyhd": zyhdurl };
        layer.load(3);
        $.ajax({
            type: "POST",
            url: '{{ url('admin/product/spider') }}',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token()  }}"
            },
            dataType: 'json',
            cache: false,
            data: data,
            success: function (result) {
                console.log(result);
                if(result.z.jd > 0){
                    $("#jdspan").html(result.z.jd);
                }else{
                    $("#jdspan").html(result.f.jd);
                }
                if(result.z.tmall > 0){
                    $("#tmallspan").html(result.z.tmall);
                }else{
                    $("#tmallspan").html(result.f.tmall);
                }
                if(result.z.yhd > 0){
                    $("#yhdspan").html(result.z.yhd);
                }else{
                    $("#yhdspan").html(result.f.yhd);
                }

                layer.closeAll('loading');
            }
        });
    }


    /**
     * 修改价格
     */
    function price_change(id)
    {
        var action = "{{ url('admin/detail/price_edit') }}/"+ id;
        $("#myModal").modal({
            remote:action,
            backdrop:"static",
            keyboard:true
        });
        $("#myModal").on("hidden.bs.modal", function() {
            $(this).removeData("bs.modal");
        });
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
                    url: '{{ url('/admin/product/update') }}',
                    dataType: 'json',
                    cache: false,
                    data:  $form.serialize(),
                    success: function(result) {
                        // 跳转链接
                        var did = {{ $did }};
                        if(did){
                            var openid = '{{ $openid }}';
                            var uuid = '{{ $uuid }}';
                            if(openid){
                                window.location.href = "/admin/detail/add/"+openid+"?uuid="+uuid;
                            }else{
                                window.location.href = "/admin/detail/detail/" + did;
                            }
                        }else{
                            window.location.href = "/admin/product";
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
                name: {
                    validators: {
                        notEmpty: {
                            message: '产品名不能为空~'
                        }
                    }
                },
                spec: {
                    validators: {
                        notEmpty: {
                            message: '规格不能为空~'
                        }
                    }
                },
                trademark: {
                    validators: {
                        notEmpty: {
                            message: '品牌不能为空~'
                        }
                    }
                },
                manufacturer: {

                },
//                jdUrl: {
//                    validators: {
//                        notEmpty: {
//                            message: 'url不能为空~'
//                        }
//                    }
//                },
//                tmallUrl: {
//                    validators: {
//                        notEmpty: {
//                            message: 'url不能为空~'
//                        }
//                    }
//                },
//                yhdUrl: {
//                    validators: {
//                        notEmpty: {
//                            message: 'url不能为空~'
//                        }
//                    }
//                },
            }
        });
    });
</script>
@endsection