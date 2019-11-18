<?php
/**
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-2
 * Time: 下午8:45
 */

namespace app\index\controller;


use app\common\lib\redis\Predis;
use app\common\lib\RedisUtil;
use app\common\lib\Sms;
use app\common\lib\Util;

class Login
{

    public function index(){
        // 获取手机号码，去redis中查找对比x
        $phone = intval($_GET['phone_num']);
        $code = intval($_GET['code']);

        if(empty($phone) || empty($code)){
            return Util::show(config('code.error'),'phone or code is error');
        }

        $redisCode = Predis::getInstance()->get(RedisUtil::smsKey($phone));

        if($redisCode == $code){

            $data = [
                'user' =>$phone,
                'srcKey' =>md5(RedisUtil::userKey($phone)),
                'time' => time(),
                'isLogin' => true
            ];

            Predis::getInstance()->set(RedisUtil::userKey($phone),$data);

            return Util::show(config('code.success'),'login success');
        }else{

            return Util::show(config('code.error'), 'login fail');
        }


    }

    /**
     *  发磅短信验证码
     * @return false|string
     */
    public function send(){

        $phoneNum = request()->get('phone_num',0,'intval');

        if(empty($phoneNum)){
            return Util::show(config('code.error'),'error');
        }

        // generate a random number
        $code = rand(1000,9999);

        // 异步任务发送短信
        $taskData = [
            'method' => 'sendSms',
            'data' => [
                'phone' => $phoneNum,
                'code' => $code
            ]

        ];

        $_POST['http_server']->task($taskData);

        return Util::show(config('code.success'),'验证码发送成功');

/*        // redis save
        try{
            $response = Sms::sendSms($phoneNum,$code);


            if($response->result == 0){
                // send success
                $redis = new \Swoole\Coroutine\Redis();
                $redis->connect(config('redis.host'), config('redis.port'));
                $redis->set(RedisUtil::smsKey($phoneNum),$code,config('code.out_time'));
                return Util::show(config('code.success'),'验证码发送成功');
            }else{
                return Util::show(config('code.error'),'验证码发送失败');
            }

        }catch (\Exception $e){
            return Util::show(config('code.error'),'sms exception');
        }*/


    }

}
