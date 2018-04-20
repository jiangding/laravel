<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="appid" content="gh_d30a13af0bc7">
    <meta name="openid" content="{{ $admin->id }}">
    <title>刚好</title>
    <!-- Bootstrap Core CSS -->
    <link href="{{ URL::asset('/') }}src/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    {{--layIM CSS--}}
    <link rel="stylesheet" href="{{ URL::asset('/') }}src/layIM/css/layui.css" media="all">

    <!-- Custom CSS -->
    <link href="{{ URL::asset('/') }}src/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="{{ URL::asset('/') }}src/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- JavaScripts -->
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" ></script>--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>--}}
    <script src="{{ URL::asset('/src/vendor/jquery/jquery-2.2.3.min.js') }}" ></script>
    <script src="{{ URL::asset('/src/js/axios.min.js') }}" ></script>
    <script src="{{ URL::asset('/src/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
</head>
<body id="app-layout">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ url('/admin/index') }}">刚好后台管理</a>
        </div>

        <ul class="nav navbar-top-links navbar-right">

            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="">
                    <i class="fa fa-user fa-fw"></i> {{ $admin->name or '' }} <i class="fa fa-caret-down"></i>

                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="#"><i class="fa fa-user fa-fw"></i> 个人中心</a>
                    </li>
                    <li><a href="#"><i class="fa fa-gear fa-fw"></i> 设置</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="{{ url('/admin/logout') }}"><i class="fa fa-sign-out fa-fw"></i> 退出</a>
                    </li>
                </ul>

            </li>
        </ul>

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav">
                    <li>
                        <a @if(strpos(Request::url(), 'admin/index')) style="background: #94ccec"  @endif href="{{ url('admin/index') }}"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="javascript:;"><i class="fa fa-bar-chart-o fa-fw"></i> 用户管理<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a @if(strpos(Request::url(), "admin/user")) style="background: #94ccec"  @endif href="{{ url('admin/user') }}"> 用户列表</a>
                            </li>
                            <li>
                                <a @if(strpos(Request::url(), 'admin/feedback')) style="background: #94ccec"  @endif href="{{ url('admin/feedback') }}"> 意见反馈</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>

                    <li>
                        <a href="javascript:;"><i class="fa fa-sitemap fa-fw"></i> 产品管理<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a @if(strpos(Request::url(), 'admin/product')) style="background: #94ccec"  @endif href="{{ url('admin/product') }}"> 产品列表 </a>
                            </li>
                            <li>
                                <a @if(strpos(Request::url(), 'admin/catalog')) style="background: #94ccec"  @endif href="{{ url('admin/catalog') }}"> 品类管理</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li>
                        <a href="javascript:;"><i class="fa fa-files-o fa-fw"></i> 订单管理<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a @if(strpos(Request::url(), 'admin/detail')) style="background: #94ccec"  @endif href="{{ url('admin/detail') }}"> 清单列表</a>
                            </li>
                            <li>
                                <a @if(strpos(Request::url(), 'admin/order')) style="background: #94ccec"  @endif href="{{ url('admin/order') }}"> 订单列表</a>
                            </li>
                            <li>
                                <a @if(strpos(Request::url(), 'admin/logistics')) style="background: #94ccec"  @endif href="{{ url('admin/logistics') }}"> 物流信息</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                    <li>
                        <a href="javascript:;"><i class="fa fa-user fa-fw"></i> 客服管理<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a @if(strpos(Request::url(), 'admin/customService')) style="background: #94ccec"  @endif href="{{ url('admin/customService') }}"> 用户消息</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <script src="{{ URL::asset('/') }}src/layui/layui.js" charset="utf-8"></script>
    @yield('content')

    <script>
        layui.use(['layer','layim'], function(layim){
            var layim = layui.layim;
            var layer = layui.layer;

            //IM
            var IM = {
                unread_count:0,
                socket:null,
                initData:null,
                inited:0,
                address: "wss://"+location.hostname+"/push/websocket",
                initsocket:function(){
                    IM.socket = new WebSocket(IM.address, ['jsonrpc']);
                    IM.socket.onopen = function(){
                        IM.register();
                    };
                    IM.socket.onmessage = function(e){

                        var socketdata = JSON.parse(e.data);
                        var msg = JSON.parse(socketdata.result);
                        if(!msg) return;
                        if(!msg.message_type || (msg.message_type != 'register' && !layim)) return;
                        switch(msg.message_type) {
                            case 'register':
                                console.log(msg.data);
                                console.log(msg.initdata);
                                return IM.init(msg.data,msg.initdata);
                            case 'pong':
                                return;
                            case 'sendMessage':
                                return;
                            case 'newMessage':
                                //将好友追加到主面板
                                layui.layim.addList({
                                    type: 'friend'
                                    ,avatar: msg.data.avatar //好友头像
                                    ,username: msg.data.username //好友昵称
                                    ,groupid: 'groupname1' //所在的分组id
                                    ,id: msg.data.id //好友ID
                                    ,sign: '' //好友签名
                                });
                                return layim.getMessage(msg.data);
                            case 'SystemMessage':
                                //将好友追加到主面板
                                layui.layim.addList({
                                    type: 'friend'
                                    ,avatar: msg.user.avatar //好友头像
                                    ,username: msg.user.username //好友昵称
                                    ,groupid: 'groupname1' //所在的分组id
                                    ,id: msg.data.id //好友ID
                                    ,sign: '' //好友签名
                                });
                                return layim.getMessage(msg.data);
                            case 'status':
                                return;
                        }
                    };
                    IM.socket.onclose = IM.initsocket;
                },
                register:function(){
                    IM.socket.send(JSON.stringify({
                        method: '/push/register',
                        params: {
                            appid:$('meta[name="appid"]').attr('content'),
                            openid:$('meta[name="openid"]').attr('content'),
                            init:this.inited
                        }
                    }));
                },
                sendMessage:function(){
                    layim.on('sendMessage', function(data){
                        var To = data.to.id;
                        var From = data.mine.id;
                        IM.socket.send(JSON.stringify({
                            method: '/push/sendMessage',
                            params: {
                                appid:$('meta[name="appid"]').attr('content'),
                                csopenid:From,
                                useropenid:To,
                                message:data.mine.content
                            }
                        }));
                    });
                },
                init:function(msg_data, init_data){
                    var unread_msg_tips = function(msg_data){
                        // 离线消息
                        for(var key in msg_data.unread_message){
                            layim.getMessage(JSON.parse(msg_data.unread_message[key]));
                        }
                        if (msg_data.unread_notice_count) {
                            // 设置消息盒子未读计数
                            // layim.msgbox && layui.layim.msgbox(msg_data.unread_notice_count);
                        }
                        return;
                    };
                    if(this.inited == 1) {
                        return unread_msg_tips(msg_data);
                    }
                    this.inited = 1;
                    setInterval(function () {
                        if(IM.socket && IM.socket.readyState == 1) {
                            IM.socket.send(JSON.stringify({
                                method: '/push/ping',
                                params: {
                                    appid:$('meta[name="appid"]').attr('content'),
                                    openid:$('meta[name="openid"]').attr('content')
                                }
                            }));
                        }
                    }, 10000);
                    layim.config({
                        title: '客服',
                        isgroup: false,
                        isNewFriend: false,
                        notice: true,
                        copyright:true,
                        isAudio: false, //开启聊天工具栏音频
                        isVideo: false, //开启聊天工具栏视频
                        initSkin: '2.jpg', //1-5 设置初始背景
                        voice:'default.mp3',
                        isfriend: true,
                        //初始化接口
                        init: init_data

                        /*查看群员接口
                        ,members: {
                            url: 'json/getMembers.json'
                            ,data: {}
                        }*/
                        //上传图片接口
                        ,uploadImage: {
                            url: '/admin/csupload'
                        }

                        /*上传文件接口
                        ,uploadFile: {
                            url: '/admin/cs/upload'
                        }*/
                        //扩展工具栏
                        ,tool: [{
                            alias: 'detail'
                            ,title: '生成清单'
                            ,icon: '&#xe63c;'
                           },
                            {
                                alias: 'code'
                                ,title: '快捷回复'
                                ,icon: '&#xe61a;'
                            }
                        ]
                        //,msgbox: '/admin/customservice/msgbox' //消息盒子页面地址，若不开启，剔除该项即可
                        //,find: layui.cache.dir + 'css/modules/layim/html/find.html' //发现页面地址，若不开启，剔除该项即可
                        ,chatLog: '/admin/cservice/chatlogs/{{ $admin->id }}'
                    });
                    layim.on('ready', function(res){

                    });
                    layim.on('online', function(status){
                        IM.socket.send(JSON.stringify({
                            method: '/push/status',
                            params: {
                                appid:$('meta[name="appid"]').attr('content'),
                                openid:$('meta[name="openid"]').attr('content'),
                                status:status
                            }
                        }));
                    });
                    layim.on('sendMessage', function(data){
                        var To = data.to.id;
                        var From = data.mine.id;
                        IM.socket.send(JSON.stringify({
                            method: '/push/sendMessage',
                            params: {
                                appid:$('meta[name="appid"]').attr('content'),
                                csopenid:From,
                                useropenid:To,
                                message:data.mine.content
                            }
                        }));
                    });

                    layim.on('tool(detail)', function(insert, send, obj){
                        var id = obj.data.id;
                        var url = '{{ url('admin/detail/add/') }}';
                        window.open(url +'/'+ id + "?uuid=1");
                    });
                    layim.on('tool(code)', function(insert){
                        insert("好的, 你先去忙别的");
//                        layer.prompt({
//                            title: ''
//                            ,formType: 3
//                            ,shade: 0
//                        }, function(text, index){
//                            layer.close(index);
//                            insert(text); //将内容插入到编辑器
//                        });
                    });
                }
            };
            IM.initsocket();
        });
    </script>
</body>
</html>