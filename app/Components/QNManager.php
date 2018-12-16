<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use Illuminate\Support\Facades\Storage;
use Qiniu\Auth;

class QNManager
{

    /*
     * 获取七牛upload token
     *
     * By TerryQi
     *
     */
    public static function uploadToken()
    {
        $disk = Storage::disk('qiniu');
        $token = $disk->getUploadToken();
        return $token;
    }
}