<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhSettingManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class MryhSettingController
{
    /*
     * 获取配置信息
     *
     * By TerryQi
     *
     * 2018-8-12
     */
    public function getSetting(Request $request)
    {
        $data = $request->all();
        $con_arr = array(
            'status' => '1',
        );
        //第一条生效的业务配置信息
        $mryhSetting = MryhSettingManager::getListByCon($con_arr, false)->first();

        return ApiResponse::makeResponse(true, $mryhSetting, ApiResponse::SUCCESS_CODE);
    }

}





