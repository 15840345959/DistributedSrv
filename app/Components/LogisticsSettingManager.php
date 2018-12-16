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
use App\Models\LogisticsSetting;
use Qiniu\Auth;

class LogisticsSettingManager
{

    /*
     * 根据id获取信息
     *
     * By TerryQi
     *
     * 2018-3-19
     */
    public static function getById($id)
    {
        $info = LogisticsSetting::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-07
     *
     * 0:带管理员信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];
        $info->status_str = Utils::LOGISTICS_STATUS_VAL[$info->status];
        if (array_key_exists($info->com, Utils::LOGISTICS_COM_VAL) && !Utils::LOGISTICS_COM_VAL[$info->com]) {
            $info->com_str = Utils::LOGISTICS_COM_VAL[$info->com];
        }

        if (strpos($level, '0') !== false) {
            $info->admin = AdminManager::getById($info->admin_id);
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
        $infos = new LogisticsSetting();
        //相关条件
        if (array_key_exists('com', $con_arr) && !Utils::isObjNull($con_arr['goods_id'])) {
            $infos = $infos->where('com', '=', $con_arr['com']);
        }

        $infos = $infos->orderby('seq', 'desc')->orderby('id', 'desc');
        //配置规则
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


    /*
     * 设置信息信息，用于编辑
     *
     * By TerryQi
     *
     * 2018-3-19
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('price', $data)) {
            $info->price = array_get($data, 'price');
        }
        if (array_key_exists('com', $data)) {
            $info->com = array_get($data, 'com');
        }
        if (array_key_exists('desc', $data)) {
            $info->desc = array_get($data, 'desc');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
        }
        return $info;
    }
}