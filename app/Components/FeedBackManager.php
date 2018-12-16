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
use App\Models\FeedBack;
use Qiniu\Auth;

class FeedBackManager
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
        $info = FeedBack::where('id', '=', $id)->first();
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
        if ($info->img) {
            $info->img_arr = implode(',', $info->img);
        } else {
            $info->img_arr = [];
        }
        $info->status_str = Utils::FEEDBACK_STATUS_VAL[$info->status];

        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
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
        $infos = new FeedBack();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
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
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('content', $data)) {
            $info->content = array_get($data, 'content');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        return $info;
    }

}