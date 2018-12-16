<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/6/5
 * Time: 9:56
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\TWStep;

class TWStepManager
{
    /*
     * 根据图文id获取图文信息
     *
     * By mtt
     *
     * 2018-6-5
     */
    public static function getById($id)
    {
        $tw = TWStep::where('id', $id)->first();
        return $tw;
    }

    /*
     * 设置条件
     *
     * By TerryQi
     *
     * 2018-06-14
     */
    public static function setCon($con_arr)
    {
        $tw = new TWStep();
        if (array_key_exists('f_id', $con_arr) && !Utils::isObjNull($con_arr['f_id'])) {
            $tw = $tw->where('f_id', '=', $con_arr['f_id']);
        }
        if (array_key_exists('f_table', $con_arr) && !Utils::isObjNull($con_arr['f_table'])) {
            $tw = $tw->where('f_table', '=', $con_arr['f_table']);
        }
        return $tw;
    }

    /*
     * 根据条件获取图文信息
     *
     * By mtt
     *
     * 2018-4-26
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $tw = self::setCon($con_arr);
        $tw = $tw->orderby('seq', 'asc')->orderby('id', 'asc');
        if ($is_paginate) {
            $tw = $tw->paginate(Utils::PAGE_SIZE);
        } else {
            $tw = $tw->get();
        }
        return $tw;
    }

    /*
     * 设置图文信息
     *
     * By mtt
     *
     * 2018-4-26
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('f_id', $data)) {
            $info->f_id = array_get($data, 'f_id');
        }
        if (array_key_exists('f_table', $data)) {
            $info->f_table = array_get($data, 'f_table');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('video', $data)) {
            $info->video = array_get($data, 'video');
        }
        if (array_key_exists('text', $data)) {
            $info->text = array_get($data, 'text');
        }
        return $info;
    }

}