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
use App\Models\Comment;
use Qiniu\Auth;

class CommentManager
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
        $info = Comment::where('id', '=', $id)->first();
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
        $info->read_status_str = Utils::COMMENT_READ_STATUS_VAL[$info->read_status];
        $info->recomm_flag_str = Utils::COMMENT_RECOMM_FLAG_VAL[$info->recomm_flag];
        $info->audit_status_str = Utils::COMMENT_AUDIT_STATUS_VAL[$info->audit_status];

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
        $infos = new Comment();
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
        if (array_key_exists('show_flag', $con_arr) && !Utils::isObjNull($con_arr['show_flag'])) {
            $infos = $infos->where('show_flag', '=', $con_arr['show_flag']);
        }
        if (array_key_exists('recomm_flag', $con_arr) && !Utils::isObjNull($con_arr['recomm_flag'])) {
            $infos = $infos->where('recomm_flag', '=', $con_arr['recomm_flag']);
        }
        if (array_key_exists('audit_status', $con_arr) && !Utils::isObjNull($con_arr['audit_status'])) {
            $infos = $infos->where('audit_status', '=', $con_arr['audit_status']);
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
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('f_table', $data)) {
            $info->f_table = array_get($data, 'f_table');
        }
        if (array_key_exists('f_id', $data)) {
            $info->f_id = array_get($data, 'f_id');
        }
        if (array_key_exists('g_id', $data)) {
            $info->g_id = array_get($data, 'g_id');
        }
        if (array_key_exists('content', $data)) {
            $info->content = array_get($data, 'content');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('read_status', $data)) {
            $info->read_status = array_get($data, 'read_status');
        }
        if (array_key_exists('show_flag', $data)) {
            $info->show_flag = array_get($data, 'show_flag');
        }
        if (array_key_exists('recomm_flag', $data)) {
            $info->recomm_flag = array_get($data, 'recomm_flag');
        }
        if (array_key_exists('audit_status', $data)) {
            $info->audit_status = array_get($data, 'audit_status');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        return $info;
    }


    /*
     * 是否评论
     *
     * By TerryQi
     *
     * 2018-09-20
     *
     */
    public static function is_comment($user_id, $f_table, $f_id)
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

}