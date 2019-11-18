<?php
/**
 * Created by PhpStorm.
 * User: python
 * Date: 19-11-2
 * Time: 下午8:11
 */

namespace app\common\lib;


require APP_PATH.'/../extend/sms/src/index.php';

use Qcloud\Sms\SmsSingleSender;


class Sms{

    // 短信应用SDK AppID
    const APP_ID = 1400279662; // 1400开头

    // 短信应用SDK AppKey
    const APP_KEY = "7b49595d40dd0276c85bbfa25c2722af";

    // 短信模板ID，需要在短信应用中申请
    const TEMPLATE_ID = 458351;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请

    // 签名
    const SMS_SIGN = "shine90网"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`


    public static function sendSms($phone, $code){
        // 单发短信

        $ssender = new SmsSingleSender(self::APP_ID, self::APP_KEY);
	    //　验证码
	    $params[] = $code;
        // 验证码码有效期（分钟）
	    $params[] = config('code.out_time')/60;
	    $result = $ssender->sendWithParam("86", $phone, self::TEMPLATE_ID,
        $params, self::SMS_SIGN, "", "");

        return json_decode($result);

    }
}


