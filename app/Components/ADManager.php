<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\AD;

class ADManager
{

    /*
     * 根据id获取轮播图信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getById($id)
    {
        $ad = AD::where('id', $id)->first();
        return $ad;
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

        $ads = new AD();
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $ads = $ads->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('mode', $con_arr) && !Utils::isObjNull($con_arr['mode'])) {
            $ads = $ads->where('mode', '=', $con_arr['mode']);
        }
        if (array_key_exists('ids_arr', $con_arr) && !Utils::isObjNull($con_arr['ids_arr'])) {
            $ads = $ads->wherein('id', $con_arr['ids_arr']);
        }
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $ads = $ads->where('title', 'like', '%' . $con_arr['search_word'] . '%');
        }
        $ads = $ads->orderby('seq', 'desc')->orderby('id', 'desc');
        if ($is_paginate) {
            $ads = $ads->paginate(Utils::PAGE_SIZE);
        } else {
            $ads = $ads->get();
        }
        return $ads;
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
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('title', $data)) {
            $info->title = array_get($data, 'title');
        }
        if (array_key_exists('position', $data)) {
            $info->position = array_get($data, 'position');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('link', $data)) {
            $info->link = array_get($data, 'link');
        }
        return $info;
    }

}