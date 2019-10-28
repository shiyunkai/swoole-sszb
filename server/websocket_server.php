
<?php

$server = new Swoole\WebSocket\Server("0.0.0.0", 8812);

// 设置静态资源
$server->set(
	[
	    'enable_static_handler' => true,
	    'document_root' => "/home/python/php/workspace/demo/data",
	]
);

// 当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
$server->on('open', 'onOpen');

function onOpen($server, $request){
    print_r($request->fd);
}


//　当服务器收到来自客户端的数据帧时会回调此函数
$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    // 向客户端推送消息
    $server->push($frame->fd, "this is server");
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();

?>
