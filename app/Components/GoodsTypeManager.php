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
use App\Models\GoodsType;
use Qiniu\Auth;

class GoodsTypeManager
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
        $info = GoodsType::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-07
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];
        $info->status_str = Utils::GOODS_TYPE_STATUS_VAL[$info->status];
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
        $infos = new GoodsType();
        //相关条件
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $infos = $infos->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }

        $infos = $infos->orderby('busi_name', 'desc')->orderby('seq', 'desc')->orderby('id', 'desc');
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
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('flag', $data)) {
            $info->flag = array_get($data, 'flag');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        return $info;
    }
}