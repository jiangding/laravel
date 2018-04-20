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
                        <li class="active">品类列表</li>
                    </ol>
                    {{--<form class="form-inline" method="get" action="catalog">--}}
                        {{--<label class="control-label">代码 : </label><input type="text" class="form-control" width="100px" name="shortcode" placeholder="输入品类代码" value="{{ isset($_GET['shortcode']) ? $_GET['shortcode'] : '' }}" />&nbsp;--}}
                        {{--<label class="control-label">品类名 : </label><input type="text" class="form-control" name="name" placeholder="输入品类名" value="{{ isset($_GET['name']) ? $_GET['name'] : '' }}" />--}}

                        {{--<button id="search" class="btn btn-info btn-search">搜索</button>--}}
                        {{--<a href="{{ url('admin/catalog/add') }}" class="btn btn-info">新增品类</a>--}}
                    {{--</form>--}}
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th width="8%">品类代码</th>
                                <th width="8%">品类名</th>
                                <th width="40%">关键词</th>
                                <th width="8%">品类图片</th>
                                <th width="14%">加入时间</th>
                                <th width="12%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td width="8%">{{ $item->shortcode }}</td>
                                    <td width="8%">{{ $item->name }}</td>
                                    {{--<td width="40%">{{ str_limit($item->keyword, 100) }}</td>--}}
                                    <td width="40%">{{ $item->keyword }}</td>
                                    <td width="8%">
                                        <img class="img-circle" width="45" src="{{ URL::asset('/') . $item->icon }}"  />
                                    </td>
                                    <td width="14%">{{ $item->created_at }}</td>
                                    <td width="12%">
                                        <a href="{{ url('/admin/catalog/edit/'.$item->id) }}" style="text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-edit" title="修改" ></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div style="display: inline-block; float:right;margin-top:-30px;">
                            {!! $data->render() !!}
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
</div>

@endsection


