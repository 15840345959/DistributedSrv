<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\YSBXCX;


use App\Components\YSB\YSBADManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class YSBADController
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
        $ysbADs = YSBADManager::getListByCon($con_arr, false);
        foreach ($ysbADs as $ysbAD) {
            unset($ysbAD->content_html);
        }
        return ApiResponse::makeResponse(true, $ysbADs, ApiResponse::SUCCESS_CODE);
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
        $ysbAD = YSBADManager::getById($data['id']);
        return ApiResponse::makeResponse(true, $ysbAD, ApiResponse::SUCCESS_CODE);
    }

}





