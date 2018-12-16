<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use App\Components\Utils;
use Illuminate\Support\Facades\Log;
use Qiniu\Auth;

class SMSManager
{

    //接入项目编码
    const PRO_CODE = "szhZAoXRbaDgZ2oIiFxzqBQim2VDTr0D";

    //发送通知
    public static function sendSMS($telphone, $templated_id, $sms_txt)
    {
        $param = array(
            'phonenum' => $telphone,       //项目pro_code应该统一管理，建议在Utils中定义一个通用变量
            'template_id' => $templated_id,
            'pro_code' => self::PRO_CODE,
            'sms_txt' => $sms_txt
        );
        Utils::processLog(__METHOD__, '', " " . "sendSMS param:" . json_encode($param));
        $result = Utils::curl('http://common.isart.me/api/common/sms/sendSMS', $param, true);   //访问接口

        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        $result = json_decode($result, true);   //因为返回的已经是json数据，为了适配makeResponse方法，所以进行json转数组操作
        return $result;
    }
}