<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\Mryh\MryhCouponManager;
use App\Components\Mryh\MryhCouponsManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\UserRelManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\UserRel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserRelController
{
    /*
     * 添加关联关系
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public function add(Request $request)
    {
        return ApiResponse::makeResponse(false, "接口已经作废，请调用登录接口", ApiResponse::INNER_ERROR);

        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'busi_name' => 'required',
            'level' => 'required',
            'a_user_id' => 'required',
            'b_user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //针对level==0拉新用户，单独判断，一个用户在一个业务场景下只能被一个用户拉新
        if ($data['level'] == 0) {
            $con_arr = array(
                'busi_name' => $data['busi_name'],
                'b_user_id' => $data['b_user_id']
            );
            $userRel = UserRelManager::getListByCon($con_arr, false)->first();
            if ($userRel) {
                return ApiResponse::makeResponse(false, "该用户已非新用户，level传参错误", ApiResponse::INNER_ERROR);
            }
        }

        //建立用户间的关联关系
        $userRel = new UserRel();
        $userRel = UserRelManager::setInfo($userRel, $data);
        $userRel->save();

        //增加被邀请人数据
        if ($data['level'] == 0) {          //新用户
            UserManager::addStatistics($userRel->a_user_id, 'yq_num', 1);
        }
        if ($data['level'] == 1) {      //非新用户
            UserManager::addStatistics($userRel->a_user_id, 'rel_num', 1);
        }
        //此处添加逻辑，每天一画派发优惠券////////////////////////////////////////////////////

        $sendResult = MryhCouponManager::send($userRel->a_user_id);
        Utils::processLog(__METHOD__, '', "wxConfig:" . json_encode($wxConfig));

        //////////////////////////////////////////////////////////////////////////////////////
        return ApiResponse::makeResponse(true, $userRel, ApiResponse::SUCCESS_CODE);
    }

}





