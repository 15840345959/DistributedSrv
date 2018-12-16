<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\GoodsManager;
use App\Components\CollectManager;
use App\Components\RequestValidator;
use App\Components\TWStepManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Goods;
use App\Models\TWStep;
use foo\bar;
use Illuminate\Http\Request;

class GoodsController
{

    /*
     * 根据id获取商品信息
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
        $level = "0";
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }
        $goods = GoodsManager::getById($data['id']);
        $goods = GoodsManager::getInfoByLevel($goods, $level);
        GoodsManager::addStatistics($goods->id, 'show_num', 1);
        return ApiResponse::makeResponse(true, $goods, ApiResponse::SUCCESS_CODE);
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
        $search_word = null;
        $goods_type_id = null;
        $recomm_flag = null;
        //配置条件
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('goods_type_id', $data) && !Utils::isObjNull($data['goods_type_id'])) {
            $goods_type_id = $data['goods_type_id'];
        }
        if (array_key_exists('recomm_flag', $data) && !Utils::isObjNull($data['recomm_flag'])) {
            $recomm_flag = $data['recomm_flag'];
        }
        $con_arr = array(
            'busi_name' => $busi_name,
            'search_word' => $search_word,
            'status' => '1',        //生效商品
            'goods_type_id' => $goods_type_id,
            'recomm_flag' => $recomm_flag
        );
        $goods = GoodsManager::getListByCon($con_arr, true);
        foreach ($goods as $good) {
            $good = GoodsManager::getInfoByLevel($good, '04');
            unset($good->content_html);      //避免数据体量太大
        }

        return ApiResponse::makeResponse(true, $goods, ApiResponse::SUCCESS_CODE);
    }

}





