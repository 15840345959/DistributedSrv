<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\GoodsTypeManager;
use App\Components\CollectManager;
use App\Components\RequestValidator;
use App\Components\TWStepManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\GoodsType;
use App\Models\TWStep;
use foo\bar;
use Illuminate\Http\Request;

class GoodsTypeController
{

    /*
     * 根据id获取商品分类信息
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
        $goodsType = GoodsTypeManager::getById($data['id']);
        return ApiResponse::makeResponse(true, $goodsType, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 根据条件获取列表信息
     *
     * By TerryQi
     *
     * 2018-06-14
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        $busi_name = null;
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }

        $con_arr = array(
            'busi_name' => $busi_name,
            'status' => '1'
        );
        $goodsTypes = GoodsTypeManager::getListByCon($con_arr, false);

        return ApiResponse::makeResponse(true, $goodsTypes, ApiResponse::SUCCESS_CODE);
    }

}





