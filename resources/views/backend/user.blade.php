@extends('backend.layouts.wrap')
@section('table')

<div id="page-wrapper">

        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                </div>
                    <ol class="breadcrumb">
                        <li>首页</li>
                        <li>用户管理</li>
                        <li class="active">用户列表</li>
                    </ol>
                    <form class="form-inline" method="get" action="user">
                        <label class="control-label">昵称 : </label><input type="text" class="form-control" name="nickname" placeholder="输入昵称" value="{{ isset($appends['nickname']) ? $appends['nickname'] : '' }}" />&nbsp;
                        <button id="search" class="btn btn-info btn-search">搜索</button>
                    </form>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                {{--<th width="10%">自编码</th>--}}
                                <th width="10%">用户id</th>
                                <th width="10%">昵称</th>
                                <th width="7%">性别</th>
                                <th width="10%">缩略图</th>
                                <th width="10%">手机号</th>
                                <th width="10%">提醒周期</th>
                                <th width="8%">状态</th>
                                <th width="14%">加入时间</th>
                                <th width="10%">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    {{--隐藏openid 和 unionid--}}
                                    <input type="hidden" name="open_id" value="{{ $item->openid }}">
                                    <input type="hidden" name="union_id" value="{{ $item->unionid }}">
                                    <td width="10%">{{ $item->id }}</td>
                                    <td class="name" width="10%">{{ $item->nickname }}</td>
                                    <td width="7%">
                                        {{ $item->sex == 1 ? '男' : '女' }}
                                    </td>
                                    <td width="10%">
                                        <img src="{{ $item->avatar }}" class="img-circle" style="cursor: pointer" width="45" />
                                    </td>
                                    <td width="10%">{{ $item->mobile }}</td>
                                    <td width="10%">{{ $item->remind }} 天</td>
                                    <td width="8%">
                                        @if($item->status == 1)
                                        <span class="label label-success">已关注</span>
                                        @else
                                        <span class="label label-warning">已取消</span>
                                        @endif
                                    </td>
                                    <td width="14%">{{ $item->created_at }}</td>
                                    <td width="10%">
                                        <a href="{{ url('/admin/user/edit/'.$item->id) }}" style="text-decoration:none;" class="fa  fa-2x  fa-btn fa-edit" title="修改" ></a>
                                        <a href="{{ url('/admin/user/stockList/'.$item->id) }}" style="text-decoration:none;" class="fa  fa-2x  fa-btn fa-home" title="库存"></a>
                                        <a href="{{ url('/admin/user/addressList/'.$item->id) }}" style="text-decoration:none;" class="fa  fa-2x  fa-btn fa-book" title="地址本"></a>
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
<script>
    layui.use('layim', function(layim){
        //面板外的操作
        var $ = layui.jquery, active = {
            message: function(){
                //制造好友消息
                layim.getMessage({
                    username: "贤心"
                    ,avatar: "//tp1.sinaimg.cn/1571889140/180/40030060651/1"
                    ,id: "100001"
                    ,type: "friend"
                    ,content: "嗨，你好！欢迎体验LayIM。演示标记："+ new Date().getTime()
                    ,timestamp: new Date().getTime()
                });
            }
            ,messageAudio: function(){
                //接受音频消息
                layim.getMessage({
                    username: "林心如"
                    ,avatar: "//tp3.sinaimg.cn/1223762662/180/5741707953/0"
                    ,id: "76543"
                    ,type: "friend"
                    ,content: "audio[http://gddx.sc.chinaz.com/Files/DownLoad/sound1/201510/6473.mp3]"
                    ,timestamp: new Date().getTime()
                });
            }
            ,messageVideo: function(){
                //接受视频消息
                layim.getMessage({
                    username: "林心如"
                    ,avatar: "//tp3.sinaimg.cn/1223762662/180/5741707953/0"
                    ,id: "76543"
                    ,type: "friend"
                    ,content: "video[http://www.w3school.com.cn//i/movie.ogg]"
                    ,timestamp: new Date().getTime()
                });
            }
        };
        $(".img-circle").click(function(){
            var avatar = $(this).attr('src');
            var name = $(this).parent().prev().prev().html();
            var id = $(this).parent().prev().prev().prev().prev().prev().val();
            layim.chat({
                name: name
                ,type: 'friend'
                ,avatar: avatar
                ,id: id
            });
        });
    });
</script>
@endsection


