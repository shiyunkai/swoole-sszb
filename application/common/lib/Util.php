<?php
/**
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-2
 * Time: 下午8:51
 */

namespace app\common\lib;


class Util
{

    /**
     *  API output format
     * @param $status
     * @param string $message
     * @param array $data
     */
    public static function show($status, $message='',$data=[]){

        $result = [
            'status' => $status,
            'message' => $message,
            'data' =>$data
        ];

        return json_encode($result);
    }

}