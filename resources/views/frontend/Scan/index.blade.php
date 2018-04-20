<!DOCTYPE html><html><head><title>{{ $title }}</title>
    <meta name="apple-mobile-web-app-title" content="{{ $title }}" />
    <meta name="msapplication-TileColor" content="#090a0a">
    <link rel="stylesheet" type="text/css" href="/css/index329af2.css">
</head>
<script language="JavaScript" type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?46c92c2addc1f87804bb84524ac9e3a3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script type="text/javascript">
    wx.config({debug: {{ $debug }},
        appId: '{{ $appId }}',
        timestamp: '{{ $timestamp }}',
        nonceStr: '{{ $nonceStr }}',
        signature: '{{ $signature }}',
        url: '{{ $url }}',
        jsApiList: {!! $jsApiList !!}});
    wx.ready(function(){(function(){
        wx.scanQRCode({needResult: 1,
            scanType: ["barCode"],
            success: function (res) {
                location.href = '/stock/barcode/1/'+res.resultStr.split(",")[1];}});})();
        wx.error(function(res){console.debug(res);});});
</script>
<body>
<div class="body">
    <div class="main">
        <div class="login_content">
            <div class="login_box" style="background-color: #f0f0f0;">
                <script>
                    var rand = parseInt(24*Math.random() + 1);
                    document.write('<img src="/scangif/loadingfun'+rand+'.gif" style="width:300px;height: 300px;">');
                </script>

            </div>
        </div>
    </div>
</div>
</body>
</html>