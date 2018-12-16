<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhCouponManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class MryhUserCouponController
{
    /*
     * 通过优惠券参加活动
     *
     * By mtt
     *
     * 2018-08-15
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //配置条件
        $used_status = null;
        if (array_key_exists('used_status', $data) && !Utils::isObjNull($data['used_status'])) {
            $used_status = $data['used_status'];
        }
        $con_arr = array(
            'user_id' => $data['user_id'],
            'used_status' => $used_status
        );
        $mryhUserCoupons = MryhUserCouponManager::getListByCon($con_arr, false);
        foreach ($mryhUserCoupons as $mryhUserCoupon) {
            $mryhUserCoupon = MryhUserCouponManager::getInfoByLevel($mryhUserCoupon, '1');
        }
        return ApiResponse::makeResponse(true, $mryhUserCoupons, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 用户是否获取某个优惠券
     *
     * By TerryQi
     *
     * 2018-12-06
     */
    public function isUserHasCoupon(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'code' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //根据code查找优惠券
        $mryhCoupon = MryhCouponManager::getListByCon(['code' => $data['code']], false)->first();
        if (!$mryhCoupon) {
            return ApiResponse::makeResponse(false, "未找到优惠券", ApiResponse::INNER_ERROR);
        }

        //根据条件查找优惠券信息
        $con_arr = array(
            'user_id' => $data['user_id'],
            'coupon_id' => $mryhCoupon->id
        );
        $mryhUserCoupon = MryhUserCouponManager::getListByCon($con_arr, false)->first();

        return ApiResponse::makeResponse(true, $mryhUserCoupon, ApiResponse::SUCCESS_CODE);
    }

}





