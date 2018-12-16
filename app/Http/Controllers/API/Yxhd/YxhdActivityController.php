<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\Yxhd;


use App\Components\Utils;
use App\Components\Yxhd\YxhdActivityManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\Yxhd\YxhdPrizeManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class YxhdActivityController
{

    /*
     * 根据id获取轮播图信息
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public function getById(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        //配置数据
        $level = null;
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }

        $yxhdActivity = YxhdActivityManager::getById($data['id']);
        $yxhdActivity = YxhdPrizeManager::getInfoByLevel($yxhdActivity, $level);

        return ApiResponse::makeResponse(true, $yxhdActivity, ApiResponse::SUCCESS_CODE);
    }

}





