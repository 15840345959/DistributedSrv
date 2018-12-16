<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2018-3-19
 * Time: 10:30
 */

namespace App\Components;

use App\Components\Utils;
use App\Models\Admin;
use App\Models\GuanZhu;
use Qiniu\Auth;

class GuanZhuManager
{

    /*
     * 根据id获取管理员
     *
     * By TerryQi
     *
     * 2018-3-19
     */
    public static function getById($id)
    {
        $info = GuanZhu::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-07
     *
     * 0:代表有关注用户信息 1:代表有粉丝用户信息
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];

        if (strpos($level, '0') !== false) {
            $info->gz_user = UserManager::getById($info->gz_user_id);
        }
        if (strpos($level, '1') !== false) {
            $info->fan_user = UserManager::getById($info->fan_user_id);
        }

        return $info;
    }


    /*
     * 根据条件获取列表
     *
     * By TerryQi
     *
     * 2018-06-06
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new GuanZhu();
        //相关条件
        if (array_key_exists('fan_user_id', $con_arr) && !Utils::isObjNull($con_arr['fan_user_id'])) {
            $infos = $infos->where('fan_user_id', '=', $con_arr['fan_user_id']);
        }
        if (array_key_exists('gz_user_id', $con_arr) && !Utils::isObjNull($con_arr['gz_user_id'])) {
            $infos = $infos->where('gz_user_id', '=', $con_arr['gz_user_id']);
        }
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $infos = $infos->where('busi_name', '=', $con_arr['busi_name']);
        }

        $infos = $infos->orderby('id', 'desc');
        //配置规则
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


    /*
     * 设置管理员信息，用于编辑
     *
     * By TerryQi
     *
     * 2018-3-19
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('fan_user_id', $data)) {
            $info->fan_user_id = array_get($data, 'fan_user_id');
        }
        if (array_key_exists('gz_user_id', $data)) {
            $info->gz_user_id = array_get($data, 'gz_user_id');
        }
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        return $info;
    }
}