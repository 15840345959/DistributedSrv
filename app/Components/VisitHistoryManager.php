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
use App\Models\VisitHistory;
use Qiniu\Auth;

class VisitHistoryManager
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
        $info = VisitHistory::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-07
     *
     * 0:代表有关注用户信息 1:代表有访问对象信息
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
        $infos = new VisitHistory();
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
        return $info;
    }
}