<?php
/**
 * User: SHI.YUN.KAI
 * Date: 19-12-14
 * Time: 上午12:03
 */

namespace app\index\controller;


use app\common\lib\Util;

class Chart
{

    public function index(){

        // 直播室id为空
        if(empty($_POST['game_id'])){
            return Util::show(config('code.error'),'error');
        }

        if(empty($_POST['content'])){
            return Util::show(config('code.error'),'error');
        }

        // 封装推送消息
        $data = [
            'user'=>'用户'. rand(0,2000),
            'content' => $_POST['content'],
        ];

        // 8812端口
        foreach($_POST['http_server']->ports[1]->connections as $fd){

            // 推送消息
            $_POST['http_server']->push($fd,json_encode($data));
        }

        return Util::show(config('code.success'),'success');
    }
}