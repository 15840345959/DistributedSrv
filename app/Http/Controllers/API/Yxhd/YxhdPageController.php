<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\Yxhd;


use App\Components\UserManager;
use App\Components\Utils;
use App\Components\Yxhd\YxhdActivityManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\Yxhd\YxhdOrderManager;
use App\Components\Yxhd\YxhdPrizeManager;
use App\Http\Controllers\ApiResponse;
use Illuminate\Http\Request;

class YxhdPageController
{

    /*
     * 整合页面接口
     *
     * By TerryQi
     *
     * 2018-06-11
     *
     * @activity_id 活动id
     *
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //根据条件获取活动信息
        $yxhdActivity = YxhdActivityManager::getById($data['activity_id']);
        if (!$yxhdActivity) {
            return ApiResponse::makeResponse(false, "未找到活动信息", ApiResponse::INNER_ERROR);
        }
        //根据条件获取用户信息
        $user = null;
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
            if ($user == null) {
                return ApiResponse::makeResponse(false, "未找到用户信息", ApiResponse::INNER_ERROR);
            }
        }
        //配置营销活动信息
        $yxhdActivity = YxhdActivityManager::getInfoByLevel($yxhdActivity, '0');
        //获取我的抽奖记录信息
        $my_winning_yxhdOrders = YxhdOrderManager::getListByCon(['user_id' => $user->id
            , 'activity_id' => $yxhdActivity->id, 'winning_status' => '1', 'page_size' => 5], true);
        foreach ($my_winning_yxhdOrders as $my_winning_yxhdOrder) {
            $my_winning_yxhdOrder = YxhdOrderManager::getInfoByLevel($my_winning_yxhdOrder, '2');
        }
        //最近中奖用户信息
        $curr_winning_yxhdOrders = YxhdOrderManager::getListByCon(['winning_status' => '1'
            , 'activity_id' => $yxhdActivity->id, 'page_size' => 8], true);
        foreach ($curr_winning_yxhdOrders as $curr_winning_yxhdOrder) {
            $curr_winning_yxhdOrder = YxhdOrderManager::getInfoByLevel($curr_winning_yxhdOrder, '02');
        }

        $page_data = [
            'yxhdActivity' => $yxhdActivity,
            'user' => $user,
            'my_winning_yxhdOrders' => $my_winning_yxhdOrders,
            'curr_winning_yxhdOrders' => $curr_winning_yxhdOrders
        ];

        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }

}





