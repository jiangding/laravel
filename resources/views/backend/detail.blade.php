@extends('backend.layouts.wrap')
@section('table')

<div id="page-wrapper">
        <div class="row">
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="panel">
                    </div>
                    <ol class="breadcrumb">
                        <li>首页</li>
                        <li>产品管理</li>
                        <li class="active">清单列表</li>
                    </ol>
                    <form class="form-inline" method="get" action="detail">
                        <label class="control-label">清单号 : </label><input type="text" class="form-control" style="width: 280px;" name="uuid" placeholder="输入清单号" value="{{ isset($appends['uuid']) ? $appends['uuid'] : '' }}" />&nbsp;
                        <button id="search" class="btn btn-info btn-search">搜索</button>
                        {{--<a href="{{ url('admin/detail/add') }}" class="btn btn-info">添加清单</a>--}}
                    </form>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th width="40%">清单号</th>
                                <th width="10%">用户</th>
                                <th width="10%">
                                    {{-- */$s = isset($appends['status']) ? $appends['status'] : 'all'; /* --}}
                                    {{-- */$u = url('admin/detail?status='.$s.'&type=')/* --}}
                                    <select class="form-control" name="type" style="padding:0;height:25px;font-size:12px;width:100%" onchange="window.location='{{$u}}'+this.value">
                                        <option value="all" @if(isset($appends['type']) && $appends['type'] == 'all') selected @endif>全部</option>
                                        <option value="0" @if(isset($appends['type']) && $appends['type'] == '0') selected @endif>扫码记录</option>
                                        <option value="1" @if(isset($appends['type']) && $appends['type'] == '1') selected @endif>扫码比价</option>
                                        <option value="2" @if(isset($appends['type']) && $appends['type'] == '2') selected @endif>生成清单</option>
                                        <option value="3" @if(isset($appends['type']) && $appends['type'] == '3') selected @endif>自动比价</option>
                                    </select>
                                </th>
                                <th width="10%">
                                    {{-- */$t = isset($appends['type']) ? $appends['type'] : 'all'; /* --}}
                                    {{-- */$r = url('admin/detail?type='.$t.'&status=')/* --}}
                                    <select class="form-control" name="status" style="padding:0;height:25px;font-size:12px;width:100%" onchange="window.location='{{$r}}'+this.value;">
                                        <option value="all" @if(isset($appends['status']) && $appends['status'] == 'all') selected @endif>全部</option>
                                        <option value="0" @if(isset($appends['status']) && $appends['status'] == '0') selected @endif>未推送</option>
                                        <option value="1" @if(isset($appends['status']) && $appends['status'] == '1') selected @endif>已推送</option>
                                        <option value="2" @if(isset($appends['status']) && $appends['status'] == '2') selected @endif>已转订单</option>
                                    </select>
                                </th>
                                <th width="15%">提交时间</th>
                                <th width="10%">operation</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td width="40%">{{ $item->uuid }}</td>
                                    <td width="10%">{{ $item->user->nickname }}</td>
                                    <td width="10%">
                                        @if($item->type == 0)
                                            <span class="bg-danger">扫码记录</span>
                                        @elseif($item->type == 1)
                                            <span class="bg-info">扫码比价</span>
                                        @elseif($item->type == 2)
                                            <span class="bg-success">生成清单</span>
                                        @elseif($item->type == 3)
                                            <span class="bg-warning">自动比价</span>
                                        @endif
                                    </td>
                                    <td width="10%">
                                        @if($item->status == 0)
                                        <span class="label label-warning">未推送</span>
                                        @elseif($item->status == 1)
                                        <span class="label label-success">已推送</span>
                                        @elseif($item->status == 2)
                                        <span class="label label-default">已转订单</span>
                                        @endif
                                    </td>
                                    <td width="15%">{{ $item->created_at }}</td>
                                    <td width="10%">
                                        @if($item->type == 2)
                                            <a href="{{ url('/admin/detail/add/'.$item->user->openid.'?uuid='.$item->uuid) }}" style="text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-file-o" title="详情" ></a>
                                        @else
                                            <a href="{{ url('/admin/detail/detail/'.$item->id) }}" style="text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-file-o" title="详情" ></a>
                                        @endif

                                        {{--@if($item->status != 2)--}}
                                        {{--<a href="javascript:;" onclick="toUsers({{  $item->id  }}, '{{ $item->user->openid }}')" style="text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-send-o" title="推送给用户" ></a>--}}
                                        {{--@endif--}}
                                    </td>
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
    function toUsers( id , openid)
    {
        var obj = $(this);
        console.log(id);
        console.log(openid);
        layer.confirm('确定要推送该清单比较结果给用户？', {
            icon: 0,
            title: '警告',
            shade: false,
            offset: '150px'
        }, function(index) {

            $.ajax({
                type: "POST",
                url: '{{ url('/admin/detail/toUser') }}',
                dataType: 'json',
                cache: false,
                data:  {id:id, openid:openid, _token:"{{ csrf_token() }}"},
                success: function(result) {
                    console.log(result);
                    if(result.status === 200) {
                        layer.msg('推送成功!', {
                                    icon: 1,
                                    time: 1000,
                                },
                                function(){
                                    location.reload();
                                });
                    }else{
                        layer.msg('推送失败!', {
                                    icon: 2,
                                    time: 1000,
                                },
                                function(){
                                    //location.reload();
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

</script>
@endsection


