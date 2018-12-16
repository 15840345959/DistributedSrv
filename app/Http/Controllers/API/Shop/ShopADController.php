<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\Shop;


use App\Components\Shop\ShopADManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class ShopADController
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
        //配置条件
        $position = null;
        if (array_key_exists('position', $data) && !Utils::isObjNull($data['position'])) {
            $position = $data['position'];
        }
        $con_arr = array(
            'status' => '1',
            'position' => $position
        );
        $shopADs = ShopADManager::getListByCon($con_arr, false);
        foreach ($shopADs as $shopAD) {
            unset($shopAD->content_html);
        }
        return ApiResponse::makeResponse(true, $shopADs, ApiResponse::SUCCESS_CODE);
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
        $shopAD = ShopADManager::getById($data['id']);
        return ApiResponse::makeResponse(true, $shopAD, ApiResponse::SUCCESS_CODE);
    }

}





