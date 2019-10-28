
<?php

/*
 * ws优化基础类
 */

class Ws{
  CONST HOST = '0.0.0.0';
  CONST PORT = '8812';

  public $ws = null;
  
  public function __construct(){
	$this->ws = new swoole_websocket_server('0.0.0.0','8812');
    $this->ws->set(
        [
            'worker_num' => 2,
            'task_worker_num' => 2 
        ] 
    );
	$this->ws->on("open", [$this,'onOpen']);
	$this->ws->on("message", [$this,'onＭessage']);
    $this->ws->on("task", [$this, 'onTask']);
    $this->ws->on("finish", [$this, 'onFinish']);
	$this->ws->on("close", [$this,'onClose']);

	$this->ws->start();
  }

  // 监听连接事件
  public function onOpen($ws, $request){
	var_dump($request->fd);
    if($request->fd == 1){
    
        swoole_timer_tick(2000, function($timer_id){
        
           echo "2s: timerId:{$timer_id}\n"; 
        });
    }
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
    //$ws->task($data);
    swoole_timer_after(5000,function() use($ws, $frame){
    
        echo "5s-after\n";
        $ws->push($frame->fd, "server-time-after:");
    });
	$ws->push($frame->fd, "server-push:".date("Y-m-d H:i:s"));
  }

  public function onTask($serv, $taskId, $workerId, $data){
    print_r($data); 
    // 耗时场景,10s
    sleep(10);
    //　告诉worker任务完成, 调用onFinish
    return "on task finish!";

  }

  public function onFinish($serv, $taskId, $data){
    echo "taskID:{$taskId}"; 
    echo "finish-data-success:{$data}"; 
  }
  
  public function onClose($ws, $fd){
	echo "clientid:{$fd}\n";
  }

}

$obj = new Ws();

?>
