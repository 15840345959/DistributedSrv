<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\ArticleTypeManager;
use App\Components\CollectManager;
use App\Components\RequestValidator;
use App\Components\TWStepManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\ArticleType;
use App\Models\TWStep;
use foo\bar;
use Illuminate\Http\Request;

class ArticleTypeController
{

    /*
     * 根据id获取作品分类信息
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
        $articleType = ArticleTypeManager::getById($data['id']);
        return ApiResponse::makeResponse(true, $articleType, ApiResponse::SUCCESS_CODE);
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
        //相关搜素条件
        $busi_name = null;
        //配置条件
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }

        $con_arr = array(
            'busi_name' => $busi_name,
        );
        $articleTypes = ArticleTypeManager::getListByCon($con_arr, false);

        return ApiResponse::makeResponse(true, $articleTypes, ApiResponse::SUCCESS_CODE);
    }

}





