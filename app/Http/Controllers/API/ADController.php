<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class ADController
{
    /*
     * 根据位置获取轮播图信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        $con_arr = array(
            'status' => '1',
        );
        $ads = ADManager::getListByCon($con_arr, false);
        return ApiResponse::makeResponse(true, $ads, ApiResponse::SUCCESS_CODE);
    }


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
        $ad = ADManager::getById($data['id']);
        return ApiResponse::makeResponse(true, $ad, ApiResponse::SUCCESS_CODE);
    }

}





