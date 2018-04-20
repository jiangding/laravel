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
                        <li  class="active"> {{ $userRow->nickname }} 地址本</li>
                    </ol>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th>姓名</th>
                                <th>电话</th>
                                <th>邮编</th>
                                <th>详细地址</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->zip }}</td>
                                    <td>{{ $item->area }} {{ $item->address }}</td>
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


