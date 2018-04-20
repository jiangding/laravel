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
                        <li>用户管理</li>
                        <li class="active">意见反馈</li>
                    </ol>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th>昵称</th>
                                <th>反馈类型</th>
                                <th width="45%">内容</th>
                                <th>时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item->user->nickname  }}</td>
                                    <td>
                                        @if ( $item->type == 'USERFEEDBACK')
                                            用户反馈
                                        @else
                                            其他
                                        @endif
                                    </td>
                                    <td width="45%">{{ $item->value }}</td>
                                    <td>{{ $item->created_at }}</td>
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


