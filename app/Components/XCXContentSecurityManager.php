<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2018-3-19
 * Time: 10:30
 */

namespace App\Components;

use App\Components\Utils;
use App\Models\Admin;
use App\Models\Zan;
use Qiniu\Auth;

class XCXContentSecurityManager
{

    /*
     * 配置小程序安全性
     *
     * By TerryQi
     *
     * 2018-3-19
     */
    public static function checkText($app, $text)
    {
        Utils::processLog(__METHOD__, 'text:', json_encode($text));
        $result = $app->content_security->checkText($text);
        Utils::processLog(__METHOD__, 'result:', json_encode($result));
        if ($result['errcode'] == "0" && $result['errmsg'] == "ok") {
            return true;
        } else {
            return false;
        }
    }
}