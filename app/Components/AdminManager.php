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
use Illuminate\Support\Facades\Cache;
use Qiniu\Auth;

class AdminManager
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
        $class = substr(explode('\\', __CLASS__)[count(explode('\\', __CLASS__)) - 1],0, -7);

        if (Cache::get("$class:$id")) {
            $info = Cache::get("$class:$id");
            return $info;
        }

        $info = Admin::where('id', '=', $id)->first();
        unset($info->password);
        Cache::put("$class:$id", $info, 60*24*7);

        return $info;
    }

    /*
     * 脱敏信息
     *
     * By TerryQi
     *
     * 2018-11-15
     *
     * $level：脱敏级别
     */
    public static function weakInfo($info, $level)
    {
        unset($info->password);
        unset($info->token);
        unset($info->phonenum);
        unset($info->email);
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
        $info->role_str = Utils::admin_role_val[$info->role];
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
        $infos = new Admin();
        //相关条件
        if (array_key_exists('phonenum', $con_arr) && !Utils::isObjNull($con_arr['phonenum'])) {
            $infos = $infos->where('phonenum', '=', $con_arr['phonenum']);
        }
        if (array_key_exists('password', $con_arr) && !Utils::isObjNull($con_arr['password'])) {
            $infos = $infos->where('password', '=', $con_arr['password']);
        }
        if (array_key_exists('role', $con_arr) && !Utils::isObjNull($con_arr['role'])) {
            $infos = $infos->where('role', '=', $con_arr['role']);
        }
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $keyword = $con_arr['search_word'];
            $infos = $infos->where(function ($query) use ($keyword) {
                $query->where('phonenum', 'like', "%{$keyword}%")
                    ->orwhere('name', 'like', "%{$keyword}%");
            });
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
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('avatar', $data)) {
            $info->avatar = array_get($data, 'avatar');
        }
        if (array_key_exists('phonenum', $data)) {
            $info->phonenum = array_get($data, 'phonenum');
        }
        if (array_key_exists('password', $data)) {
            $info->password = array_get($data, 'password');
        }
        if (array_key_exists('role', $data)) {
            $info->role = array_get($data, 'role');
        }
        return $info;
    }
}