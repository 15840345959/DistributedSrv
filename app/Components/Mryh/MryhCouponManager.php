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
use App\Components\UserRelManager;
use App\Components\Mryh\MryhSendXCXTplMessageManager;
use App\Models\Mryh\MryhCoupon;
use App\Models\Mryh\MryhUserCoupon;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhCouponManager
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
        $info = MryhCoupon::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带管理员信息
     */
    public static function getInfoByLevel($info, $level)
    {
        if (strpos($level, '0') !== false) {
            $info->admin = AdminManager::getById($info->admin_id);
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
        if (array_key_exists('code', $data)) {
            $info->code = array_get($data, 'code');
        }
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('intro_text', $data)) {
            $info->intro_text = array_get($data, 'intro_text');
        }
        if (array_key_exists('intro_html', $data)) {
            $info->intro_html = array_get($data, 'intro_html');
        }
        if (array_key_exists('mode', $data)) {
            $info->mode = array_get($data, 'mode');
        }
        if (array_key_exists('con_game_ids', $data)) {
            $info->con_game_ids = array_get($data, 'con_game_ids');
        }
        if (array_key_exists('con_date', $data)) {
            $info->con_date = array_get($data, 'con_date');
        }
        if (array_key_exists('con_yq_num', $data)) {
            $info->con_yq_num = array_get($data, 'con_yq_num');
        }
        if (array_key_exists('con_valid_days', $data)) {
            $info->con_valid_days = array_get($data, 'con_valid_days');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('alloc_num', $data)) {
            $info->alloc_num = array_get($data, 'alloc_num');
        }
        if (array_key_exists('used_num', $data)) {
            $info->used_num = array_get($data, 'used_num');
        }
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
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
        $infos = new MryhCoupon();
        //相关条件
        $infos = $infos->orderby('id', 'desc');
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', "%" . $con_arr['search_word'] . "%");
        }
        if (array_key_exists('code', $con_arr) && !Utils::isObjNull($con_arr['code'])) {
            $infos = $infos->where('code', '=', $con_arr['code']);
        }

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

    /*
     * 配置统计信息
     *
     * By TerryQi
     *
     * 2018-08-15
     */
    public static function addStatistics($coupon_id, $item, $num)
    {
        $coupon = self::getById($coupon_id);
        switch ($item) {
            case "alloc_num":
                $coupon->alloc_num = $coupon->alloc_num + $num;
                break;
            case "used_num":
                $coupon->used_num = $coupon->used_num + $num;
                break;
        }
        $coupon->save();
        return $coupon;
    }


    /*
     * 每天一画派发优惠券逻辑
     *
     * By TerryQi
     *
     * 2018-08-15
     *
     */
    public static function send($user_id)
    {
        //搜索现有生效的优惠券，本期只做迎新优惠券
        $con_arr = array(
            'status' => '1',
            'code' => 'FOR_FIRST_NEW'       //迎新优惠券的编码
        );
        $mryhCoupon = self::getListByCon($con_arr, false)->first();
        Utils::processLog(__METHOD__, '', json_encode($mryhCoupon));
        if (!$mryhCoupon) {
            return false;
        }
        //派发是否派发过优惠券，一个用户只能获得一次优惠券
        $con_arr = array(
            'user_id' => $user_id,
            'coupon_id' => $mryhCoupon->id
        );
        $userCoupon = MryhUserCouponManager::getListByCon($con_arr, false)->first();
        Utils::processLog(__METHOD__, '', "userCoupon:" . json_encode($userCoupon));
        if ($userCoupon) {
            return false;
        }
        //判断用户是否达到发券标准
        $con_arr = array(
            'a_user_id' => $user_id,
            'busi_name' => 'mryh',
            'level' => '0',
            'start_at' => $mryhCoupon->con_date
        );
        Utils::processLog(__METHOD__, '', " " . "total_yq_nums con_arr:" . json_encode($con_arr));
        $total_yq_nums = UserRelManager::getListByCon($con_arr, false)->count();
        Utils::processLog(__METHOD__, '', " " . "total_yq_nums:" . json_encode($total_yq_nums));
        //邀请数达标，进行优惠券派发
        if ($total_yq_nums >= $mryhCoupon->con_yq_num) {
            $userCoupon = new MryhUserCoupon();
            $userCoupon->user_id = $user_id;
            $userCoupon->coupon_id = $mryhCoupon->id;
            $userCoupon->alloc_time = DateTool::getCurrentTime();
            $userCoupon->used_status = '0';
            $userCoupon->end_time = DateTool::dateAdd('D', $mryhCoupon->con_valid_days, $userCoupon->alloc_time);
            $userCoupon->save();
            Utils::processLog(__METHOD__, '', " " . "userCoupon:" . json_encode($userCoupon));
            //发送邀请达标提醒
            MryhSendXCXTplMessageManager::sendInviteSuccessNotify($user_id);
            return true;
        }
        return false;
    }
}