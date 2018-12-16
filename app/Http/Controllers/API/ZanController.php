<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\FTableManager;
use App\Components\GuanZhuManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\ZanManager;
use App\Http\Controllers\ApiResponse;
use App\Models\GuanZhu;
use App\Models\Zan;
use Illuminate\Http\Request;

class ZanController
{
    /*
     * 点赞接口
     *
     * By TerryQi
     *
     * 2018-09-19
     *
     */
    public function setZan(Request $request)
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
        $zans = ZanManager::getListByCon($con_arr, false);
        if ($zans->count() > 0) {
            return ApiResponse::makeResponse(false, "已经点赞", ApiResponse::INNER_ERROR);
        }
        //点赞
        $zan = new Zan();
        $zan = ZanManager::setInfo($zan, $data);
        $zan->save();

        //统计数据
        FTableManager::addStatistics($data['f_table'], $data['f_id'], 'zan_num', 1);

        return ApiResponse::makeResponse(true, "点赞成功", ApiResponse::SUCCESS_CODE);
    }

}





