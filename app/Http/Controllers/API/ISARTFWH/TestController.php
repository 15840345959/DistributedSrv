<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\ISARTFWH;


use App\Components\Utils;
use App\Components\XCXTplMessageManager;
use App\Components\YSB\YSBADManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class TestController
{

    const ACCOUNT_CONFIG = Utils::OFFICIAL_ACCOUNT_CONFIG_VAL['isart'];     //配置文件位置

    /*
     * 发送模板消息的测试
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function getUnionid(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'openid' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $app = app(self::ACCOUNT_CONFIG);

        $openid = $data['openid'];

        $wechat_user = $app->user->get($openid);        //通过用户openid获取信息

        return ApiResponse::makeResponse(true, $wechat_user, ApiResponse::SUCCESS_CODE);
    }

}





