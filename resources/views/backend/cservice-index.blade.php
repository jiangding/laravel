<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>聊天记录</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="appid" content="{{ $appid }}">
    <link rel="stylesheet" href="/src/layui/css/layui.css" />
    <style>
        body .layim-chat-main{height: auto;}
    </style>
</head>

<body>
<div class="layim-chat-main">
    <ul id="LAY_view"></ul>
</div>
<div id="LAY_page" style="margin: 0 10px;"></div>
<textarea title="消息模版" id="LAY_tpl" style="display:none;">
<%# layui.each(d.data, function(index, item){
  if(!isNaN(item.id)){ %>
    <li class="layim-chat-mine"><div class="layim-chat-user"><img src="<% item.avatar %>"><cite><i><% layui.data.date(item.timestamp) %></i><% item.username %></cite></div><div class="layim-chat-text"><% layui.layim.content(item.content) %></div></li>
    <%# } else { %>
    <li><div class="layim-chat-user"><img src="<% item.avatar %>"><cite><% item.username %><i><% layui.data.date(item.timestamp) %></i></cite></div><div class="layim-chat-text"><% layui.layim.content(item.content) %></div></li>
    <%# }
  }); %>
</textarea>

<script src="/src/layui/layui.js"></script>
<script src="/src/js/axios.min.js"></script>

<script>
    var useropenid = '{{ $useropenid }}';
    var adminid = '{{ $adminid }}';
</script>
<script>

    layui.use(['layim', 'laypage'], function(){
        var layim = layui.layim
                ,layer = layui.layer
                ,laytpl = layui.laytpl
                ,$ = layui.jquery
                ,laypage = layui.laypage;
        laytpl.config({
            open: '<%'
            ,close: '%>'
        });
        //开始请求聊天记录
        getLog($,laytpl,laypage,1);
    });

    //获取聊天记录
    function getLog($,laytpl, laypage, curr){
        var param = {
            appid:$('meta[name="appid"]').attr('content'),
            id:useropenid,
            page:curr
        };
        axios.post('/admin/cservice/chatlogs/' + adminid, param,{headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(function (res) {
                    if(0 == res.data.code){
                        var html = laytpl(LAY_tpl.value).render({
                            data: res.data.data
                        });
                        console.log(res);
                        $('#LAY_view').html(html);
                        //分页
                        laypage({
                            cont: 'LAY_page'
                            ,pages: Math.ceil(res.data.total/20)
                            ,first: false
                            ,last: false
                            ,curr: curr || 1 //当前页
                            ,jump: function(obj, first){
                                if(!first){
                                    getLog($,laytpl, laypage, obj.curr);
                                }
                            }
                        });
                    }else{
                        layer.msg('暂无聊天记录', {time:1000});
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
    }
    var parseQueryString = function( queryString ) {
        var params = {}, queries, temp, i, l;
        queryString = queryString.split("?")[1];
        queries = queryString.split("&amp;");
        for ( i = 0, l = queries.length; i < l; i++ ) {
            temp = queries[i].split('=');
            params[temp[0]] = temp[1];
        }
        return params;
    };


</script>
</body>
</html>