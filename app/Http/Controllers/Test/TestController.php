<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\Test;

use App\Components\ADManager;
use App\Components\AdminManager;
use App\Components\BusiWordManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteADManager;
use App\Components\Vote\VoteGuanZhuManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteRecordManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\Admin\Vote\VoteOrderController;
use App\Libs\CommonUtils;
use App\Models\Vote\VoteOrder;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use App\Models\Admin;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Support\Facades\Log;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;

class TestController
{

    //相关配置
    const BUSI_NAME = "isart";      //业务名称

    /*
     * 小程序map测试
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function map(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        //生成微信JS-SDK相关
        $wxConfig = null;
        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
        $wxConfig = $app->jssdk->buildConfig(array('getLocation', 'openLocation'), false);
        Utils::processLog(__METHOD__, '', " " . "wxConfig:" . json_encode($wxConfig));

        return view('test.countrymap', ['wxConfig' => $wxConfig]);
    }


    /*
     * 小程序微信config测试
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function wxConfig(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'url' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //生成微信JS-SDK相关
        $wxConfig = null;
        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
        Utils::processLog(__METHOD__, '', " " . "url:" . $data['url']);
        $app->jssdk->setUrl($data['url']);
        $wxConfig = $app->jssdk->buildConfig(array('getLocation', 'openLocation'), false);
        Utils::processLog(__METHOD__, '', " " . "wxConfig:" . json_encode($wxConfig));
        return ApiResponse::makeResponse(true, json_decode($wxConfig), ApiResponse::SUCCESS_CODE);
    }

}
