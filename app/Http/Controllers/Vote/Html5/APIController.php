<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\Vote\Html5;

use App\Components\ADManager;
use App\Components\AdminManager;
use App\Components\BusiWordManager;
use App\Components\DateTool;
use App\Components\LoginManager;
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
use App\Libs\CommonUtils;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteComplain;
use App\Models\Vote\VoteGuanZhu;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteRecord;
use App\Models\Vote\VoteShareRecord;
use App\Models\Vote\VoteTeam;
use App\Models\Vote\VoteUser;
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

    //相关配置


    /*
     * 举报接口
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public function complain(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //封装参数
        $voteComplain = new VoteComplain();
        $voteComplain = VoteComplainManager::setInfo($voteComplain, $data);
        $voteComplain->save();
        //进行数据统计
        $activity = VoteActivityManager::getById($data['activity_id']);
        if ($activity) {
            VoteActivityManager::addStatistics($activity->id, 'complain_num', 1);
        }
        //补充手机号
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user = UserManager::getByIdWithToken($data['user_id']);
            if (Utils::isObjNull($user->phonenum)) {
                $user->phonenum = $data['phonenum'];
                $user->save();
            }
        }
        //发送短信息通知
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $activity = VoteActivityManager::getById($data['activity_id']);
            if ($activity && $activity->c_admin_id1) {
                $admin = AdminManager::getById($activity->c_admin_id1); //获取第一责任人
                //向第一责任人发送短信
                if ($admin && $admin->phonenum) {
                    SMSManager::sendSMS($admin->phonenum, Utils::VOTE_SMS_TEMPLATE_COMPLAIN, $activity->name);
                }
            }
        }

        return ApiResponse::makeResponse(true, $voteComplain, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 投票接口
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public function vote(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'vote_user_id' => 'required',
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "缺少参数", ApiResponse::MISSING_PARAM);
        }
        $vote_user = VoteUserManager::getById($data['vote_user_id']);
        if (!$vote_user) {
            return ApiResponse::makeResponse(false, "没有找到选手", ApiResponse::INNER_ERROR);
        }
        //关联的活动
        $activity = VoteActivityManager::getById($vote_user->activity_id);
        if (!$activity) {
            return ApiResponse::makeResponse(false, "没有找到活动", ApiResponse::INNER_ERROR);
        }
        //条件控制-投票开始时间、结束时间控制
        if ($activity->vote_status == '0') {
            return ApiResponse::makeResponse(false, "投票还未开始", ApiResponse::INNER_ERROR);
        }
        if ($activity->vote_status == '2') {
            return ApiResponse::makeResponse(false, "投票已经结束", ApiResponse::INNER_ERROR);
        }
//        dd($activity);
        //是否达到个人投票总数
        $con_arr = array(
            'user_id' => $data['user_id'],
            'at_date' => date("Y-m-d"),
        );
        Utils::processLog(__METHOD__, '', "投票次数查询接口 " . "wx_order:" . json_encode($con_arr));

        $today_vote_num = VoteRecordManager::getListByCon($con_arr, false)->count();
        if ($today_vote_num >= $activity->daily_vote_num) {
            return ApiResponse::makeResponse(false, "今日投票已用完", ApiResponse::VOTE_OUTOF_NUM);
        }
        //是否达到给某个用户的投票总数
        $con_arr = array(
            'user_id' => $data['user_id'],
            'vote_user_id' => $vote_user->id,
            'at_date' => date("Y-m-d"),
        );
        $today_to_vote_user_vote_num = VoteRecordManager::getListByCon($con_arr, false)->count();
        if ($today_to_vote_user_vote_num >= $activity->daily_vote_to_user_num) {
            return ApiResponse::makeResponse(false, "今日投票已用完", ApiResponse::VOTE_OUTOF_NUM);
        }
        //记录投票记录
        $vote_record = new VoteRecord();
        $vote_record->user_id = $data['user_id'];
        $vote_record->vote_user_id = $vote_user->id;
        $vote_record->vote_num = 1;
        $vote_record->activity_id = $activity->id;
        $vote_record->save();

        //数据统计
        VoteActivityManager::addStatistics($activity->id, 'vote_num', 1);       //大赛投票数增加
        VoteUserManager::addStatistics($data['vote_user_id'], 'vote_num', 1);      //选手投票数增加

        return ApiResponse::makeResponse(true, $vote_record, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 参赛用户被转发-激活参赛用户
     *
     * By TerryQi
     *
     * 2018-07-21
     */
    public function shareVoteUser(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'vote_user_id' => 'required',
            'type' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //记录分享
        $shareRecord = new VoteShareRecord();
        $shareRecord = VoteShareRecordManager::setInfo($shareRecord, $data);
        $shareRecord->save();
        //激选手并进行统计
        $vote_user = VoteUserManager::getById($data['vote_user_id']);
        $vote_user->valid_status = '1';
        $vote_user->save();
        //数据统计
        VoteUserManager::addStatistics($vote_user->id, 'share_num', '1');
        VoteActivityManager::addStatistics($vote_user->activity_id, 'share_num', '1');

        return ApiResponse::makeResponse(true, $vote_user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 选手报名
     *
     * By TerryQi
     *
     * 2018-07-21
     */
    public function apply(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'activity_id' => 'required',
            'type' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //关联的活动
        $activity = VoteActivityManager::getById($data['activity_id']);
        if (!$activity) {
            return ApiResponse::makeResponse(false, "没有找到活动", ApiResponse::INNER_ERROR);
        }
        //条件控制-报名开始时间、结束时间控制
        if ($activity->apply_status == '0') {
            return ApiResponse::makeResponse(false, "报名还未开始", ApiResponse::INNER_ERROR);
        }
        if ($activity->apply_status == '2') {
            return ApiResponse::makeResponse(false, "报名已经结束", ApiResponse::INNER_ERROR);
        }
        //判断是否在重复提交
        /*
         * 2018-10-13，避免用户重复提交报名申请
         *
         * By TerryQi
         *
         * 逻辑为在某个活动下，在待审核的选手列表中有重名的用户
         *
         */
        $con_arr = array(
            'activity' => $data['activity_id'],       //同一活动
            'audit_status' => '0',                //待审核
            'name' => $data['name']
        );
        $audit_status0_vote_users = VoteUserManager::getListByCon($con_arr, false);
        if ($audit_status0_vote_users->count() != 0) {
            return ApiResponse::makeResponse(false, "您的报名信息正在审核，请耐心等待", ApiResponse::VOTE_ALREADY_APPLY);
        }
        $vote_user = new VoteUser();
        // 2018.11.21 阿伟提出如果是前台通过机构报名的不需要审核直接通过
        /*
         * 2018.11.23 优化了逻辑，person页面当待审核时，不会引导用户跳转，所以此处逻辑可以去除，但暂时先保留
         *
         * By TerryQi
         */
        if (array_get($data, 'type') == '0') {
            $data['audit_status'] = 1;
        }
        $vote_user = VoteUserManager::setInfo($vote_user, $data);
        $vote_user->save();
        //记录编号
        VoteUserManager::setCode($vote_user);
        //增加活动统计记录
        VoteActivityManager::addStatistics($vote_user->activity_id, 'join_num', 1);

        //进行短信通知
        if ($activity->c_admin_id1) {
            $admin = AdminManager::getById($activity->c_admin_id1);
            //向第一责任人发送短信
            if ($admin && $admin->phonenum) {
                $sms_text = $activity->name;
                SMSManager::sendSMS($admin->phonenum, Utils::VOTE_SMS_TEMPLATE_AUDIT_NOTICE
                    , $sms_text);
                Utils::processLog(__METHOD__, '', "活动 " . "activity:" . $activity->name . " 管理员:" . $admin->name . " 短信内容:" . $sms_text);
            }
        }

        return ApiResponse::makeResponse(true, $vote_user, ApiResponse::SUCCESS_CODE);
    }


    /*
    * 关注选手
    *
    * By TerryQi
    *
    * 2018-07-21
    */
    public function guanzhu(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'vote_user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //选手信息
        $vote_user = VoteUserManager::getById($data['vote_user_id']);
        if (!$vote_user) {
            return ApiResponse::makeResponse(false, "未找到选手", ApiResponse::INNER_ERROR);
        }
        $con_arr = array(
            'user_id' => $data['user_id'],
            'vote_user_id' => $data['vote_user_id']
        );
        $guanzhu = VoteGuanZhuManager::getListByCon($con_arr, false)->first();
        if (!$guanzhu) {
            $guanzhu = new VoteGuanZhu();
            $guanzhu = VoteGuanZhuManager::setInfo($guanzhu, $data);
            $guanzhu->save();
        }
        //增加选手统计记录
        VoteUserManager::addStatistics($vote_user->id, 'fans_num', 1);

        return ApiResponse::makeResponse(true, $vote_user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 支付礼品订单
     *
     * By TerryQi
     *
     * 2018-07-24
     */
    public function payOrder(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'vote_user_id' => 'required',
            'gift_id' => 'required',
            'gift_num' => 'required',
        ]);

        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //未找到礼品
        $vote_gift = VoteGiftManager::getById($data['gift_id']);
        if (!$vote_gift) {
            return ApiResponse::makeResponse(false, "未找到礼品", ApiResponse::INNER_ERROR);
        }
        //选手信息
        $vote_user = VoteUserManager::getById($data['vote_user_id']);
        if (!$vote_user) {
            return ApiResponse::makeResponse(false, "选手不存在", ApiResponse::INNER_ERROR);
        }
        //活动信息
        $activity = VoteActivityManager::getById($vote_user->activity_id);
        if (!$activity) {
            return ApiResponse::makeResponse(false, "活动不存在", ApiResponse::INNER_ERROR);
        }
        //条件控制-投票开始时间、结束时间控制
        if ($activity->vote_status == '0') {
            return ApiResponse::makeResponse(false, "投票还未开始", ApiResponse::INNER_ERROR);
        }
        if ($activity->vote_status == '2') {
            return ApiResponse::makeResponse(false, "投票已经结束", ApiResponse::INNER_ERROR);
        }
//        dd($activity);
        //计算订单信息
        $total_fee = $vote_gift->price * $data['gift_num'];
        $as_vote_num = $vote_gift->as_vote_num * $data['gift_num'];
        //生成订单并保存
        $vote_order = new VoteOrder();
        $vote_order = VoteOrderManager::setInfo($vote_order, $data);
        $vote_order->total_fee = $total_fee;
        $vote_order->activity_id = $activity->id;
        $vote_order->as_vote_num = $as_vote_num;
        $vote_order->trade_no = Utils::generateTradeNo();
        $vote_order->content = $activity->name . " " . $vote_user->name;
        $vote_order->remark = $activity->name . " " . $vote_user->name;
        $vote_order->save();
        Utils::processLog(__METHOD__, '', "新建订单 " . "wx_order:" . json_encode($vote_order));

        //获取用户的openid
        $con_arr = array(
            'user_id' => $data['user_id'],
            'account_type' => 'fwh',
            'busi_name' => 'isart'
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        if (!$login) {
            return ApiResponse::makeResponse(false, "未找到用户", ApiResponse::INNER_ERROR);
        }
        $wx_order = array(
            'body' => $activity->code . '-' . $vote_user->code,
            'out_trade_no' => $vote_order->trade_no,
            'total_fee' => $vote_order->total_fee * 100,
            'trade_type' => 'JSAPI',
            'openid' => $login->ve_value1,
        );
        Utils::processLog(__METHOD__, '', "支付下单 " . "wx_order:" . json_encode($wx_order));
        $config = Utils::getPaymentConfig('isart');
        $result = Pay::wechat($config)->mp($wx_order);
        Utils::processLog(__METHOD__, '', "下单结果 " . "result:" . json_encode($result));

        return ApiResponse::makeResponse(true, json_decode($result), ApiResponse::SUCCESS_CODE);
    }


    /*
     * 根据活动状态获取地推团队活动
     *
     * activity_type 活动状态（1：进行中；2：未结算；3：已结算；）
     *
     */
    public function getTeamActivityByType(Request $request)
    {
        $data = $request->all();

        $requestValidationResult = RequestValidator::validator($request->all(), [
            'team_id' => 'required',
            'activity_type' => 'required'
        ]);

        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        $team = VoteTeam::find(array_get($data, 'team_id'));

        if (!$team) {
            return ApiResponse::makeResponse(false, "未找到团队", ApiResponse::INNER_ERROR);
        }

        switch (array_get($data, 'activity_type')) {
            case '1':
                $activity = VoteActivity::whereNotNull('apply_start_time')
                    ->whereNotNull('vote_end_time')
                    ->where('vote_team_id', $team->id)
                    ->where('apply_start_time', '<', Carbon::now()->toDateTimeString())
                    ->where('vote_end_time', '>', Carbon::now()->toDateTimeString())
                    ->paginate(Utils::PAGE_SIZE);
                break;
            case '2':
                $activity = VoteActivity::whereNotNull('apply_start_time')
                    ->whereNotNull('vote_end_time')
                    ->where('vote_team_id', $team->id)
                    ->where('is_settle', 0)
                    ->paginate(Utils::PAGE_SIZE);
                break;
            case '3':
                $activity = VoteActivity::whereNotNull('apply_start_time')
                    ->whereNotNull('vote_end_time')
                    ->where('vote_team_id', $team->id)
                    ->where('is_settle', 1)
                    ->paginate(Utils::PAGE_SIZE);
                break;
        }

        return ApiResponse::makeResponse(true, $activity, ApiResponse::SUCCESS_CODE);

    }
}
