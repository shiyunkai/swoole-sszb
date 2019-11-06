<?php
/**
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-2
 * Time: 下午10:15
 */

namespace app\common\lib;


class RedisUtil
{
    /**
     *  验证码前缀
     * @var string
     */
    public static $pre = "sms_";

    /**
     *  用户前缀
     * @var string
     */
    public static $userpre = "user_";

    /**
     *  存储验证码 redis key
     * @param $phone
     * @return string
     */
    public static function smsKey($phone){
        return self::$pre.$phone;
    }

    /**
     * 　用户key
     * @param $phone
     * @return string
     */
    public static function userKey($phone){
        return self::$userpre.$phone;
    }

}