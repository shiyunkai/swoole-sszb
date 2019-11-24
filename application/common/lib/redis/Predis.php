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

        return $this->redis->setex($key,$time,$value);
    }

    /**
     *  删除key
     * @param $key
     * @return int
     */
    public function del($key){
        return $this->redis->del($key);
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

//    /**
//     * 添加到有序集合
//     * @param $key
//     * @param $value
//     * @return mixed
//     */
//    public function sAdd($key, $value){
//        return $this->redis->sAdd($key, $value);
//    }
//
//
//    /**
//     *  删除有序集合中的值
//     * @param $key
//     * @param $value
//     * @return mixed
//     */
//    public function sRem($key, $value){
//        return $this->redis->sRem($key, $value);
//    }

    /**
     *  获取有序集合中的值g
     * @param $key
     * @return array
     */
    public function sMembers($key){
        return $this->redis->sMembers($key);
    }


    /**
     *  使用魔术方法可以省略　sAdd sRem 方法的编写
     * @param $name
     * @param $arguments
     * @return string
     */
    public function __call($name, $arguments){

        if(count($arguments) == 2){
            return $this->redis->$name($arguments[0],$arguments[1]);
        }

    }


}