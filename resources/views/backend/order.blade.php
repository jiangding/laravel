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
                        <li>订单管理</li>
                        <li class="active">订单列表</li>
                    </ol>
                    <form class="form-inline" method="get" action="order">
                        <label class="control-label">订单号 : </label><input type="text" class="form-control" name="orderNo" placeholder="输入订单号" value="{{ isset($appends['orderNo']) ? $appends['orderNo'] : '' }}" />&nbsp;
                        <label class="control-label">清单号 : </label><input type="text" class="form-control" style="width: 280px;" name="uuid" placeholder="输入清单号" value="{{ isset($appends['uuid']) ? $appends['uuid'] : '' }}" />&nbsp;
                        <button id="search" class="btn btn-info btn-search">搜索</button>
                    </form>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th width="12%">订单号</th>
                                <th width="15%">清单号</th>
                                <th width="6%">平台</th>
                                <th width="8%">用户</th>
                                <th width="8%">订单总价</th>
                                <th width="8%">
                                    {{-- */$s = isset($appends['states']) ? $appends['states'] : 'all'; /* --}}
                                    {{-- */$u = url('admin/order?states='.$s.'&type=')/* --}}
                                    <select class="form-control" name="type" style="padding:0;height:25px;font-size:12px;width:100%" onchange="window.location='{{$u}}'+this.value">
                                        <option value="all" @if(isset($appends['type']) && $appends['type'] == 'all') selected @endif>全部</option>
                                        <option value="0" @if(isset($appends['type']) && $appends['type'] == '0') selected @endif>扫码记录</option>
                                        <option value="1" @if(isset($appends['type']) && $appends['type'] == '1') selected @endif>扫码比价</option>
                                        <option value="2" @if(isset($appends['type']) && $appends['type'] == '2') selected @endif>生成清单</option>
                                        <option value="3" @if(isset($appends['type']) && $appends['type'] == '3') selected @endif>自动比价</option>
                                    </select>
                                </th>
                                <th width="7%">
                                    {{-- */$t = isset($appends['type']) ? $appends['type'] : 'all'; /* --}}
                                    {{-- */$r = url('admin/order?type='.$t.'&states=')/* --}}
                                    <select class="form-control" name="status" style="padding:0;height:25px;font-size:12px;width:100%" onchange="window.location='{{$r}}'+this.value;">
                                        <option value="all" @if(isset($appends['states']) && $appends['states'] == 'all') selected @endif>全部</option>
                                        <option value="1" @if(isset($appends['states']) && $appends['states'] == '1') selected @endif>已支付</option>
                                        <option value="6" @if(isset($appends['states']) && $appends['states'] == '6') selected @endif>已下单</option>
                                        <option value="2" @if(isset($appends['states']) && $appends['states'] == '2') selected @endif>已取消</option>
                                        <option value="0" @if(isset($appends['states']) && $appends['states'] == '0') selected @endif>未支付</option>
                                        <option value="3" @if(isset($appends['states']) && $appends['states'] == '3') selected @endif>退款申请</option>
                                        <option value="4" @if(isset($appends['states']) && $appends['states'] == '4') selected @endif>退款中</option>
                                        <option value="5" @if(isset($appends['states']) && $appends['states'] == '5') selected @endif>已退款</option>
                                    </select>
                                </th>
                                <th width="14%">下单时间</th>
                                <th width="10%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($orders as $item)
                                <tr>
                                    <td width="12%">{{ $item->orderid }}
                                    </td>
                                    <td width="15%">{{ $item->uuid }}</td>
                                    <td width="6%">
                                        @if($item->platform == 'TMALL')
                                            天猫
                                        @elseif($item->platform == 'YHD')
                                            一号店
                                        @else
                                            京东
                                        @endif
                                    </td>
                                    <td width="8%">{{ $item->user->nickname }}</td>
                                    <td width="8%">{{ $item->total }} 元</td>
                                    <td width="8%">
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
                                    <td width="7%">
                                        @if($item->status == 0)
                                            <span class="label label-default">未支付</span>
                                        @elseif($item->status == 1)
                                            <span class="label label-success">已支付</span>
                                        @elseif($item->status == 6)
                                            <span class="label label-success" style="background:#228B22">已下单</span>
                                        @elseif($item->status == 2)
                                            <span class="label label-default">已取消</span>
                                        @elseif($item->status == 3)
                                            <span class="label label-info">退款申请</span>
                                        @elseif($item->status == 4)
                                            <span class="label label-danger">退款中</span>
                                        @elseif($item->status == 5)
                                            <span class="label label-primary">已退款</span>
                                        @endif
                                    </td>
                                    <td width="14%">{{ $item->created_at }}</td>
                                    <td width="10%">
                                        <a href="{{ url('/admin/order/detail/'.$item->id) }}" style="text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-file-o" title="详情" ></a>
                                        {{--@if($item->status == 1)--}}
                                            {{--<a onclick="templateToUser({{ $item->id }}, {{ $item->userid }})" href="javascript:;" style="text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-send-o" title="模板消息" ></a>--}}
                                        {{--@endif--}}
                                        @if($item->unusual == 1)
                                            异
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div style="display: inline-block; float:right;margin-top:-30px;">
                            {!! $orders->appends($appends)->render() !!}
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
    function templateToUser( id , uid)
    {
        var obj = $(this);
        layer.confirm('推送收货模板消息给用户？', {
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


