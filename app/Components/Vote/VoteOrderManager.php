<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Components\UserManager;
use App\Models\Vote\VoteOrder;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class VoteOrderManager
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
        $info = VoteOrder::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据订单号查询
     *
     * By TerryQi
     *
     * 2018-07-24
     */
    public static function getByTradeNo($trade_no)
    {
        $info = VoteOrder::where('trade_no', '=', $trade_no)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带活动信息 1：带选手信息 2：带投票人信息 3:带礼品信息
     */
    public static function getInfoByLevel($info, $level)
    {

        $info->pay_status_str = Utils::VOTE_ORDER_PAY_STATUS_VAL[$info->pay_status];

        //活动信息
        if (strpos($level, '0') !== false) {
            if ($info->activity_id) {
                $activity = VoteActivityManager::getById($info->activity_id);
                $activity = VoteActivityManager::getInfoByLevel($activity, '2');
                $info->activity = $activity;
            }
        }
        //选手信息
        if (strpos($level, '1') !== false) {
            if ($info->vote_user_id) {
                $info->vote_user = VoteUserManager::getById($info->vote_user_id);
            }
        }
        //投票人信息
        if (strpos($level, '2') !== false) {
            if ($info->user_id) {
                $info->user = UserManager::getById($info->user_id);
            }
        }
        //礼品信息
        if (strpos($level, '3') !== false) {
            if ($info->gift_id) {
                $info->gift = VoteGiftManager::getById($info->gift_id);
            }
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
        if (array_key_exists('prepay_id', $data)) {
            $info->prepay_id = array_get($data, 'prepay_id');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('gift_id', $data)) {
            $info->gift_id = array_get($data, 'gift_id');
        }
        if (array_key_exists('gift_num', $data)) {
            $info->gift_num = array_get($data, 'gift_num');
        }
        if (array_key_exists('activity_id', $data)) {
            $info->activity_id = array_get($data, 'activity_id');
        }
        if (array_key_exists('vote_user_id', $data)) {
            $info->vote_user_id = array_get($data, 'vote_user_id');
        }
        if (array_key_exists('total_fee', $data)) {
            $info->total_fee = array_get($data, 'total_fee');
        }
        if (array_key_exists('content', $data)) {
            $info->content = array_get($data, 'content');
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
        $infos = new VoteOrder();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('trade_no', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('vote_user_id', $con_arr) && !Utils::isObjNull($con_arr['vote_user_id'])) {
            $infos = $infos->where('vote_user_id', '=', $con_arr['vote_user_id']);
        }
        if (array_key_exists('activity_id', $con_arr) && !Utils::isObjNull($con_arr['activity_id'])) {
            $infos = $infos->where('activity_id', '=', $con_arr['activity_id']);
        }
        if (array_key_exists('pay_status', $con_arr) && !Utils::isObjNull($con_arr['pay_status'])) {
            $infos = $infos->where('pay_status', '=', $con_arr['pay_status']);
        }
        //定位到哪天
        if (array_key_exists('at_date', $con_arr) && !Utils::isObjNull($con_arr['at_date'])) {
            $infos = $infos->whereDate('created_at', $con_arr['at_date']);
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
     * 获取某个选手-打赏人投票总数最多降序的结果集
     *
     * By TerryQi
     *
     * 2018-07-25
     *
     */
    public static function groupByUserList($vote_user_id)
    {
        //拼装sql
        $sql_str = "SELECT *,sum(as_vote_num) as total_vote_num FROM isartdb.vote_order_info where pay_status = '1' and vote_user_id = " . $vote_user_id . " group by user_id order by total_vote_num desc limit 0,3;";
        $data = DB::select($sql_str);
        return $data;
    }


}