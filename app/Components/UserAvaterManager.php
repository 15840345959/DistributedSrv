<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use App\Models\GuanZhu;
use App\Models\Login;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Leto\MiniProgramAES\WXBizDataCrypt;


class UserAvaterManager
{

    /*
     * 用户头像是否为腾讯链接的头像
     *
     * By TerryQi
     *
     * 2018-12-26
     */
    public static function isAvaterFromWX($avatar_url)
    {
        //头像链接中带thirdwx.qlogo.cn字符串，代表为微信链接的头像
        if (strpos($avatar_url, "thirdwx.qlogo.cn")
            || strpos($avatar_url, "wx.qlogo.cn")) {
            return true;
        }
        return false;
    }

    /*
     * 获取用户头像并下载，下载后上传七牛，并更新用户头像位置
     *
     * By TerryQi
     *
     * 2018-12-26
     */
    public static function setAvaterToQN($user_id)
    {
        $user = UserManager::getByIdWithToken($user_id);        //注意此处为withToken
        //如果不存在用户信息
        if (!$user) {
            return false;
        }
        //如果还没有头像
        if (Utils::isObjNull($user->avatar)) {
            return false;
        }
        //如果不是腾讯的头像
        if (!self::isAvaterFromWX($user->avatar)) {
            return false;
        }
        //缓存头像目录
        $local_avatar_dir = public_path('img/example');
        $local_avatar_name = Utils::downloadFile($user->avatar, $local_avatar_dir, Utils::generateTradeNo() . ".png");
        Utils::processLog(__METHOD__, '', " " . "user_id:" . $user->id . "  local_avatar_path:" . json_encode($local_avatar_name));
        $local_avatar_fileContent = file_get_contents($local_avatar_dir . "/" . $local_avatar_name);
        $qiniu_url = QNManager::upload("avatar/" . $local_avatar_name, $local_avatar_fileContent);
        Utils::processLog(__METHOD__, '', " " . "user_id:" . $user->id . "  qiniu_url:" . json_encode($qiniu_url));
        //最终要删除本地图片
        unlink($local_avatar_dir . "/" . $local_avatar_name);

        //如果存在七牛url，则更新头像
        if ($qiniu_url) {
            $user->avatar = "http://" . $qiniu_url;
            $user->save();
            return $user;
        } else {
            return null;
        }
    }

}