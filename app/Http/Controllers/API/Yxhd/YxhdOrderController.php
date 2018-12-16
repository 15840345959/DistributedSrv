<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\Yxhd;


use App\Components\DateTool;
use App\Components\ScoreRecordManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\Yxhd\YxhdActivityManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\Yxhd\YxhdOrderManager;
use App\Components\Yxhd\YxhdPrizeManager;
use App\Http\Controllers\ApiResponse;
use App\Models\ScoreRecord;
use App\Models\Yxhd\YxhdOrder;
use Illuminate\Http\Request;

class YxhdOrderController
{

    /*
     * 进行抽奖
     *
     * By TerryQi
     *
     * 2018-06-11
     *
     * @抽奖成功返回奖品信息，需要进行比对
     */
    public function draw(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'activity_id' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //获取用户信息
        $user = UserManager::getById($data['user_id']);
        if (!$user) {
            return ApiResponse::makeResponse(false, "未找到用户信息", ApiResponse::INNER_ERROR);
        }
        //获取活动信息
        $yxhdActivity = YxhdActivityManager::getById($data['activity_id']);
        if (!$yxhdActivity) {
            return ApiResponse::makeResponse(false, "未找到活动信息", ApiResponse::INNER_ERROR);
        }
        //用户积分小于参与活动所需积分
        if ($user->score < $yxhdActivity->join_score) {
            return ApiResponse::makeResponse(false, ApiResponse::$returnMessage[ApiResponse::USER_SCORE_LACK], ApiResponse::USER_SCORE_LACK);
        }
        //扣减积分额度
        $deduct_score = $yxhdActivity->join_score;
        //扣减用户积分
        $scoreRecord_info = array(
            'user_id' => $user->id,
            'score' => $deduct_score,
            'remark' => $yxhdActivity->name . "活动抽奖",
            'opt' => '0'
        );
        $scoreRecord = ScoreRecordManager::change($scoreRecord_info);
        Utils::processLog(__METHOD__, '', "扣减用户积分记录：" . json_encode($scoreRecord));
        //如果抽奖记录为空
        if ($scoreRecord == null) {
            return ApiResponse::makeResponse(false, "用户扣减积分失败", ApiResponse::DEDUCT_SCORE_ERROR);
        }
        //扣减积分成功，则进行抽奖
        $yxhdPrize_id = YxhdOrderManager::draw($yxhdActivity->id);
        Utils::processLog(__METHOD__, '', "抽奖记录 yxhdPirze_id：" . json_encode($yxhdPrize_id));
        $yxhdPrize = YxhdPrizeManager::getById($yxhdPrize_id);
        Utils::processLog(__METHOD__, '', "抽奖记录 yxhdPrize：" . json_encode($yxhdPrize));
        //生成抽奖订单
        $yxhdOrder = new YxhdOrder();
        $yxhdOrder->trade_no = Utils::generateTradeNo();
        $yxhdOrder->user_id = $user->id;
        $yxhdOrder->activity_id = $yxhdActivity->id;
        $yxhdOrder->total_score = $deduct_score;
        $yxhdOrder->pay_at = DateTool::getCurrentTime();
        $yxhdOrder->pay_status = '1';
        $yxhdOrder->winning_status = ($yxhdPrize == null) ? '0' : '1';
        $yxhdOrder->prize_id = $yxhdPrize_id;
        $yxhdOrder->save();
        Utils::processLog(__METHOD__, '', "抽奖订单信息 yxhdOrder：" . json_encode($yxhdOrder));
        //补全抽奖记录信息
        $scoreRecord->f_table = Utils::F_TABLE_YXHD_ORDER;
        $scoreRecord->f_id = $yxhdOrder->id;
        $scoreRecord->save();

        return ApiResponse::makeResponse(true, $yxhdPrize, ApiResponse::SUCCESS_CODE);
    }

}





