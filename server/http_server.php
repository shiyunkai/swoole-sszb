
<?php

use think\Container;

$http = new swoole_http_server('0.0.0.0',8811);

// 设置静态资源
$http->set(
	[
	    'enable_static_handler' => true,
	    'document_root' => "/home/python/php/workspace/swoole-sszb/public/static/live",
		'worker_num' => 5
	]
);
// worker进程开启时
$http->on('WorkerStart', function (swoole_server $server, $worker_id){
	// ThinkPHP 引导文件,
	define('APP_PATH', __DIR__ . '/../application/');
	require __DIR__ . '/../thinkphp/base.php';
});

$http->on('request', function($request, $response){
	// 转换swoole request为 thinkphp request
    $_SERVER = []; // 清除缓存
	if(isset($request->server)){
		foreach ($request->server as $k => $v){
			$_SERVER[$k] = $v;
		}
	}

    $_GET = [];
	if(isset($request->get)){
		foreach ($request->get as $k => $v){
			$_GET[$k] = $v;
		}
	}

	$_POST = [];
	if(isset($request->post)){
		foreach ($request->post as $k => $v){
			$_POST[$k] = $v;
		}
	}

	ob_start();

	// 执行应用并响应
	Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
		->run()
		->send();
//    echo '--action--'.request()->action();

	$res = ob_get_contents();
	ob_end_clean();

	$response->end($res);

});




$http->start();


