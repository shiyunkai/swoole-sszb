<?php
/**
 * User: SHI.YUN.KAI
 * Date: 19-11-24
 * Time: 下午9:28
 */

namespace app\admin\controller;


use app\common\lib\redis\Predis;
use app\common\lib\Util;

class Live
{

    /**
     *
     * @return false|string
     */
    public function push(){

        print_r($_POST);
        if(empty($_POST)){
            Util::show(config('code.error'),'error');
        }

        // TODO 暂时不写
        //1. 赛况信息入库


        //2.　push到客户端赛况页面

        // TODO 模拟数据(后期需要从数据库中查询)
        $teams = [
            1 => [
                'name' => '马刺',
                'logo' => '/live/imgs/team1.png'
            ],
            1 => [
                'name' => '火箭',
                'logo' => '/live/imgs/team2.png'
            ],
        ];

        $data = [
          'type' => intval($_POST['type']),
          'title' => !empty($teams[$_POST['team_id']]['name']) ? $teams[$_POST['team_id']]['name']:'直播员',
          'logo' => !empty($teams[$_POST['team_id']]['logo']) ? $teams[$_POST['team_id']]['logo']:'',
          'content' => !empty($_POST['content']) ? $_POST['content']:'',
          'image' => !empty($_POST['image']) ? $_POST['image']:'',

        ];

        $taskData = [
            'method' => 'pushLive',
            'data' => $data
        ];

        $_POST['http_server']->task($taskData);

        return Util::sho(config('code.success'),'ok');



    }

}