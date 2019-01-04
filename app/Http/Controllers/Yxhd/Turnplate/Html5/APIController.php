<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\Yxhd\Turnplate\Html5;

use App\Components\ADManager;
use App\Components\AdminManager;
use App\Components\BusiWordManager;
use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\ScoreRecordManager;
use App\Components\SMSManager;
use App\Components\UserManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteADManager;
use App\Components\Vote\VoteComplainManager;
use App\Components\Vote\VoteGiftManager;
use App\Components\Vote\VoteGuanZhuManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteRecordManager;
use App\Components\Vote\VoteShareRecordManager;
use App\Components\Vote\VoteUserManager;
use App\Components\Yxhd\YxhdActivityManager;
use App\Components\Yxhd\YxhdOrderManager;
use App\Components\Yxhd\YxhdPrizeManager;
use App\Libs\CommonUtils;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteComplain;
use App\Models\Vote\VoteGuanZhu;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteRecord;
use App\Models\Vote\VoteShareRecord;
use App\Models\Vote\VoteTeam;
use App\Models\Vote\VoteUser;
use App\Models\Yxhd\YxhdOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use App\Models\Admin;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Support\Facades\Log;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use SimpleSoftwareIO\QrCode\DataTypes\SMS;
use Yansongda\Pay\Pay;

class APIController
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

    public function test (Request $request) {
        $data = $request->all();
        for ($i = 0; $i < 20; $i++) {
            $user = UserManager::getById($data['user_id']);
            $activity = YxhdActivityManager::getById(1);
            $prize = YxhdPrizeManager::getById(1);
            $order = YxhdOrderManager::getById(1);
        }

        return ApiResponse::makeResponse(true, '', ApiResponse::SUCCESS_CODE);
    }

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
        if ($yxhdPrize_id == null) {
            $yxhdPrize_id = -1;
        }
        //生成抽奖订单
        $yxhdOrder = new YxhdOrder();
        $yxhdOrder->trade_no = Utils::generateTradeNo();
        $yxhdOrder->user_id = $user->id;
        $yxhdOrder->activity_id = $yxhdActivity->id;
        $yxhdOrder->total_score = $deduct_score;
        $yxhdOrder->pay_at = DateTool::getCurrentTime();
        $yxhdOrder->pay_status = '1';
        $yxhdOrder->winning_status = ($yxhdPrize_id == -1) ? '0' : '1';
        $yxhdOrder->prize_id = $yxhdPrize_id;
        $yxhdOrder->save();
        Utils::processLog(__METHOD__, '', "抽奖订单信息 yxhdOrder：" . json_encode($yxhdOrder));
        //补全抽奖记录信息
        $scoreRecord->f_table = Utils::F_TABLE_YXHD_ORDER;
        $scoreRecord->f_id = $yxhdOrder->id;
        $scoreRecord->save();

        //增加统计次数
        YxhdActivityManager::addStatistics($yxhdActivity->id, 'join_num', 1);

        return ApiResponse::makeResponse(true, $yxhdPrize_id, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 进行分享
     *
     * By TerryQi
     *
     * 2018-12-23
     */
    public function share(Request $request)
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

        //2018-12-23，目前只要分享，即可获得10积分，后续可以与活动或者用户相关，分级进行积分赠送
        //扣减用户积分
        $scoreRecord_info = array(
            'user_id' => $user->id,
            'score' => 10,
            'remark' => "分享活动奖励积分",
            'opt' => '1'
        );
        $scoreRecord = ScoreRecordManager::change($scoreRecord_info);
        Utils::processLog(__METHOD__, '', "增加用户积分：" . json_encode($scoreRecord));

        $scoreRecord->f_table = Utils::F_TABLE_YXHD_ACTIVITY;
        $scoreRecord->f_id = $yxhdActivity->id;
        $scoreRecord->save();

        return ApiResponse::makeResponse(true, $scoreRecord, ApiResponse::SUCCESS_CODE);
    }




}
