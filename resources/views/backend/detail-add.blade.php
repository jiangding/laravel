@extends('backend.layouts.master')
@section('content')

    {{--loading--}}
    <div class="modal fade" id="loading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop='static'>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    loading...
                </div>
            </div>
        </div>
    </div>

    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel">
                </div>
                <ol class="breadcrumb">
                    <li>首页</li>
                    <li>清单列表</li>
                    <li class="active">清单详情</li>
                </ol>
                <div class="panel-body">
                    <fieldset>
                        <legend style="border:none;margin-bottom: 10px;">清单号: {{ $row->uuid }}
                            <a href="javascript:;" onclick="toUsers({{  $row->id  }}, '{{ $user->openid }}')" style="float:right; margin-right:20px;text-decoration:none;cursor: pointer" class="fa  fa-2x  fa-btn fa-send-o" title="推送给用户" ></a>
                            <a onclick="spird();" style="float:right; margin-right:40px;" class="btn btn-danger btn-sm">批量爬取价格</a>
                        </legend>
                    </fieldset>
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal -->
                    </div>
                    <table class="table">
                        <tr>
                            <td>
                                <img width="45" src="{{ $user->avatar }}" class="img-circle">
                                {{ $user->nickname }}
                            </td>
                        </tr>
                    </table>
                    <fieldset>
                        <legend style="border:none;margin-bottom: 10px;">商品</legend>
                        {{-- */$JDPrice = 0;/* --}}
                        {{-- */$CatPrice = 0;/* --}}
                        {{-- */$OnePrice = 0;/* --}}
                        @foreach($products as $item)
                            <table class="table">
                                <tr>
                                    <th width="33%" style="background: #e4e4e4">条码</th>
                                    <th width="33%" style="background: #e4e4e4">商品名</th>
                                    <th style="background: #e4e4e4">规格</th>
                                    <td width="9%" rowspan="4" style="text-align: center;vertical-align: middle!important;">
                                        <a style="margin-bottom:30px;" class="btn btn-danger btn-sm openurl">批量打开链接</a>
                                        <button onclick="delete_product('{{ $row->uuid }}',{{$item->id}})" class="btn btn-warning btn-sm">删除商品</button>
                                        <a onclick="price_change({{$item->id}})" class="btn btn-info btn-sm" style="margin-top:30px" id="price_change">修改价格</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="33%"><a  href=" {{ url('admin/product/edit/'.$item->id.'/'.$row->id.'?openid='.$user->openid.'&uuid='.$row->uuid) }} ">{{ $item->barcode  }} </a></td>
                                    <td width="33%"><a  href=" {{ url('admin/product/edit/'.$item->id.'/'.$row->id.'?openid='.$user->openid.'&uuid='.$row->uuid) }} "> {{ $item->name  }} </a></td>
                                    <td>{{ $item->spec  }}</td>
                                </tr>
                                <!--计算-->
                                {{-- */$arrUrl = json_decode($item->url, true);/* --}}
                                {{-- */$arrPrice = json_decode($item->spider, true);/* --}}
                                {{-- */$JDPrice = bcadd($JDPrice,$arrPrice['JD']['price'],2);/* --}}
                                {{-- */$CatPrice = bcadd($CatPrice,$arrPrice['TMALL']['price'],2);/* --}}
                                {{-- */$OnePrice = bcadd($OnePrice,$arrPrice['YHD']['price'],2);/* --}}
                                <tr class="urls" id="{{$item->id}}">
                                    @if($arrUrl['JD'])
                                        <td><a id="jdurl" target="_blank" href="{{ $arrUrl['JD']  }}">京东商品链接   x {{ $arrPrice['JD']['stock'] }}</a></td>
                                    @else
                                        <td><a id="jdurl">京东商品链接 </a></td>
                                    @endif
                                    @if($arrUrl['TMALL'])
                                        <td><a id="tmallurl" target="_blank" href="{{ $arrUrl['TMALL']  }}">天猫商品链接   x {{ $arrPrice['TMALL']['stock'] }}</a></td>
                                    @else
                                        <td><a id="tmallurl">天猫商品链接 </a></td>
                                    @endif
                                    @if($arrUrl['YHD'])
                                        <td><a id="yhdurl" target="_blank" href="{{ $arrUrl['YHD']  }}">1号店商品链接   x {{ $arrPrice['YHD']['stock'] }}</a></td>
                                    @else
                                        <td><a id="yhdurl">1号店商品链接 </a></td>
                                    @endif

                                </tr>
                                <tr>
                                    <td style="color:red" class="jd_price"> ¥ {{ $arrPrice['JD']['price'] }}</td>
                                    <td style="color:red" class="tmall_price"> ¥ {{ $arrPrice['TMALL']['price'] }}</td>
                                    <td style="color:red" class="yhd_price"> ¥ {{ $arrPrice['YHD']['price'] }}</td>
                                </tr>
                            </table>
                        @endforeach
                        <table class="table">
                        <tr>
                        <td colspan="3"><button onclick="add_product('{{ $row->uuid }}')" class="btn btn-primary">添加产品</button></td>
                        </tr>
                        </table>
                        <legend style="border:none;margin-bottom: 10px;">运费 {{-- <a style="float:right; margin-right:20px" class="btn btn-sm btn-primary" id="postage_change">修改邮费</a> --}}</legend>
                        <table class="table">
                            <tr class="info">
                                <td width="33%">京东: {{ $postage['JD'] }} 元</td>
                                <td width="33%">天猫: {{ $postage['TMALL'] }} 元</td>
                                <td width="33%">1号店: {{ $postage['YHD'] }} 元</td>
                            </tr>
                        </table>
                        <legend style="border:none;margin-bottom: 10px;">比价结果</legend>
                        <table class="table">
                            <tr class="info">
                                <td width="33%">运费: {{$postage['JD']}}</td>
                                <td width="33%">运费: {{$postage['TMALL']}}</td>
                                <td width="33%">运费: {{$postage['YHD']}}</td>
                            </tr>
                            <tr>
                                <td width="33%">商品: {{ $JDPrice }} 元</td>
                                <td width="33%">商品: {{ $CatPrice }} 元</td>
                                <td width="33%">商品: {{ $OnePrice }} 元</td>
                            </tr>
                            <tr class="danger">
                                <td width="33%">京东总价: <span style="font-size:18px;color:#a10000">{{ bcadd($postage['JD'] , $JDPrice , 2) }}</span> 元</td>
                                <td width="33%">天猫总价: <span style="font-size:18px;color:#a10000">{{ bcadd($postage['TMALL'] , $CatPrice , 2) }}</span> 元</td>
                                <td width="33%">一号店总价: <span style="font-size:18px;color:#a10000">{{ bcadd($postage['YHD'] , $OnePrice ,2)}}</span> 元</td>
                            </tr>
                        </table>
                        <fieldset>
                            <legend style="border:none;margin-bottom: 10px;">操作记录</legend>
                            <table class="table">
                                @foreach($logs as $l)
                                    <tr>
                                        <td width="100%">
                                            @if(isset($l->admin->name))
                                                {{ $l->admin->name }}
                                            @elseif(isset($l->user->nickname))
                                                <img width="25" src="{{ $l->user->avatar }}" class="img-circle">
                                                {{ $l->user->nickname }}
                                            @endif
                                            {{ $l->pval }}  {{ $l->created_at }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td width="100%">
                                        <img width="25" src="{{ $row->user->avatar }}" class="img-circle">
                                        {{ $row->user->nickname }}
                                        创建清单  {{ $row['created_at'] }}
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toUsers( id , openid)
        {
            var obj = $(this);
            console.log(id);
            console.log(openid);
            layer.confirm('确定要推送该清单比较结果给用户？', {
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


        $(".openurl").click(function(){
            var urls = [];
            var urlsClass = $(this).parents('tbody').find('.urls');
            var jd_price = $(this).parents('tbody').find('.jd_price');
            var tmall_price = $(this).parents('tbody').find('.tmall_price');
            var yhd_price = $(this).parents('tbody').find('.yhd_price');


            $(".jd_price").removeClass('alert-success');
            $(".tmall_price").removeClass('alert-info');
            $(".yhd_price").removeClass('alert-warning');

            jd_price.addClass('alert-success');
            tmall_price.addClass('alert-info');
            yhd_price.addClass('alert-warning');

            urlsClass.find('a').each(function(){
                urls.push($(this).attr('href'));
            });
            for(var i =0; i <  urls.length; i++){
                window.open(urls[i]);
            }
        });



        function spird(){
            // 获取链接
            var jdUrl = [];
            var tmallUrl = [];
            var yhdUrl = [];
            var ids = [];
            $(".urls").each(function(i,e){
                ids.push($(e).attr('id'));
            });
            $(".urls").find('a').each(function(i,e){
                var k = $(e).attr('id');
                var v = $(e).attr('href');
                switch (k){
                    case "jdurl":
                        if(v != undefined){
                            jdUrl.push($(e).attr('href'));
                        }else{
                            jdUrl.push('');
                        }
                        break;
                    case "tmallurl":
                        if(v != undefined){
                            tmallUrl.push($(e).attr('href'));
                        }else{
                            tmallUrl.push('');
                        }
                        break;
                    case "yhdurl":
                        if(v != undefined){
                            yhdUrl.push($(e).attr('href'));
                        }else{
                            yhdUrl.push('');
                        }
                        break;
                }
            });

            var data = {"jd": jdUrl, "tmall": tmallUrl, "yhd": yhdUrl, "id":ids};
            layer.load(3);
            $.ajax({
                type: "POST",
                url: '{{ url('admin/detail/spider') }}',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token()  }}"
                },
                dataType: 'json',
                cache: false,
                data: data,
                success: function (result) {
                    console.log(result);
                    if (result.status == 0) {
                        window.location.reload();
                    } else {
                        alert(result.message);
                    }
                    layer.closeAll('loading');
                }
            });

        }

        /**
         * 修改邮费
         */
        $("#postage_change").click(function(){
            var uid = "{{ $row->id  }}";
            var action = "{{ url('admin/detail/postage_edit') }}/"+ uid;
            $("#myModal").modal({
                remote:action,
                backdrop:"static",
                keyboard:true
            });
            $("#myModal").on("hidden.bs.modal", function() {
                $(this).removeData("bs.modal");
            });
        });

        /**
         * 修改价格
         */
        function price_change(id)
        {
            var action = "{{ url('admin/detail/price_edit') }}/"+ id;
            $("#myModal").modal({
                remote:action,
                backdrop:"static",
                keyboard:true
            });
            $("#myModal").on("hidden.bs.modal", function() {
                $(this).removeData("bs.modal");
            });
        }

        /**
         * 添加产品
         */
        function add_product(uuid)
        {
           layer.prompt({
                title: '输入产品条码'
                ,formType: 3
                ,shade: 0
            }, function(text, index){
               layer.close(index);
               $.ajax({
                   type: "POST",
                   url: '{{ url('/admin/detail/productAdd') }}',
                   dataType: 'json',
                   cache: false,
                   data:  {uuid:uuid, productNo:text, _token:"{{ csrf_token() }}"},
                   success: function(result) {
                       console.log(result);
                       if(result.status === 200) {
                           layer.msg('添加成功!', {
                                       icon: 1,
                                       time: 1000,
                                   },
                                   function(){
                                       location.reload();
                                   });
                       }else{
                           layer.msg('添加失败!', {
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

        /**
         * 删除产品
         */
        function delete_product(uuid,id){
            layer.confirm('确定要删除该产品？', {
                icon: 0,
                title: '警告',
                shade: false,
                offset: '150px'
            }, function(index) {
                $.ajax({
                    type: "POST",
                    url: '{{ url('/admin/detail/productDelete') }}',
                    dataType: 'json',
                    cache: false,
                    data:  {pid:id, uuid:uuid, _token:"{{ csrf_token() }}"},
                    success: function(result) {
                        if(result.status === 200) {
                            location.reload();
                        }
                    }
                });
            });
        }

    </script>
@endsection
