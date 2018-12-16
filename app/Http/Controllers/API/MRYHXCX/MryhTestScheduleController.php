<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class MryhTestScheduleController
{
    /*
     * 测试调度任务
     *
     * By TerryQi
     *
     * 2018-08-21
     */
    public function test(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'schedule_name' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $schedule_name = $data['schedule_name'];
        switch ($schedule_name) {
            case "computePirze":
                MryhGameManager::computePrize($data['computePrize_id']);
                break;
            case "joinSchedule":
                MryhJoinManager::joinSchedule();
                break;
        }

        return ApiResponse::makeResponse(true, "调用成功", ApiResponse::SUCCESS_CODE);
    }
}





