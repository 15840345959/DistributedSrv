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

//微信模板消息
/*
 * By TerryQi
 *
 * 2018-11-15
 */

class XCXTplMessageManager
{

    /*
     * 小程序派发模板消息
     *
     * By TerryQi
     *
     * $app app对象
     * @template_id 模板id
     * @info 消息数组，形式为
     *
     * [
     *  "keyword1"=>keyword1,
     * "keyword2"=>keyword2,
     * "keyword3"=>keyword3
     *
     *  ]
     *
    * param 配置参数，其中应该放置
     *
     * [
     *  "touser"=>小程序用户的openid,
     *  "page"=>小程序跳转的页面
     *  "form_id"=>'form_id'
     * ]
     *
     * @return true/false       发送成功 or 失败
     */
    public static function sendMessage($app, $template_id, $param, $info)
    {
        Utils::processLog(__METHOD__, '' . " template_id:" . $template_id . " param:" . json_encode($param) . " info:" . json_encode($info));
        $data = self::getContentByTemplatedId($template_id, $info);
        //如果data为空，代表没有模板，则返回
        if ($data == null) {
            return false;       //返回失败
        }
        $result = $app->template_message->send([
            'touser' => $param['touser'],
            'template_id' => $template_id,
            'page' => $param['page'],
            'form_id' => $param['form_id'],
            'data' => $data,
        ]);
        Utils::processLog(__METHOD__, '' . " result:" . json_encode($result));
        return true;
    }


    /*
     * 根据模板id获取keywords相关信息
     *
     * By TerryQi
     *
     * 2018-11-15
     *
     */
    private static function getContentByTemplatedId($templated_id, $info)
    {
        //$templated_id
        switch ($templated_id) {
            //艺术榜
            case Utils::XCX_YSB_SCHEDULE_NOTIFY:     //艺术榜日程类提醒
                return [
                    'keyword1' => $info["keyword1"],
                    'keyword2' => $info["keyword2"],
                    'keyword3' => $info["keyword3"],
                ];
            //每天一画
            case Utils::XCX_MRYH_JOIN_RESULT_NOFITY:    //结果通知
                return [
                    'keyword1' => $info["keyword1"],
                    'keyword2' => $info["keyword2"],
                    'keyword3' => $info["keyword3"],
                ];
            case Utils::XCX_MRYH_WITHDRAW_NOFITY:    //提现通知
                return [
                    'keyword1' => $info["keyword1"],
                    'keyword2' => $info["keyword2"],
                    'keyword3' => $info["keyword3"],
                ];
            case Utils::XCX_MRYH_JOIN_SCHEDULE_NOTIFY:    //日程提醒
                return [
                    'keyword1' => $info["keyword1"],
                    'keyword2' => $info["keyword2"],
                    'keyword3' => $info["keyword3"],
                ];
            default:
                return null;
        }
    }


}