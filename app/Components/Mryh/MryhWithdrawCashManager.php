<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\UserManager;
use App\Models\Mryh\MryhWithdrawCash;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhWithdrawCashManager
{
    /*
     * 根据id获取信息
     *
     * By TerryQi
     *
     * 2018-08-20
     */
    public static function getById($id)
    {
        $info = MryhWithdrawCash::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     *
     *  0：带用户信息     1：带参赛信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->withdraw_status_str = Utils::MRYH_WITHDRAW_CASH_WITHDRAW_STATUS[$info->withdraw_status];

        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        if (strpos($level, '1') !== false) {
            $join_ids_arr = explode(',', $info->join_ids);
//            dd($join_ids_arr);
            $mryhJoins = array();
            foreach ($join_ids_arr as $join_id) {
                $mryhJoin = MryhJoinManager::getById($join_id);
                $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '0');
                array_push($mryhJoins, $mryhJoin);
            }
            $info->mryhJoins = $mryhJoins;
        }
        return $info;
    }


    /*
     * 设置信息，用于编辑
     *
     * By TerryQi
     *
     * 2018-08-20
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('trade_no', $data)) {
            $info->trade_no = array_get($data, 'trade_no');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('openid', $data)) {
            $info->openid = array_get($data, 'openid');
        }
        if (array_key_exists('join_ids', $data)) {
            $info->join_ids = array_get($data, 'join_ids');
        }
        if (array_key_exists('withdraw_at', $data)) {
            $info->withdraw_at = array_get($data, 'withdraw_at');
        }
        if (array_key_exists('amount', $data)) {
            $info->amount = array_get($data, 'amount');
        }
        if (array_key_exists('pay_at', $data)) {
            $info->pay_at = array_get($data, 'pay_at');
        }
        if (array_key_exists('desc', $data)) {
            $info->desc = array_get($data, 'desc');
        }
        if (array_key_exists('withdraw_status', $data)) {
            $info->withdraw_status = array_get($data, 'withdraw_status');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        return $info;
    }

    /*
     * 获取列表
     *
     * By TerryQi
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new MryhWithdrawCash();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('withdraw_status', $con_arr) && !Utils::isObjNull($con_arr['withdraw_status'])) {
            $infos = $infos->where('withdraw_status', '=', $con_arr['withdraw_status']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('start_time', $con_arr) && !Utils::isObjNull($con_arr['start_time'])) {
            $infos = $infos->where('created_at', '>=', $con_arr['start_time']);
        }
        if (array_key_exists('end_time', $con_arr) && !Utils::isObjNull($con_arr['end_time'])) {
            $infos = $infos->where('created_at', '<=', $con_arr['end_time']);
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
     * 进行奖励
     *
     * By TerryQi
     *
     * 2018-08-19
     */
    public static function sendPrize($app, $partner_trade_no, $openid, $check_name, $re_user_name, $amount, $desc)
    {
        Utils::processLog(__METHOD__, '', " " . "partner_trade_no:" . $partner_trade_no . " openid:" . $openid . ' check_name:' . $check_name . ' re_user_name:' . $re_user_name . ' amcount:' . $amount . ' desc:' . $desc);
        //校验金额，不能大于100元！！！！！！！！！！！！！！！！！！！！！！！！！！
        //此处比较担心大额资金流失！！！！！！！！！！！！！！！！！！！！！！！！！
        if ($amount >= 100 * 100) {
            return false;
        }
        //！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！！
        $result = $app->transfer->toBalance([
            'partner_trade_no' => $partner_trade_no, // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
            'openid' => $openid,
            'check_name' => 'NO_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
            're_user_name' => '', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
            'amount' => $amount * 100, // 企业付款金额，单位为分
            'desc' => $desc, // 企业付款操作说明信息。必填
        ]);

        Utils::processLog(__METHOD__, '', json_encode($result));
        if ($result['result_code'] === "SUCCESS") {
            return true;
        } else {
            return false;
        }
    }
}