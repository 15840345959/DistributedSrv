<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\DateTool;
use App\Components\UserManager;
use App\Models\Mryh\MryhJoinOrder;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhJoinOrderManager
{
    /*
     * 根据id获取信息
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function getById($id)
    {
        $info = MryhJoinOrder::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据订单号查询信息
     *
     * By TerryQi
     *
     * 2018-08-13
     */
    public static function getByTradeNo($trade_no)
    {
        $info = MryhJoinOrder::where('trade_no', '=', $trade_no)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带活动信息 1：带用户信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->pay_status_str = Utils::MRYH_ORDER_PAY_STATUS_VAL[$info->pay_status];

        if (strpos($level, '0') !== false) {
            $info->game = MryhGameManager::getById($info->game_id);
        }
        if (strpos($level, '1') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        return $info;
    }


    /*
     * 设置信息，用于编辑
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('trade_no', $data)) {
            $info->trade_no = array_get($data, 'trade_no');
        }
        if (array_key_exists('refund_trade_no', $data)) {
            $info->refund_trade_no = array_get($data, 'refund_trade_no');
        }
        if (array_key_exists('prepay_id', $data)) {
            $info->prepay_id = array_get($data, 'prepay_id');
        }
        if (array_key_exists('game_id', $data)) {
            $info->game_id = array_get($data, 'game_id');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('total_fee', $data)) {
            $info->total_fee = array_get($data, 'total_fee');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        if (array_key_exists('pay_type', $data)) {
            $info->pay_type = array_get($data, 'pay_type');
        }
        if (array_key_exists('pay_status', $data)) {
            $info->pay_status = array_get($data, 'pay_status');
        }
        if (array_key_exists('pay_at', $data)) {
            $info->pay_at = array_get($data, 'pay_at');
        }
        if (array_key_exists('refund_at', $data)) {
            $info->refund_at = array_get($data, 'refund_at');
        }
        return $info;
    }

    /*
     * 获取列表
     *
     * By Amy
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new MryhJoinOrder();
        //相关条件
        if (array_key_exists('game_id', $con_arr) && !Utils::isObjNull($con_arr['game_id'])) {
            $infos = $infos->where('game_id', '=', $con_arr['game_id']);
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('pay_status', $con_arr) && !Utils::isObjNull($con_arr['pay_status'])) {
            $infos = $infos->where('pay_status', '=', $con_arr['pay_status']);
        }
        if (array_key_exists('trade_no', $con_arr) && !Utils::isObjNull($con_arr['trade_no'])) {
            $infos = $infos->where('trade_no', 'like', '%' . $con_arr['trade_no'] . '%');
        }
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('trade_no', 'like', '%' . $con_arr['search_word'] . '%');
        }

        $infos = $infos->orderby('id', 'desc');

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

    /*
     * 根据商户订单号进行退款
     *
     * By TerryQi
     *
     * 2018-08-19
     */
    public static function refundByTradeNo($app, $trade_no, $refund_desc)
    {
        $refund_trade_no = $trade_no . Utils::getRandNum(4);        //退款订单号为原订单号增加四位随机数字
        $joinOrder = self::getByTradeNo($trade_no);
        if (!$joinOrder) {
            return false;
        }
        $total_fee = $joinOrder->total_fee * 100;
        Utils::processLog(__METHOD__, '', "trade_no:" . $trade_no . " refund_trade_no:" . $refund_trade_no . ' total:' . $total_fee);
        $result = $app->refund->byOutTradeNumber($trade_no, $refund_trade_no, $total_fee, $total_fee, [
            // 可在此处传入其他参数，详细参数见微信支付文档
            'refund_desc' => $refund_desc,
        ]);
        Utils::processLog(__METHOD__, '', "result:" . json_encode($result));
        if ($result['result_code'] == "SUCCESS") {
            //退款成功，需要补充退款订单号和退款时间
            $joinOrder->refund_trade_no = $refund_trade_no;
            $joinOrder->refund_at = DateTool::getCurrentTime();
            $joinOrder->pay_status = '4';       //订单为退款状态
            $joinOrder->save();
            return true;
        } else {
            return false;
        }
    }

}