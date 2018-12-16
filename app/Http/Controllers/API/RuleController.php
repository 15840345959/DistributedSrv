<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\RuleManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class RuleController
{
    /*
     * 根据id获取信息
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
        $rule = RuleManager::getById($data['id']);
        return ApiResponse::makeResponse(true, $rule, ApiResponse::SUCCESS_CODE);
    }

}





