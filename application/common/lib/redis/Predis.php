<?php
/**
 * 同步的redis
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-6
 * Time: 下午10:03
 */
namespace app\common\lib\redis;


class Predis
{
    /**
     *  定义单例模式的变量　
     * @var null
     */
    private static $_instance = null;


    public $redis = "";

    private function __construct()
    {
        $this->redis = new \Redis();
        $result = $this->redis->connect(
            config('redis.host'),
            config('redis.port')
        );

        if($result === false){
            throw new \Exception('redis connect error');
        }
    }

    public static function getInstance(){
        if(empty(self::$_instance)){
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     *  设置redis值
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|string
     */
    public function set($key, $value, $time = 0){
        if(!$key){
            return '';
        }

        if(is_array($key)){
            // 转换成string
            $value = json_encode($value);
        }

        if(!$time){
            // 不设置有效期
            return $this->redis->set($key, $value);
        }

        return $this->redis->setex($key,$value,$time);
    }

    /**
     *  获取key
     * @param $key
     * @return bool|string
     */
    public function get($key){
        if(!$key){
            return '';
        }

        return $this->redis->get($key);
    }


}