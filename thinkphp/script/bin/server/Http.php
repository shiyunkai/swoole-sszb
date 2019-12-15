<?php
/**
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-6
 * Time: 下午11:30
 */

use think\Container;
use app\common\lib\RedisUtil;
use app\common\lib\Sms;
use app\common\lib\Util;

/**
 *  封装http类（替换http_server.php）
 * Class Http
 */
class Http
{
    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;

    private $http = null;

    public function __construct()
    {
        $this->http = new swoole_http_server(
            self::HOST, self::PORT
        );
        $this->http->set(
            [
                'worker_num' => 4,
                'task_worker_num' => 4,
                // 设置静态资源
                'enable_static_handler' => true,
                'document_root' => "/home/python/php/workspace/swoole-sszb/public/static/live",
            ]
        );


        $this->http->on("workerstart", [$this,'onWorkerStart']);
        $this->http->on("request", [$this,'onRequest']);
        $this->http->on("task", [$this, 'onTask']);
        $this->http->on("finish", [$this, 'onFinish']);
        $this->http->on("close", [$this,'onClose']);


        $this->http->start();
    }

    public function onWorkerStart($server, $worker_id){
        // ThinkPHP 引导文件,
        define('APP_PATH', __DIR__ . '/../application/');
        require __DIR__ . '/../thinkphp/start.php';
    }

    public function onRequest($request, $response){
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

        // 传递当前对象
        $_POST['http_server'] = $this->http;

        ob_start();

        // 执行应用并响应
        Container::get('app', [defined('APP_PATH') ? APP_PATH : ''])
            ->run()
            ->send();
//    echo '--action--'.request()->action();

        $res = ob_get_contents();
        ob_end_clean();

        $response->end($res);

    }

    public function onFinish($serv, $taskId, $data){
        echo "taskID:{$taskId}";
        echo "finish-data-success:{$data}";
    }


    public function onTask($serv, $taskId, $workerId, $data){

        // 分发task任务机制，让不同的任务，走不同的逻辑
        $obj = new app\common\lib\task\Task;
        $method = $data['method'];
        $flag = $obj->$method($data['data']);

        return $flag;

//        $response = Sms::sendSms($data['phone'],$data['code']);

/*        echo $response;

       if($response->result == 0){
            // send success
            $redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'), config('redis.port'));
            $redis->set(RedisUtil::smsKey($phoneNum),$code,config('code.out_time'));
            return Util::show(config('code.success'),'验证码发送成功');
        }else{
            return Util::show(config('code.error'),'验证码发送失败');
        }*/

        //　告诉worker任务完成, 调用onFinish
       // return "on task finish!";

    }

    public function onClose($http, $fd){
        echo "clientid:{$fd}\n";
    }

}

$obj = new Http();