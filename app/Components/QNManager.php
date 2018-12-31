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

    /*
     * 上传文件
     *
     * By TerryQi
     *
     * 2018-12-26
     *
     * @return 如果上传成功返回七牛url，否则返回null
     */
    public static function upload($path, $fileContent)
    {
        $disk = Storage::disk('qiniu');
        $result = $disk->put($path, $fileContent);
        if ($result) {
            return env("QINIU_DOMAIN") . "/" . $path;
        } else {
            return null;
        }
    }

}