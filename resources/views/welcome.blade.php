<!DOCTYPE html>
<meta charset="utf-8" />
<title>WebSocket Test</title>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?46c92c2addc1f87804bb84524ac9e3a3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<style>
    p{word-wrap: break-word;}
    tr:nth-child(odd){background-color: #ccc}
    tr:nth-child(even){background-color: #eee}
</style>
<h2>WebSocket Test</h2>
<table><tbody id="output"></tbody></table>
<script>
    var wsUri = "ws://127.0.0.1:9050/websocket";
    var protocols = ['jsonrpc'];
    var output = document.getElementById("output");

    function send(message) {
        websocket.send(message);
        log('Sent', message);
    }

    function log(type, str) {
        str = str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        output.insertAdjacentHTML('beforeend', '<tr><td>' + type + '</td><td><p>' + htmlEscape(str) + '</p></td></tr>');
    }

    websocket = new WebSocket(wsUri, protocols);
    websocket.onopen = function(evt) {
        log('Status', "Connection opened");
        send(JSON.stringify({method: '/', params: {hello: 'world'},  id: 1}));
        setTimeout(function(){ websocket.close() },1000)
    };
    websocket.onclose = function(evt) { log('Status', "Connection closed") };
    websocket.onmessage = function(evt) { log('<span style="color: blue;">Received</span>', evt.data) };
    websocket.onerror = function(evt) {  log('<span style="color: red;">Error</span>', evt.data) };
</script>
</html>