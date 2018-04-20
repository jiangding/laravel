@extends('backend.layouts.master')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel">
            </div>
            <ol class="breadcrumb">
                <li>首页</li>
                <li>订单列表</li>
                <li class="active">订单详情</li>
            </ol>
            <div class="panel-body">
                <fieldset>
                    <legend style="margin-bottom:10px;border-bottom: none;">
                        <img width="30" src="{{ $order->user->avatar }}" class="img-circle">
                        {{ $order->user->nickname }}
                        <span style="padding:0px 10px;">|</span>{{ $order->orderid  }}
                        <div style="float:right;">
                            @if( $order->status == 1)
                            <button onclick="toOrder({{$order->id}})" class="btn btn-sm btn-primary">下单</button>
                            @elseif( $order->status == 3)
                            <button onclick="jugeMent(4, '{{$order->user->nickname}}，你的退款申请已受理，我们会尽快走完退款流程并在稍后通知你。')" class="btn btn-sm btn-primary">同意</button>
                            <button onclick="jugeMent(6, '{{$order->user->nickname}}，你的退款申请已取消，请知悉。')" class="btn btn-sm btn-primary">不同意</button>
                            @elseif( $order->status == 4)
                            <button onclick="jugeMent(5, '{{$order->user->nickname}}，我们已经在微信支付上为你申请退款，预计到账时间为0-3个工作日，请留意微信支付的相关信息。')" class="btn btn-sm btn-info">退款</button>
                            @endif
                        </div>

                    </legend>
                    <table class="table">
                        <tr>
                            <td>购物平台：
                                @if( $order->platform == 'JD')
                                京东<img src="{{ URL::asset('/images/jd.jpg') }}" width="70"  />
                                @elseif( $order->platform == 'TMALL')
                                天猫<img src="{{ URL::asset('/images/tm.jpg') }}" width="70"/>
                                @else
                                一号店<img src="{{ URL::asset('/images/yhd.jpg') }}" width="50"  />
                                @endif
                            </td>
                            <td>清单号: {{ $order->uuid }}</td>
                        </tr>
                        <tr>
                            <td style="border-top:none;">状态：
                                @if( $order->status == 0)
                                    <span class="label label-default ">未支付</span>
                                @elseif( $order->status == 1 )
                                    <span class="label label-success ">已支付</span>
                                @elseif( $order->status == 6 )
                                    <span class="label label-success " style="background:#228B22">已下单</span>
                                @elseif( $order->status == 2 )
                                    <span class="label label-default ">已取消</span>
                                @elseif($order->status == 3)
                                    <span class="label label-info">退款申请</span>
                                @elseif($order->status == 4)
                                    <span class="label label-danger">退款中</span>
                                @elseif($order->status == 5)
                                    <span class="label label-primary">已退款</span>
                                @endif
                            </td>
                            <td style="border-top:none;">支付交易号: {{ $order->pay->transaction_id or '' }}</td>
                        </tr>
                        <tr>
                            <td style="border-top:none;">收货人：{{ $order->address_name }}</td>
                            <td style="border-top:none;">电话：{{ $order->address_phone }}</td>
                        </tr>

                        <tr>
                            <td colspan="2" style="border-top:none;">收货地址：{{ $order->address }}</td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>操作记录</legend>
                    <table class="table">
                        @foreach($logs as $l)
                            <tr>
                                <td style="border-top:none;" >
                                    <div style="float:left;width:500px;">
                                    @if(isset($l->admin->name))
                                        <b style="float:left;width:25px;height:25px;background: green;border-radius: 12px"></b>
                                        &nbsp;{{ $l->admin->name }}
                                    @elseif(isset($l->user->nickname))
                                        <img width="25" src="{{ $l->user->avatar }}" class="img-circle">
                                        {{ $l->user->nickname }}
                                    @endif
                                    {{ $l->pval }}
                                    </div>
                                    <span style="float:left;">{{ $l->created_at }}</span>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td style="border-top:none;">
                                <div style="float:left;width:500px;">
                                <img width="25" src="{{ $order->user->avatar }}" class="img-circle">
                                {{ $order->user->nickname }}
                                创建订单
                                </div>
                                <span style="float:left;">{{ $order->created_at }}</span>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                <legend>订单商品
                        <input style="width:20px;height:18px" type="checkbox" @if($order->unusual == 1) checked @endif onclick="onClickHander(this)">
                </legend>
                <table class="table">
                    <tr style="background: #ececf0">
                        <th>条码</th>
                        <th>商品名</th>
                        <th>规格</th>
                        <th>价格</th>
                        <th>数量</th>
                    </tr>
                    {{-- */$arrProducts = json_decode($order->product, true);/* --}}
                    {{-- */$arrPrice = 0; /* --}}
                    @foreach($arrProducts as $p)
                    <tr style="height: 50px;">
                        <td width="10%">{{ $p['barcode'] }}</td>
                        <td width="15%"><a href="{{ $p['url'] }}" target="_blank">{{ $p['name'] }}</a></td>
                        <td width="15%">{{ $p['spec'] }}</td>
                        <td width="10%">{{ $p['price'] }}</td>
                        <td width="10%">{{ $p['num'] }}</td>
                        {{-- */$arrPrice += $p['price'] * $p['num'];/* --}}
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" align="right">商品总价: {{ $arrPrice }} 元</td>
                    </tr>
                    <tr>
                        <td  style="border-top:none;" colspan="5" align="right">运费：{{ bcsub($order->total,$arrPrice) }} 元</td>
                    </tr>
                    <tr>
                        <td  style="border-top:none;" colspan="5" align="right">订单总价：<span style="font-size:18px;color:#a10000">{{ $order->total }}</span> 元</td>
                    </tr>
                </table>
                <legend>物流信息</legend>
                <table class="table">
                    <tr>
                       卖家已发货
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function onClickHander(obj){
        console.log(obj.checked);
        var t;
        if(obj.checked){
            t = 1
        }else{
            t = 0
        }
        $.ajax({
            type: "POST",
            url: '{{ url('/admin/order/unusual') }}',
            dataType: 'json',
            cache: false,
            data:  { orderid:'{{$order->orderid}}', t:t, _token:"{{ csrf_token() }}"},
            success: function(result) {
//                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    }
    function jugeMent(t, message){
        layer.confirm('确定执行该操作？', {
            icon: 0,
            title: '警告',
            shade: false,
            offset: '150px'
        }, function(index) {
            $.ajax({
                type: "POST",
                url: '{{ url('/admin/order/refund') }}',
                dataType: 'json',
                cache: false,
                data:  { orderid:'{{$order->orderid}}',openid:'{{$order->user->openid}}', t:t,message:message, _token:"{{ csrf_token() }}"},
                success: function(result) {
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
                }
            });
        });
    }

    function toOrder(oid){
            layer.prompt({
                title: '输入平台订单号'
                ,formType: 3
                ,shade: 0
            }, function(text, index){
                layer.close(index);
                $.ajax({
                    type: "POST",
                    url: '{{ url('/admin/order/toOrder') }}',
                    dataType: 'json',
                    cache: false,
                    data:  {oid:oid, orderNo:text, _token:"{{ csrf_token() }}"},
                    success: function(result) {
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
</script>
@endsection
