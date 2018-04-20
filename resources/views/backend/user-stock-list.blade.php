@extends('backend.layouts.wrap')
@section('table')

<div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
                </div>
                    <ol class="breadcrumb">
                        <li>首页</li>
                        <li>用户管理</li>
                        <li>用户列表</li>
                        <li  class="active"> {{ $userRow->nickname }} 库存</li>
                    </ol>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th>条形码</th>
                                <th>商品名</th>
                                <th>规格</th>
                                <th>单件周期</th>
                                <th>剩余数量</th>
                                <th>剩余用量</th>
                                {{--<th>操作</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    {{--<td>{{ $item->id }}</td>--}}
                                    <td>{{ $item->product->barcode }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->spec }}</td>
                                    <td>{{ $item->cycle }}</td>
                                    <td>{{ $item->last }} </td>
                                    <td>{{ $item->lastday }} 天</td>
                                    {{--<td>--}}
                                        {{--<a data-id="{{ $item->id }}" style="text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-edit" title="修改" ></a>--}}
                                    {{--</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
</div>

@endsection


