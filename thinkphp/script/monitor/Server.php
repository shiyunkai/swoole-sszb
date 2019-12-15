<?php
/**
 *  监控服务　Ｗs http 8811
 * User: SHI.YUN.KAI
 * Date: 19-12-15
 * Time: 下午11:38
 */

namespace thinkphp\script\monitor;


class Server
{
    const PORT = 8811;

    public function port(){
        $shell = " netstat -anp 2>/dev/null | grep ".self::PORT." | grep LISTEN | wc -l";

        $result = shell_exec($shell);
        echo $result;
        if($result != 1){
            // 发送报警服务，邮件，短信
            echo date("Ymd H:i:s")." error".PHP_EOL;
        }else{
            echo date("Ymd H:i:s")." success".PHP_EOL;
        }
    }

}

// 每隔２秒监测一次
swoole_timer_tick(2000,function ($timer_id){
    (new Server())->port();
});
