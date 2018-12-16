<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\ArticleManager;
use App\Components\FTableManager;
use App\Components\GuanZhuManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\CollectManager;
use App\Http\Controllers\ApiResponse;
use App\Models\GuanZhu;
use App\Models\Collect;
use Illuminate\Http\Request;

class CollectController
{
    /*
     * 收藏接口
     *
     * By TerryQi
     *
     * 2018-09-19
     *
     */
    public function setCollect(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'f_id' => 'required',
            'f_table' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $con_arr = array(
            'user_id' => $data['user_id'],
            'f_id' => $data['f_id'],
            'f_table' => $data['f_table']
        );
        $collects = CollectManager::getListByCon($con_arr, false);
        if ($collects->count() > 0) {
            return ApiResponse::makeResponse(false, "已经收藏", ApiResponse::INNER_ERROR);
        }
        //收藏
        $collect = new Collect();
        $collect = CollectManager::setInfo($collect, $data);
        $collect->save();
        //统计数据
        FTableManager::addStatistics($data['f_table'], $data['f_id'], 'coll_num', 1);

        return ApiResponse::makeResponse(true, "收藏成功", ApiResponse::SUCCESS_CODE);
    }


    /*
     * 根据条件获取列表
     *
     * By TerryQi
     *
     * 2018-09-27
     *
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'p_user_id' => 'required',
            'f_table' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $p_user_id = $data['p_user_id'];
        $f_table = $data['f_table'];
        $con_arr = array(
            'user_id' => $p_user_id,
            'f_table' => $f_table
        );
        $collects = CollectManager::getListByCon($con_arr, true);
        foreach ($collects as $collect) {
            $collect = CollectManager::getInfoByLevel($collect, '1');
        }
        return ApiResponse::makeResponse(true, $collects, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 取消收藏
     *
     * By TerryQi
     *
     * 2018-09-19
     */
    public function cancelCollect(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $collect = CollectManager::getById($data['id']);
        $collect->delete();

        //统计数据
        FTableManager::addStatistics($data['f_table'], $data['f_id'], 'coll_num', -1);

        return ApiResponse::makeResponse(true, "取消收藏", ApiResponse::SUCCESS_CODE);
    }

}





