<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components\GZH;

use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Text;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use Qiniu\Auth;
use App\Components\Utils;

class WeChatManager
{
    /*
     * 根据openid获取用户公众号信息
     *
     * By TerryQi
     *
     * 2018-07-03
     */
    public static function getByFWHOpenId($fwh_openid, $app)
    {
        Utils::processLog(__METHOD__, '', " " . "getUserInfoByFWHOpenId fwh_openid:" . $fwh_openid);
        $userInfo = $app->user->get($fwh_openid);
        return $userInfo;
    }

    /*
     * 判断用户是否关注公众号
     *
     * By TerryQi
     *
     * 2018-07-03
     */
    public static function isUserSubscribe($fwh_openid, $app)
    {
        $userInfo = $app->user->get($fwh_openid);
        return $userInfo['subscribe'] == 1 ? true : false;
    }


    /*
     * 发送定向消息
     *
     * By TerryQi
     *
     * 2018-07-06
     */
    public static function sendDirectMessage($directMessage, $app)
    {
        Utils::processLog(__METHOD__, '', " " . "directMessage:" . json_encode($directMessage));
//        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[$directMessage->busi_name]);
        $result = $app->customer_service->message($directMessage->content)
            ->to($directMessage->to_openid)
            ->send();
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        return $result;
    }

    /*
     * 发送客服消息
     *
     * By TerryQi
     *
     * 2018-07-24
     */
    public static function sendCustomerMessage($message, $openid, $app)
    {
        Utils::processLog(__METHOD__, '', " " . "message:" . json_encode($message) . " openid" . $openid);
        $result = $app->customer_service->message($message)
            ->to($openid)
            ->send();
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        return $result;
    }

    /*
     * 删除菜单
     *
     * By TerryQi
     *
     * 2018-08-09
     */
    public static function deleteMenu($app)
    {
        $result = $app->menu->delete(); // 全部
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        return $result;
    }

    /*
     * 创建菜单项
     *
     * By TerryQi
     *
     * 2018-07-09
     */
    public static function createMenu($app, $buttons)
    {
        $result = $app->menu->create($buttons);       //创建搜索项目
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));
        return $result;
    }


    /*
     * 新建临时素材
     *
     * By TerryQi
     *
     * 2018-09-11
     *
     */
    public static function createMediaId($filepath, $app)
    {
        Utils::processLog(__METHOD__, '', " " . "createMediaId filepath:" . $filepath);
        $result = $app->media->uploadImage($filepath);
        Utils::processLog(__METHOD__, '', "app->material->uploadImage file exists result:" . json_encode($result));
        return $result['media_id'];
    }


    /*
     * 配置文本消息
     *
     * By TerryQi
     *
     * 2018-09-12
     */
    public static function setTextMessage($text_val)
    {
        $text = new Text($text_val);
        return $text;
    }

    /*
     * 配置图片消息
     *
     * By TerryQi
     *
     * 2018-09-12
     */
    public static function setImageMessage($media_id)
    {
        $image = new Image($media_id);
        return $image;
    }
}