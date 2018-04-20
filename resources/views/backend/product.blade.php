@extends('backend.layouts.wrap')
@section('table')

<div id="page-wrapper">

        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel"></div>
                    <ol class="breadcrumb">
                        <li>首页</li>
                        <li>产品管理</li>
                        <li class="active">产品列表</li>
                    </ol>
                    <form class="form-inline" method="get" action="product">
                        <label class="control-label">条码 : </label><input type="text" class="form-control" style="width: 150px" name="barcode" placeholder="输入条形码" value="{{ isset($appends['barcode']) ? $appends['barcode'] : '' }}" />&nbsp;
                        <label class="control-label">产品名 : </label><input type="text" class="form-control" name="name" placeholder="输入产品名" value="{{ isset($appends['name']) ? $appends['name'] : '' }}" />&nbsp;
                        <label class="control-label">品牌 : </label><input type="text" class="form-control" style="width: 150px" name="trademark" placeholder="输入品牌" value="{{ isset($appends['trademark']) ? $appends['trademark'] : '' }}" />

                        <button id="search" class="btn btn-info btn-search">搜索</button>
                        <input type="file" name="upload" id="demo-upload-unwrap" lay-ext="xlsx" lay-title="导入excel" class="layui-upload-file">
                        <a target="_blank" href="{{ url('admin/product/export') }}" class="btn btn-info">导出</a>
                    </form>

                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th width="3%"></th>
                                <th width="10%">条形码</th>
                                <th width="8%">分类</th>
                                <th width="20%">产品名</th>
                                <th width="10%">规格</th>
                                <th width="10%">品牌</th>
                                {{--<th width="5%">--}}
                                    {{-- */$s = isset($appends['status']) ? $appends['status'] : 'all'; /* --}}
                                    {{-- */$u = url('admin/product?status='.$s.'&type=')/* --}}
                                    {{--<select class="form-control" name="type" style="padding:0;height:25px;font-size:12px;width:100%" onchange="window.location='{{$u}}'+this.value">--}}
                                        {{--<option value="all" @if(isset($appends['type']) && $appends['type'] == 'all') selected @endif>all</option>--}}
                                        {{--<option value="0" @if(isset($appends['type']) && $appends['type'] == '0') selected @endif>加</option>--}}
                                        {{--<option value="1" @if(isset($appends['type']) && $appends['type'] == '1') selected @endif>立</option>--}}
                                    {{--</select>--}}
                                {{--</th>--}}
                                <th width="7%">
                                    {{-- */$t = isset($appends['type']) ? $appends['type'] : 'all'; /* --}}
                                    {{-- */$r = url('admin/product?type='.$t.'&status=')/* --}}
                                    <select class="form-control" name="status" style="padding:0;height:25px;font-size:12px;width:100%" onchange="window.location='{{$r}}'+this.value;">
                                        <option value="all" @if(isset($appends['status']) && $appends['status'] == 'all') selected @endif>全部</option>
                                        <option value="-1" @if(isset($appends['status']) && $appends['status'] == '-1') selected @endif>异常</option>
                                        <option value="0" @if(isset($appends['status']) && $appends['status'] == '0') selected @endif>未处理</option>
                                        <option value="1" @if(isset($appends['status']) && $appends['status'] == '1') selected @endif>部分url</option>
                                        <option value="2" @if(isset($appends['status']) && $appends['status'] == '2') selected @endif>完整url</option>
                                    </select>
                                </th>
                                <th width="10%">电商价格</th>
                                <th width="9%">操作</th>
                                {{--<th width="5%"></th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td width="3%"><input type="checkbox" class="btn-checkall fly-checkbox"></td>
                                    <td width="10%">{{ $item->barcode or '' }}</td>
                                    <td width="8%">{{ $item->category->name or '' }}</td>
                                    <td width="20%">{{ $item->name or '' }}</td>
                                    <td width="10%">{{ $item->spec or '' }}</td>
                                    <td width="10%">{{ $item->trademark or '' }}</td>
                                    {{--<td width="5%">--}}
                                        {{--@if($item->t == 0)--}}
                                            {{--<span class="bg-danger">加</span>--}}
                                        {{--@elseif($item->t == 1)--}}
                                            {{--<span class="bg-info">立</span>--}}
                                        {{--@endif--}}
                                    {{--</td>--}}
                                    <td width="7%">
                                         @if($item->status == -1)
                                            <span class="label label-danger">异常</span>
                                         @elseif($item->status == 0)
                                            <span class="label label-primary">未处理</span>
                                         @elseif($item->status == 1)
                                            <span class="label label-info">部分url</span>
                                         @else
                                            <span class="label label-success">完整url</span>
                                         @endif
                                    </td>
                                    <td width="10%">
                                        {{-- */$arr = json_decode($item->spider, true);/* --}}
                                        J:{{ $arr["JD"]["price"] }}
                                        T:{{ $arr["TMALL"]["price"] }}
                                        Y:{{ $arr["YHD"]["price"] }}
                                    </td>
                                    <td width="9%">
                                        <a href="{{ url('/admin/product/edit/'.$item->id.'/0') }}" style="text-decoration:none;" class="fa  fa-2x  fa-btn fa-edit" title="修改" ></a>
                                        @if($item->userid)
                                        {{--<a href="javascript:;" style="text-decoration:none;cursor: pointer"  onclick="ajaxGetUser({{  $item->id  }}, '{{ $item->userid }}')" title="{{ $item->userid }}" class="fa fa-2x fa-btn fa-send-o"  ></a>--}}
                                        @endif
                                    </td>

                                    {{--<td width="5%" style="text-align:center;font-size:11px;">--}}
                                        {{--@if(isset($item->user->avatar))--}}
                                        {{--<img width="20" src="{{ $item->user->avatar }}"><br />--}}
                                        {{--{{$item->user->nickname}}--}}
                                        {{--@endif--}}
                                    {{--</td>--}}

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div style="display: inline-block; float:right;margin-top:-30px;">
                            {!! $data->appends($appends)->render() !!}
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
            </div>
</div>
    <script>
        function ajaxGetUser( id , uid)
        {
            $.ajax({
                type: "POST",
                url: '{{ url('/admin/product/getUser') }}',
                dataType: 'json',
                cache: false,
                data:  {id:id, uid:uid, _token:"{{ csrf_token() }}"},
                success: function(result) {
                    console.log(result);
                    var cont = '<ul>';
                    $.each(result.user,function(index,value){
                        cont += '<li style="float:left; padding:5px 0px"><input checked style="position: relative;left:28px; top:5px; width:16px;height:16px;" type="checkbox" name="ids" value='+value['id']+' /><img  width="40" class="img-circle" src= '+value['avatar']+'><br/><span style="float:left;width:75px;font-size:11px;text-align:center">'+value['nickname']+'</span></li>';
                    });
                    cont += '</ul>';
                    cont += '<div style="position:fixed;top:180px;left: 310px;"><button onclick="toUsers('+result.pid+')" class="btn btn-info" >推送</button></div>';
                    layer.open({
                        title:'推送',
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['400px', '240px'], //宽高
                        content: cont
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        }

        function toUsers( id )
        {
            var obj = $(this);
            var check = null;
            $('input[name="ids"]:checked').each(function(){
                if(check == null ){
                    check = $(this).val();
                }else{
                    check = check + ',' + $(this).val()
                }
            });
            if(check == null){
                layer.msg('请选择用户!', {
                            icon: 2,
                            time: 800,
                        },
                        function(){
                        });
                return ;
            }
            layer.confirm('确定要推送该产品给用户？', {
                icon: 0,
                title: '警告',
                shade: false,
                offset: '150px'
            }, function(index) {

                $.ajax({
                    type: "POST",
                    url: '{{ url('/admin/product/toUser') }}',
                    dataType: 'json',
                    cache: false,
                    data:  {id:id, uid:check, _token:"{{ csrf_token() }}"},
                    success: function(result) {
                        console.log(result);
                        layer.closeAll()
                        if(result.status === 200) {
                            layer.msg('推送成功!', {
                                        icon: 1,
                                        time: 1000,
                                    },
                                    function(){
                                        //layer.closeAll()
                                    });
                        }else{
                            layer.msg('推送失败!', {
                                        icon: 2,
                                        time: 1000,
                                    },
                                    function(){
                                        //layer.closeAll()
                                    });
                        }

                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                        console.log(status);
                        console.log(error);
                    }
                });

            });
        }
        $(document).ready(function() {
            layui.use(['layer','upload','form'], function(){
                layui.upload({
                    url: '/admin/product/import'
                    ,before: function(input){
                        //返回的参数item，即为当前的input DOM对象
                        layer.load(3);
                        console.log('文件上传中~~');
                    }
                    ,success: function(res){
                        console.log(res);
                        layer.closeAll('loading');
                        layer.msg('上传成功!');
                        layer.msg('上传成功!', function(){
                            window.location.reload();
                        });

                    }
                });
            });
        });

    </script>
@endsection


