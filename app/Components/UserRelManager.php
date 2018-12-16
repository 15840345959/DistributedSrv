<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;

use App\Components\Utils;
use App\Models\UserRel;

class UserRelManager
{

    /*
     * 根据id信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getById($id)
    {
        $userRel = UserRel::where('id', $id)->first();
        return $userRel;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-08-14
     */
    public static function getInfoByLevel($info, $level)
    {
        if (array_key_exists($info->busi_name, Utils::BUSI_NAME_VAL)) {
            $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];
        }
        $info->level_str = Utils::USER_REL_LEVEL_VAL[$info->level];
        $info->a_user = UserManager::getById($info->a_user_id);
        $info->b_user = UserManager::getById($info->b_user_id);
    }


    /*
     * 根据条件获取信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $userRels = new UserRel();
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $userRels = $userRels->where('busi_name', $con_arr['busi_name']);
        }
        if (array_key_exists('level', $con_arr) && !Utils::isObjNull($con_arr['level'])) {
            $userRels = $userRels->where('level', $con_arr['level']);
        }
        if (array_key_exists('a_user_id', $con_arr) && !Utils::isObjNull($con_arr['a_user_id'])) {
            $userRels = $userRels->where('a_user_id', $con_arr['a_user_id']);
        }
        if (array_key_exists('b_user_id', $con_arr) && !Utils::isObjNull($con_arr['b_user_id'])) {
            $userRels = $userRels->where('b_user_id', $con_arr['b_user_id']);
        }
        if (array_key_exists('start_at', $con_arr) && !Utils::isObjNull($con_arr['start_at'])) {
            $userRels = $userRels->where('created_at', '>=', $con_arr['start_at']);
        }
        $userRels = $userRels->orderby('id', 'desc');
        if ($is_paginate) {
            $userRels = $userRels->paginate(Utils::PAGE_SIZE);
        } else {
            $userRels = $userRels->get();
        }
        return $userRels;
    }

    /*
     * 配置信息
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('level', $data)) {
            $info->level = array_get($data, 'level');
        }
        if (array_key_exists('a_user_id', $data)) {
            $info->a_user_id = array_get($data, 'a_user_id');
        }
        if (array_key_exists('b_user_id', $data)) {
            $info->b_user_id = array_get($data, 'b_user_id');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        return $info;
    }

}