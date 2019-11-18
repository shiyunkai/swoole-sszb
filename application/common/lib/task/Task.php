<?php
/**
 *  代表的是swoole里面　后续所有的异步任务
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-7
 * Time: 上午12:16
 */

namespace app\common\lib\task;
use app\common\lib\RedisUtil;
use app\common\lib\Sms;
use app\common\lib\Util;
use app\common\lib\redis\Predis;

class Task
{

    /**
     *  异步发送验证码
     * @param $data
     */
    public function sendSms($data){
        $response = Sms::sendSms($data['phone'],$data['code']);
        dump($response);
        //if($response->result == 0){
            // send success
           // $redis = new \Swoole\Coroutine\Redis();
            //$redis->connect(config('redis.host'), config('redis.port'));
            //$redis->set(RedisUtil::smsKey($data['phone']),$data['code'],config('code.out_time'));
            //return Util::show(config('code.success'),'验证码发送成功');
//        }else{
//            //return Util::show(config('code.error'),'验证码发送失败');
//            return false;
//        }

        echo $data['code'];

       if($response->result == 0){

            $redis = Predis::getInstance();
            $redis->set(RedisUtil::smsKey($data['phone']),$data['code'],config('code.out_time'));

        }else{
            return false;
        }

        return true;


    }

}