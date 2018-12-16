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
use App\Models\GoodsPrice;
use Qiniu\Auth;

class GoodsPriceManager
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
        $info = GoodsPrice::where('id', '=', $id)->first();
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
        $info->mode_str = Utils::GOODS_PRICE_MODE_VAL[$info->mode];
        $info->status_str = Utils::GOODS_PRICE_STATUS_VAL[$info->status];

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
        $infos = new GoodsPrice();
        //相关条件
        if (array_key_exists('goods_id', $con_arr) && !Utils::isObjNull($con_arr['goods_id'])) {
            $infos = $infos->where('goods_id', '=', $con_arr['goods_id']);
        }
        if (array_key_exists('mode', $con_arr) && !Utils::isObjNull($con_arr['mode'])) {
            $infos = $infos->where('mode', '=', $con_arr['mode']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
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
        if (array_key_exists('goods_id', $data)) {
            $info->goods_id = array_get($data, 'goods_id');
        }
        if (array_key_exists('mode', $data)) {
            $info->mode = array_get($data, 'mode');
        }
        if (array_key_exists('price', $data)) {
            $info->price = array_get($data, 'price');
        }
        if (array_key_exists('score', $data)) {
            $info->score = array_get($data, 'score');
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