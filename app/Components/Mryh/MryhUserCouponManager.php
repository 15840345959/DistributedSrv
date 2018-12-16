<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\UserManager;
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

class MryhUserCouponManager
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
        $info = MryhUserCoupon::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带用户信息 1：带优惠券信息
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->used_status_str = Utils::MRYH_COUPONS_USED_STATUS_VAL[$info->used_status];

        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        if (strpos($level, '1') !== false) {
            $info->coupon = MryhCouponManager::getById($info->coupon_id);
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
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('coupon_id', $data)) {
            $info->coupon_id = array_get($data, 'coupon_id');
        }
        if (array_key_exists('alloc_time', $data)) {
            $info->alloc_time = array_get($data, 'alloc_time');
        }
        if (array_key_exists('used_status', $data)) {
            $info->used_status = array_get($data, 'used_status');
        }
        if (array_key_exists('end_time', $data)) {
            $info->end_time = array_get($data, 'end_time');
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
        $infos = new MryhUserCoupon();
        //相关条件
        $infos = $infos->orderby('id', 'desc');
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('coupon_id', $con_arr) && !Utils::isObjNull($con_arr['coupon_id'])) {
            $infos = $infos->where('coupon_id', '=', $con_arr['coupon_id']);
        }
        if (array_key_exists('used_status', $con_arr) && !Utils::isObjNull($con_arr['used_status'])) {
            $infos = $infos->where('used_status', '=', $con_arr['used_status']);
        }

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

    /*
     * 根据用户id获取用户信息
     *
     * By TerryQi
     *
     * 2018-08-14
     */
    public static function getByUserId($user_id)
    {
        $con_arr = array(
            'user_id' => $user_id
        );
        $mryhUser = self::getListByCon($con_arr, false)->first();
        return $mryhUser;
    }

}