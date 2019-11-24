<?php
/**
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-6
 * Time: 下午11:30
 */

use think\Container;
use app\common\lib\redis\Predis;
use app\common\lib\RedisUtil;
use app\common\lib\Sms;
use app\common\lib\Util;

/**
 *  封装web_socket类
 *  web_socket是基于http的，所以可以用http访问
 * Class Http
 */
class Web_socket
{
    CONST HOST = "0.0.0.0";
    CONST PORT = 8811;

    private $ws = null;

    public function __construct()
    {
        $this->ws = new swoole_websocket_server(
            self::HOST, self::PORT
        );
        $this->ws->set(
            [
                'worker_num' => 4,
                'task_worker_num' => 4,
                // 设置静态资源
                'enable_static_handler' => true,
                'document_root' => "/home/python/php/workspace/swoole-sszb/public/static",
            ]
        );


        $this->ws->on("open", [$this,'onOpen']);
        $this->ws->on("message", [$this,'onＭessage']);
        $this->ws->on("workerstart", [$this,'onWorkerStart']);
        $this->ws->on("request", [$this,'onRequest']);
        $this->ws->on("task", [$this, 'onTask']);
        $this->ws->on("finish", [$this, 'onFinish']);
        $this->ws->on("close", [$this,'onClose']);


        $this->ws->start();
    }

    public function onWorkerStart($server, $worker_id){
        // ThinkPHP 引导文件,
        define('APP_PATH', __DIR__ . '/../application/');
        require __DIR__ . '/../thinkphp/start.php';

        // 重启时删除redis中的客户端id
        Predis::getInstance()->del(config('redis.live_game_key'));
    }

    // 监听连接事件
    public function onOpen($ws, $request){
        Predis::getInstance()->sAdd(config('redis.live_game_key'),$request->fd);

        var_dump('client:'.$request->fd.' connect');
    }

    // 监听客户端消息事件
    public function onＭessage($ws, $frame){
        echo "ser-push-message:{$frame->data}\n";
        // 假设有耗时操作
        $data = [
            'task' => 1,
            'fd' => fd
        ];
        // 投放任务
        $ws->task($data);
        $ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));
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

        $_FILES = [];
        if(isset($request->files)){
            foreach ($request->files as $k => $v){
                $_FILES[$k] = $v;
            }
        }

        // 传递当前对象
        $_POST['http_server'] = $this->ws;

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
        $flag = $obj->$method($data['data'], $serv);

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

    public function onClose($ws, $fd){
        Predis::getInstance()->sRem(config('redis.live_game_key'),$fd);

        echo "client:{$fd} close\n";
    }

}

$obj = new Web_socket();