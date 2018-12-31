<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/20
 * Time: 11:15
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\OptInfo;

class OptInfoManager
{
    /*
     * 根据id获取操作值
     *
     * By mtt
     *
     * 2018-4-20
     */
    public static function getById($id)
    {
        $optInfo = OptInfo::where('id', $id)->first();
        return $optInfo;
    }

    /*
     * 根据条件获取信息
     *
     * By mtt
     *
     * 2018-4-20
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $optInfo = new OptInfo();
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $optInfo = $optInfo->where('value', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('f_table', $con_arr) && !Utils::isObjNull($con_arr['f_table'])) {
            $optInfo = $optInfo->where('f_table', '=', $con_arr['f_table']);
        }
        $optInfo = $optInfo->orderby('seq', 'desc')->orderby('id', 'desc');
        if ($is_paginate) {
            $optInfo = $optInfo->paginate(Utils::PAGE_SIZE);
        } else {
            $optInfo = $optInfo->get();
        }
        return $optInfo;
    }

    /*
     * 根据级别获取信息
     *
     * By mtt
     *
     * 2018-4-20
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->f_table_str = Utils::OPT_F_TABLE_VAL[$info->f_table];

        return $info;
    }

    /*
     * 设置操作值信息
     *
     * By mtt
     *
     * 2018-4-20
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('value', $data)) {
            $info->value = array_get($data, 'value');
        }
        if (array_key_exists('f_table', $data)) {
            $info->f_table = array_get($data, 'f_table');
        }
        return $info;
    }


}







