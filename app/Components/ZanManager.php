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
use App\Models\Zan;
use Qiniu\Auth;

class ZanManager
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
        $info = Zan::where('id', '=', $id)->first();
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
        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        if (strpos($level, '1') !== false) {
            $info = FTableManager::getObjWithFtable($info, '');
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
        $infos = new Zan();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('f_table', $con_arr) && !Utils::isObjNull($con_arr['f_table'])) {
            $infos = $infos->where('f_table', '=', $con_arr['f_table']);
        }
        if (array_key_exists('f_id', $con_arr) && !Utils::isObjNull($con_arr['f_id'])) {
            $infos = $infos->where('f_id', '=', $con_arr['f_id']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
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
     * 是否赞了
     *
     * By TerryQi
     *
     * 2018-09-20
     *
     */
    public static function is_zan($user_id, $f_table, $f_id)
    {
        $con_arr = array(
            'user_id' => $user_id,
            'f_table' => $f_table,
            'f_id' => $f_id
        );
        $coll_num = self::getListByCon($con_arr, false)->count();
        if ($coll_num == 0) {
            return false;
        } else {
            return true;
        }
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
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('f_table', $data)) {
            $info->f_table = array_get($data, 'f_table');
        }
        if (array_key_exists('f_id', $data)) {
            $info->f_id = array_get($data, 'f_id');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        return $info;
    }
}