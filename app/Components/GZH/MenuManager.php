<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 17:25
 */

namespace App\Components\GZH;


use App\Models\GZH\Menu;
use App\Components\AdminManager;
use App\Components\Utils;

class MenuManager
{
    /*
     * 根据id获取信息
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function getById($id)
    {
        $menu = Menu::where('id', '=', $id)->first();
        return $menu;
    }

    /*
     * 获取菜单列表
     *
     * By Amy
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new Menu();
        //相关条件
        if (array_key_exists('f_id', $con_arr) && !Utils::isObjNull($con_arr['f_id'])) {
            $infos = $infos->where('f_id', '=', $con_arr['f_id']);
        }
        if (array_key_exists('level', $con_arr) && !Utils::isObjNull($con_arr['level'])) {
            $infos = $infos->where('level', '=', $con_arr['level']);
        }
        $infos = $infos->orderby('seq', 'desc');
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

    /*
     * 根据级别获取信息
     *
     *
     * By TerryQi
     *
     * 2018-07-08
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->f_menu = self::getById($info->f_id);
        $info->type_str = Utils::MENU_TYPE_VAL[$info->type];
        $info->admin = AdminManager::getById($info->admin_id);

        return $info;
    }


    /*
     * 设置菜单信息，用于编辑
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('level', $data)) {
            $info->level = array_get($data, 'level');
        }
        if (array_key_exists('f_id', $data)) {
            $info->f_id = array_get($data, 'f_id');
        }
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('url', $data)) {
            $info->url = array_get($data, 'url');
        }
        if (array_key_exists('media_id', $data)) {
            $info->media_id = array_get($data, 'media_id');
        }
        if (array_key_exists('key', $data)) {
            $info->key = array_get($data, 'key');
        }
        if (array_key_exists('appid', $data)) {
            $info->appid = array_get($data, 'appid');
        }
        if (array_key_exists('pagepath', $data)) {
            $info->pagepath = array_get($data, 'pagepath');
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