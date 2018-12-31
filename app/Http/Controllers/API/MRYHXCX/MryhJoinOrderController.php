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
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinOrder;
use App\Models\XCXForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yansongda\Pay\Pay;

class MryhJoinOrderController
{
    /*
     * 支付订单
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function payOrder(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'game_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user = UserManager::getById($data['user_id']);
        if (!$user) {
            return ApiResponse::makeResponse(false, "没有找到用户信息", ApiResponse::INNER_ERROR);
        }
        //获取登录信息
        $con_arr = array(
            'user_id' => $data['user_id'],
            'account_type' => 'xcx',
            'busi_name' => 'mryh'
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        if (!$login) {
            return ApiResponse::makeResponse(false, "未找到用户", ApiResponse::INNER_ERROR);
        }
        //获取活动信息
        $mryhGame = MryhGameManager::getById($data['game_id']);
        if (!$mryhGame) {
            return ApiResponse::makeResponse(false, "没有找到活动信息", ApiResponse::INNER_ERROR);
        }
        //活动状态
        if ($mryhGame->status != '1') {
            return ApiResponse::makeResponse(false, "活动状态不正确", ApiResponse::INNER_ERROR);
        }
        //活动金额
        if ($mryhGame->join_price <= 0) {
            return ApiResponse::makeResponse(false, "活动金额不正确", ApiResponse::INNER_ERROR);
        }
        //是否为可以参加的状态
        if ($mryhGame->join_status != '1') {
            return ApiResponse::makeResponse(false, "活动暂不允许参与", ApiResponse::INNER_ERROR);
        }
        //用户是否已经参与过活动
        $con_arr = array(
            'user_id' => $user->id,
            'game_id' => $mryhGame->id
        );
        if (MryhJoinManager::getListByCon($con_arr, false)->count() > 0) {
            return ApiResponse::makeResponse(false, "已经参与过活动", ApiResponse::INNER_ERROR);
        }
        //如果活动有人数控制
        if ($mryhGame->max_join_num != 0) {
            if ($mryhGame->join_num >= $mryhGame->max_join_num) {
                return ApiResponse::makeResponse(false, "活动已经达到上限", ApiResponse::INNER_ERROR);
            }
        }
        //生成订单
        $mryhJoinOrder = new MryhJoinOrder();
        $mryhJoinOrder->user_id = $user->id;
        $mryhJoinOrder->game_id = $mryhGame->id;
        $mryhJoinOrder->total_fee = $mryhGame->join_price;
        $mryhJoinOrder->trade_no = Utils::generateTradeNo();
        $mryhJoinOrder->remark = "每天一画";
        $mryhJoinOrder->save();
        Utils::processLog(__METHOD__, '', "订单信息 " . "mryhJoinOrder:" . json_encode($mryhJoinOrder));
        //支付订单
        $pay_order = [
            'out_trade_no' => $mryhJoinOrder->trade_no,
            'total_fee' => $mryhJoinOrder->total_fee * 100,
            'body' => $mryhJoinOrder->remark,
            'spbill_create_ip' => env('SERVER_IP', ''),
            'openid' => $login->ve_value1,
        ];
        Utils::processLog(__METHOD__, '', "支付下单 " . "wx_order:" . json_encode($pay_order));
        $config = Utils::getPaymentConfig('mryh');
        Utils::processLog(__METHOD__, '', "支付配置 " . "config:" . json_encode($config));
        $result = Pay::wechat($config)->miniapp($pay_order);
        if ($result) {
            $mryhJoinOrder->prepay_id = explode("=", $result['package'])[1];
            $mryhJoinOrder->save();
        }
        return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 支付结果通知
     *
     * By TerryQi
     *
     * 2018-08-13
     */
    public function payNotify(Request $request)
    {
        $config = Utils::getPaymentConfig('mryh');
        $wechat = Pay::wechat($config);
        try {
            $data = $wechat->verify($request->getContent()); // 是的，验签就这么简单！
            Utils::processLog(__METHOD__, '', ' Wechat notify ', $data->all());

            //支付成功
            if ($data->result_code == "SUCCESS") {
                //总订单out_trade_no
                $out_trade_no = $data->out_trade_no;
                $mryhJoinOrder = MryhJoinOrderManager::getByTradeNo($out_trade_no);
                ///////////////////////////////////////////////////////////
                /// 此处需要代码补充
                ///
                /// 当mryhGame的max_join_num非0时，当join_num大于等于max_join_num后，需要进行订单退款
                ///
                ///////////////////////////////////////////////////////////

                //设置订单状态
                $mryhJoinOrder->pay_status = '1';
                $mryhJoinOrder->pay_at = DateTool::getCurrentTime();
                $mryhJoinOrder->save();

                //生成用户参与记录
                $mryhJoin = new MryhJoin();
                $mryhJoin->user_id = $mryhJoinOrder->user_id;
                $mryhJoin->game_id = $mryhJoinOrder->game_id;
                $mryhJoin->trade_no = $mryhJoinOrder->trade_no;
                $mryhJoin->total_fee = $mryhJoinOrder->total_fee;
                $mryhJoin->join_time = DateTool::getCurrentTime();
                $mryhJoin->save();

                //生成小程序的推送消息数
                $xcxForm = new XCXForm();
                $xcxForm->user_id = $mryhJoinOrder->user_id;
                $xcxForm->busi_name = Utils::BUSI_NAME_MRYH;
                $xcxForm->form_id = $mryhJoinOrder->prepay_id;
                $xcxForm->f_table = Utils::F_TABLE_MRYH_JOIN;
                $xcxForm->f_id = $mryhJoin->id;
                $xcxForm->total_num = 3;
                $xcxForm->save();

                //增加活动数据
                MryhGameManager::addStatistics($mryhJoinOrder->game_id, 'join_num', 1);
                MryhGameManager::addStatistics($mryhJoinOrder->game_id, 'total_money', $mryhJoinOrder->total_fee);

            }
            return $wechat->success();
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }


}





