<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\ArticleManager;
use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhCouponManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinArticleManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\Mryh\MryhUserManager;
use App\Components\Mryh\MryhWithdrawCashManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Components\Mryh\MryhSendXCXTplMessageManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinArticle;
use App\Models\Mryh\MryhJoinOrder;
use App\Models\Mryh\MryhWithdrawCash;
use App\Models\XCXForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class MryhWithdrawCashController
{

    //相关配置
    const ACCOUNT_CONFIG = "wechat.payment.mryh";     //配置文件位置
    const BUSI_NAME = "mryh";      //业务名称


    /*
     * 获取全部待提现的活动及总金额
     *
     * By TerryQi
     *
     * 2018-10-31
     */
    public function getListByWaitingJieSuan(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user_id = $data['user_id'];

        //条件1：用户是否还有待提现的活动参与记录
        $con_arr = array(
            'user_id' => $user_id,
            'game_status' => '1', //成功
            'jiesuan_status' => '0',       //未结算
            'clear_status' => '1',        //已经清分      2018-12-10 此处参与记录join表的清分状态，避免活动结束、未清分时间差上的问题
            'bigger_than_jiesuan_price' => '0'        //2018-12-08 此处，结算金额大于0，此处是为了解决目前myrh_join中没有清分标识的问题，那么结算金额只展示大于零的参赛记录，这样不会在用户提现列表中有为0的信息，不会引起用户的歧义
        );
        $waitingJiesuan_mryhJoins = MryhJoinManager::getListByCon($con_arr, false);

        //匹配信息
        foreach ($waitingJiesuan_mryhJoins as $waitingJiesuan_mryhJoin) {
            $waitingJiesuan_mryhJoin = MryhJoinManager::getInfoByLevel($waitingJiesuan_mryhJoin, '0');
            unset($waitingJiesuan_mryhJoin->game->intro_html);
        }

        $amount = 0;
        $join_ids_arr = [];
        foreach ($waitingJiesuan_mryhJoins as $waitingJiesuan_mryhJoin) {
            $amount += $waitingJiesuan_mryhJoin->jiesuan_price;
        }

        //amount保留两位小数
        /*
         * 2018-12-10 进行优化
         */
        $amount = round($amount, 2);

        $page_data = array(
            'amount' => $amount,
            'waitingJieSuan_joins' => $waitingJiesuan_mryhJoins
        );

        return ApiResponse::makeResponse(true, $page_data, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 申请提现接口
     *
     * By TerryQi
     *
     * 2018-08-17
     */
    public function apply(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //涉及到金钱，必须有较严格的合规校验！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！
        //！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！
        //无论谁接手此处代码，必须严格考虑合规校验，所有修改必须告知TerryQi和阿伟
        $user_id = $data['user_id'];
        //条件1：用户今天是否重复提交-用户每日只可以提交一个申请
        $con_arr = array(
            'user_id' => $user_id,
            'start_time' => DateTool::getToday(),
        );
        $today_WithdrawCash_num = MryhWithdrawCashManager::getListByCon($con_arr, false)->count();
//        dd($today_WithdrawCash_num);
        if ($today_WithdrawCash_num >= 1) {
            return ApiResponse::makeResponse(false, "用户每日只允许提现一次", ApiResponse::INNER_ERROR);
        }
        //条件2：用户是否还有待提现的活动参与记录
        $con_arr = array(
            'user_id' => $user_id,
            'game_status' => '1', //成功
            'clear_status' => '1',        //2018-12-10 已经清分
            'jiesuan_status' => '0'       //未结算
        );
        $waitingJiesuan_mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        if ($waitingJiesuan_mryhJoins->count() == 0) {
            return ApiResponse::makeResponse(false, "用户没有待结算的参与活动记录", ApiResponse::INNER_ERROR);
        }
        //条件3：用户参与每天一画活动信息
        $mryhUser = MryhUserManager::getByUserId($user_id);
        if (!$mryhUser) {
            return ApiResponse::makeResponse(false, "没有每天一画用户信息", ApiResponse::INNER_ERROR);
        }
        //条件4：获取用户每天一画登录信息
        $con_arr = array(
            'user_id' => $user_id,
            'busi_name' => self::BUSI_NAME
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        if (!$login) {
            return ApiResponse::makeResponse(false, "没有用户登录信息", ApiResponse::INNER_ERROR);
        }
        //计算结算金额
        $amount = 0;
        $join_ids_arr = [];
        foreach ($waitingJiesuan_mryhJoins as $waitingJiesuan_mryhJoin) {
            $amount += $waitingJiesuan_mryhJoin->jiesuan_price;
        }
        $amount = round($amount, 2);        //2018-12-10，结算金额设置为保留两位小数
        //不足1元，无法提现
        if ($amount < 1) {
            return ApiResponse::makeResponse(false, "提现金额不足1元，无法提现", ApiResponse::INNER_ERROR);
        }

        //立即生成提现记录，确保每个用户一天只能提现一次
        $trade_no = Utils::generateTradeNo();
        $mryhWithdrawCash = new MryhWithdrawCash();
        $mryhWithdrawCash->trade_no = $trade_no;
        $mryhWithdrawCash->save();

        //将带结算的相关数据设置为已结算
        foreach ($waitingJiesuan_mryhJoins as $waitingJiesuan_mryhJoin) {
            $waitingJiesuan_mryhJoin->jiesuan_status = '1';
            $waitingJiesuan_mryhJoin->jiesuan_time = DateTool::getCurrentTime();
            $waitingJiesuan_mryhJoin->save();
            array_push($join_ids_arr, $waitingJiesuan_mryhJoin->id);
        }

        $join_ids = implode(',', $join_ids_arr);
        //建立结算申请订单
        $mryhWithdrawCash->user_id = $user_id;
        $mryhWithdrawCash->openid = $login->ve_value1;
        $mryhWithdrawCash->join_ids = $join_ids;
        $mryhWithdrawCash->withdraw_at = DateTool::getCurrentTime();
        $mryhWithdrawCash->amount = $amount;
        $mryhWithdrawCash->desc = "每天一画提现，获胜奖励金";
        $mryhWithdrawCash->withdraw_status = '0';       //提现中
        $mryhWithdrawCash->save();
        Utils::processLog(__METHOD__, '', " " . "befor pay mryhWithdrawCash:" . json_encode($mryhWithdrawCash));
        //进行提现动作
        $app = app(self::ACCOUNT_CONFIG);
        $result = MryhWithdrawCashManager::sendPrize($app, $mryhWithdrawCash->trade_no, $mryhWithdrawCash->openid
            , 'NO_CHECK', '', $mryhWithdrawCash->amount, $mryhWithdrawCash->desc);
        Utils::processLog(__METHOD__, '', " " . "result:" . json_encode($result));

        //2018-11-26日，进行提现form的通知
        if (array_key_exists('form_id', $data) && !Utils::isObjNull($data['form_id'])) {
            $xcxForm = new XCXForm();
            $xcxForm->user_id = $user_id;
            $xcxForm->busi_name = self::BUSI_NAME;
            $xcxForm->form_id = $data['form_id'];
            $xcxForm->total_num = 1;        //提现表单total_num=1
            $xcxForm->f_table = Utils::F_TABLE_MRYH_WITHDRAW;
            $xcxForm->f_id = $mryhWithdrawCash->id;
            $xcxForm->save();
        }
        //根据提现结果，进行管理
        if ($result) {
            $mryhWithdrawCash->withdraw_status = '1';
            $mryhWithdrawCash->pay_at = DateTool::getCurrentTime();
            $mryhWithdrawCash->save();
            Utils::processLog(__METHOD__, '', " " . "after pay mryhWithdrawCash:" . json_encode($mryhWithdrawCash));
            //发送小程序通知
            MryhSendXCXTplMessageManager::sendWithdrawNotify($mryhWithdrawCash->id);
            return ApiResponse::makeResponse(true, $mryhWithdrawCash, ApiResponse::SUCCESS_CODE);
        } else {
            $mryhWithdrawCash->withdraw_status = '2';
            $mryhWithdrawCash->save();
            //发送小程序通知
            MryhSendXCXTplMessageManager::sendWithdrawNotify($mryhWithdrawCash->id);
            return ApiResponse::makeResponse(false, "提现失败，请联系管理员处理", ApiResponse::INNER_ERROR);
        }
    }


    /*
     * 获取提现申请列表
     *
     * By TerryQi
     *
     * 2018-11-26
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
        $withdraw_status = null;
        $level = "0";
        //配置信息
        if (array_key_exists('withdraw_status', $data) && !Utils::isObjNull($data['withdraw_status'])) {
            $withdraw_status = $data['withdraw_status'];
        }
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }

        $con_arr = array(
            'withdraw_status' => $withdraw_status,
            'user_id' => $data['user_id']
        );
        $mryh_withdrawCashs = MryhWithdrawCashManager::getListByCon($con_arr, true);
        foreach ($mryh_withdrawCashs as $mryh_withdrawCash) {
            $mryh_withdrawCash = MryhWithdrawCashManager::getInfoByLevel($mryh_withdrawCash, $level);
        }
        return ApiResponse::makeResponse(true, $mryh_withdrawCashs, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 根据id获取提现信息明细
     *
     * By TerryQi
     *
     * 2018-11-26
     */
    public function getById(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $mryh_withdrawCash = MryhWithdrawCashManager::getById($data['id']);
        if (!$mryh_withdrawCash) {
            return ApiResponse::makeResponse(false, "未找到提现记录", ApiResponse::INNER_ERROR);
        }
        $mryh_withdrawCash = MryhWithdrawCashManager::getInfoByLevel($mryh_withdrawCash, '01');
        return ApiResponse::makeResponse(true, $mryh_withdrawCash, ApiResponse::SUCCESS_CODE);
    }

}





