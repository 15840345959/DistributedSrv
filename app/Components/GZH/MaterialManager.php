<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2018-3-19
 * Time: 10:30
 */

namespace App\Components\GZH;

use App\Components\AdminManager;
use App\Components\Utils;
use App\Models\GZH\Admin;
use App\Models\GZH\Material;
use Qiniu\Auth;

class MaterialManager
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
        $info = Material::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-07
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        if (isset($info->busi_name)) {
            $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];
        }
        if (isset($info->type)) {
            $info->type_str = Utils::MATERIAL_TYPE_VAL[$info->type];
        }
        $info->admin = AdminManager::getById($info->admin_id);
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
        $infos = new Material();
        //相关条件
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $infos = $infos->where('type', '=', $con_arr['type']);
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
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('media_id', $data)) {
            $info->media_id = array_get($data, 'media_id');
        }
        if (array_key_exists('url', $data)) {
            $info->url = array_get($data, 'url');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        if (array_key_exists('title', $data)) {
            $info->title = array_get($data, 'title');
        }
        if (array_key_exists('description', $data)) {
            $info->description = array_get($data, 'description');
        }
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
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