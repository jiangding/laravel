@extends('backend.layouts.wrap')
@section('table')

<div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                </div>
                    <ol class="breadcrumb">
                        <li>首页</li>
                        <li>客服管理</li>
                        <li class="active">用户消息</li>
                    </ol>
                    <form class="form-inline" method="get" action="customService">
                        <label class="control-label">客户 : </label><input type="text" class="form-control" name="nickname" placeholder="输入客户昵称" value="{{ isset($appends['nickname']) ? $appends['nickname'] : '' }}" />&nbsp;
                        <button id="search" class="btn btn-info btn-search">搜索</button>
                    </form>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="table-layout:fixed;">
                            <thead>
                            <tr>
                                {{--<th>ID</th>--}}
                                <th width="20%">消息</th>

                                <th width="10%">类型</th>
                                <th width="55%">内容</th>
                                <th width="15%">时间</th>
                                {{--<th width="10%">操作</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($csRecord as $item)
                                <tr>
                                    {{--<td>{{ $item->id }}</td>--}}

                                    <td width="20%">
                                        @if($item->mode == 1)
                                            {{ $item->kf['name'] }}  →  <img src="{{ $item->user['avatar'] }}" class="img-circle" width="15" /> {{ $item->user['nickname'] }}
                                        @else
                                            <img src="{{ $item->user['avatar'] }}" class="img-circle" width="15" /> {{ $item->user['nickname'] }}  →  {{ $item->kf['name'] }}
                                        @endif

                                    </td>
                                    <td width="10%">
                                        {{ $item->mode == 1 ? '客服消息' : '用户消息' }}
                                    </td>
                                    <td width="55%">
                                    @if(strpos($item->record,'pload'))
                                        @if(strpos($item->record, 'mg['))
                                           {{-- */$axx = rtrim($item->record,']'); /* --}}
                                           {{-- */$axx = ltrim($axx,'img['); /* --}}
                                           <img src="{{env('URL').$axx}}" width="50" />
                                        @else
                                           <img src="{{env('URL').'/upload/'.$item->record}}" width="50" />
                                        @endif

                                    @else
                                       {!! $item->record !!}
                                    @endif
                                    </td>

                                    <td width="15%">{{ $item->created_at }}</td>
                                    {{--<td width="10%">--}}
                                        {{--<a href="{{ url('/admin/user/edit/'.$item->id) }}" style="text-decoration:none;" class="fa  fa-2x  fa-btn fa-edit" title="修改" ></a>--}}
                                        {{--<a href="{{ url('/admin/user/stockList/'.$item->id) }}" style="text-decoration:none;" class="fa  fa-2x  fa-btn fa-home" title="库存"></a>--}}
                                        {{--<a href="{{ url('/admin/user/addressList/'.$item->id) }}" style="text-decoration:none;" class="fa  fa-2x  fa-btn fa-book" title="地址本"></a>--}}
                                    {{--</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div style="display: inline-block; float:right;margin-top:-30px;">
                            {!! $csRecord->render() !!}
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
         </div>
            <!-- /.col-lg-12 -->
</div>

@endsection


