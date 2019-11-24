<?php
/**
 * User: SHI.YUN.KAI
 * Date: 19-11-24
 * Time: 下午9:28
 */

namespace app\admin\controller;


use app\common\lib\Util;

class Image
{

    /**
     *  图片上传
     * @return false|string
     */
    public function index(){

        $file = request()->file('file');
        $info = $file->move('../public/static/upload');
        if($info) {

            $data = [
                'image' => config('live.host') . '/upload/' . $info->getSaveName()
            ];

            return Util::show(config('code.success'), 'OK', $data);

        }else{
            return Util::show(config('code.error'),'error');
        }

    }

}