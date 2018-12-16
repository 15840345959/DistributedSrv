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

class AMapController
{
    //百度地图坐标转换URL
    const AMAP_GEOCONV_URL = "https://api.map.baidu.com/geoconv/v1/";
    const AMAP_AK = "oXMDw3xWEGnHqPrPbFZowA7uqaM0kqUn";

    /*
     * 点赞接口
     *
     * By TerryQi
     *
     * 2018-09-19
     *
     */
    public function geoconv(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'coords' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //配置参数
        $coords = $data['coords'];
        $from = null;
        $to = null;
        if (array_key_exists('from', $data) && !Utils::isObjNull($data['from'])) {
            $from = $data['form'];
        }
        if (array_key_exists('to', $data) && !Utils::isObjNull($data['to'])) {
            $from = $data['to'];
        }

        $param = array(
            'coords' => $coords,
            'ak' => self::AMAP_AK,
            'from' => $from,
            'to' => $to
        );

        $result = json_decode(Utils::curl(self::AMAP_GEOCONV_URL, $param));
        $result = $result->result;

        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }

}





