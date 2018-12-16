<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\YSBXCX;


use App\Components\XCXTplMessageManager;
use App\Components\YSB\YSBADManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class TestController
{

    const ACCOUNT_CONFIG = "wechat.mini_program.ysb";     //配置文件位置

    /*
     * 发送模板消息的测试
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function sendTemplateMessage(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'touser' => 'required',
            'template_id' => 'required',
            'form_id' => 'required',
            'page' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $app = app(self::ACCOUNT_CONFIG);

        $param = array(
            'touser' => $data['touser'],
            'page' => $data['page'],
            'form_id' => $data['form_id']
        );

        $info = array(
            'keyword1' => '作品审核通过',
            'keyword2' => '2018年11月15日 14:18',
            'keyword3' => '您发布的作品已经审核通过'
        );

        XCXTplMessageManager::sendMessage($app, $data['template_id'], $param, $info);


        return ApiResponse::makeResponse(true, "发送模板消息", ApiResponse::SUCCESS_CODE);
    }

}





