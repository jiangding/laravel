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
                        <li>物流信息</li>
                        <li class="active">物流</li>
                    </ol>
                    <form class="form-inline" method="get" action="order">
                        <label class="control-label">订单号 : </label><input type="text" class="form-control" name="orderNo" placeholder="输入订单号" value="" />&nbsp;
                        <button id="search" class="btn btn-info btn-search">搜索</button>
                    </form>
                    <div style="width:500px;margin-left:800px;">
                        <button id="spiderLogistic" class="btn btn-info btn-danger">批量爬取物流</button>
                        <button id="updateCookies" class="btn btn-info btn-search">更新cookies</button>

                    </div>

                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>
                                <th width="5%">收货人</th>
                                <th width="5%">联系电话</th>
                                <th width="15%">收货地址</th>
                                <th width="5%">订单号</th>
                                <th width="5%">子单号</th>
                                <th width="5%">平台</th>
                                <th width="55%">物流信息</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($orders as $item)
                                <tr>
                                    <td rowspan="{{$item['counts']}}">{{$item['address_name']}}</td>
                                    <td rowspan="{{$item['counts']}}">{{$item['address_phone']}}</td>
                                    <td rowspan="{{$item['counts']}}">{{$item['address']}}</td>
                                    <td rowspan="{{$item['counts']}}" style="text-align: center">{{$item['logisticsid']}}<br /> <button data-href="/admin/addChildrenId/{{$item['logisticsid']}}" class="addChildrenIds btn btn-default ">添加子单</button></td>
                                    @if ($item['counts']>1)
                                        @foreach($item['childrenId'] as $id)
                                            <td>{{$id}}</td>
                                            <td>{{$item['platform']}}</td>
                                            @if (isset($item['logisticsInfo'][$id]) && count($item['logisticsInfo'][$id]) > 0)
                                                <td>
                                                    <button class="showLogisticsInfo btn btn-default center-block">点击查看物流信息</button>
                                                    <span class="latest_info">{{$item['logisticsInfo'][$id][0][0]->info_mesg}}</span>
                                                    <span class="hidden hideInfo">
                                                        @foreach($item['logisticsInfo'][$id] as $info)
                                                            <span>{{$info[0]->info_mesg}}</span>
                                                            <span>{{$info[0]->info_method}}</span>
                                                            <span class="pull-right">{{$info[0]->time}}</span>
                                                            <br />
                                                         @endforeach
                                                    </span>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                        <tr>
                                        @endforeach
                                    </tr>
                                    @else
                                        <td></td>
                                        <td>{{$item['platform']}}</td>
                                        @if (isset($item['logisticsInfo']) && count($item['logisticsInfo']) > 0)
                                            <td>
                                                <button class="showLogisticsInfo btn btn-default center-block" >点击查看物流信息</button>
                                                <span class="latest_info">{{$item['logisticsInfo'][$item['logisticsid']][0][0]->info_mesg}}</span>
                                                <span class="hidden hideInfo">
                                                    @foreach($item['logisticsInfo'] as $infos)
                                                        @if ($infos != '')
                                                            @foreach($infos as $info)
                                                            <span>{{$info[0]->info_mesg}}</span>
                                                            <span class="pull-right">{{$info[0]->time}}</span>
                                                            <br />
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </span>
                                            </td>
                                        @else
                                            <td></td>
                                        @endif

                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div style="display: inline-block; float:right;margin-top:-30px;">
                            {!! $orders->render() !!}
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

    <div class="panel panel-success hidden toAddChildrenIds" style="width:20%;position: absolute;height:100px;top:500px;left:20%;">
        <div class="panel-heading">
            <h3 class="panel-title">添加子单</h3>
        </div>
        <div class="panel-body">
            <div class="input-group">
                <input type="text" class="form-control" id="childrenIdInfo">
                <span class="input-group-btn">
                    <button id="doAddChildrenIds" class="btn btn-default" type="button" data-href="">Go!</button>
                </span>
            </div><!-- /input-group -->
        </div>
    </div>

    <div class="panel panel-success hidden updateCookies" style="width:20%;position: absolute;height:400px;top:200px;left:40%;">
        <div class="panel-heading">
            <h3 class="panel-title">更新cookies</h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="name">jd</label>
                <input type="text" class="form-control" id="jdCookies" placeholder="请输入cookies">
            </div>
            <div class="form-group">
                <label for="name">yhd</label>
                <input type="text" class="form-control" id="yhdCookies" placeholder="请输入cookies">
            </div>
            <div class="form-group">
                <label for="name">tamll</label>
                <input type="text" class="form-control" id="tmallCookies" placeholder="请输入cookies">
            </div>

            <div class="form-group">
                <button id="doUpdateCookies"  class="btn btn-primary btn-lg btn-block">更新</button>
            </div>

            <div class="form-group">
                <button onclick="$(this).parents().find('.panel-success').addClass('hidden')"  class="btn btn-primary btn-lg btn-block">关闭</button>
            </div>

        </div>
    </div>
</div>




<script>
    $(document).ready(function(){
       //点击爬取物流
        $("#spiderLogistic").click(function(){
            $.ajax({
                url:'/admin/spiderLogisticByAll',
                type:'get',
                async:true,
                cache:false,
                dataType:'text',
                success:function(returnMessage){
                    var returnMessage = $.parseJSON(returnMessage);
                    alert(returnMessage['message']);
                },
                error:function(){
                    alert('亲，你今天更新cookies了吗');
                }
            });
        });

        //添加子单
        $(".addChildrenIds").click(function(){
            var url = $(this).attr('data-href');
            var top = $(this).offset().top;
            $("#childrenIdInfo").val('');
            $(".toAddChildrenIds")
                    .removeClass('hidden')
                    .css('top', top+20+'px')
                    .find('#doAddChildrenIds')
                    .attr('data-href', url);
        });

        //添加子单  go
        $("#doAddChildrenIds").click(function(){
            var childrenIdStr = $("#childrenIdInfo").val();
            var url = $(this).attr('data-href');
            $.ajax({
                url:url,
                data:{'childrenIdStr':childrenIdStr},
                type:'get',
                async:true,
                cache:false,
                dataType:'text',
                success:function(returnMessage){
                    var returnMessage = $.parseJSON(returnMessage);
                    $(".toAddChildrenIds").addClass('hidden');
                    alert(returnMessage['message'])
                },
                error:function(){
                    alert('添加子单出错，请联系你们亲爱的攻城狮');
                }
            });
        });


        //更新cookies
        $("#updateCookies").click(function(){
            $("#jdCookies").val('');
            $("#yhdCookies").val('');
            $("#tmallCookies").val('');
            $(".updateCookies").removeClass('hidden');
        });

        //去更新cookies
        $("#doUpdateCookies").click(function(){
            var jdCookies = $("#jdCookies").val();
            var yhdCookies = $("#yhdCookies").val();
            var tmallCookies = $("#tmallCookies").val();
            if (jdCookies == '' && yhdCookies == '' && tmallCookies == ''){
                alert('大哥，cookies全部为空你叫我爬啥');
                return false;
            }

            var param = {};
            param['jd'] = jdCookies;
            param['yhd'] = yhdCookies;
            param['tmall'] = tmallCookies;
            var url = '/admin/logistics/updateCookies';
            $.ajax({
                url:url,
                data:param,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                async:true,
                cache:false,
                dataType:'text',
                success:function(returnMessage){
                    var returnMessage = $.parseJSON(returnMessage);
                    $(".updateCookies").addClass('hidden');
                    alert(returnMessage['message']);
                }
            });

        });

        //点击查看物流
        $(".showLogisticsInfo").click(function(){
            var info = $(this).parent().find('.hideInfo');
            var latest_info = $(this).parent().find('.latest_info');
            if (info.hasClass('hidden')){
                latest_info.addClass('hidden');
                info.removeClass('hidden');
            }else{
                latest_info.removeClass('hidden');
                info.addClass('hidden');
            }
        });

    });
</script>
@endsection


