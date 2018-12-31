<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\Vertify;

class VertifyManager
{

    /*
     * 根据id获取信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getById($id)
    {
        $vertify = Vertify::where('id', $id)->first();
        return $vertify;
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
        $vertifies = new Vertify();
        if (array_key_exists('phonenum', $con_arr) && !Utils::isObjNull($con_arr['phonenum'])) {
            $vertifies = $vertifies->where('phonenum', '=', $con_arr['phonenum']);
        }
        if (array_key_exists('email', $con_arr) && !Utils::isObjNull($con_arr['email'])) {
            $vertifies = $vertifies->where('email', '=', $con_arr['email']);
        }
        if (array_key_exists('code', $con_arr) && !Utils::isObjNull($con_arr['code'])) {
            $vertifies = $vertifies->where('code', '=', $con_arr['code']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $vertifies = $vertifies->where('status', '=', $con_arr['status']);
        }
        $vertifies = $vertifies->orderby('id', 'desc');
        if ($is_paginate) {
            $vertifies = $vertifies->paginate(Utils::PAGE_SIZE);
        } else {
            $vertifies = $vertifies->get();
        }
        return $vertifies;
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
        if (array_key_exists('phonenum', $data)) {
            $info->phonenum = array_get($data, 'phonenum');
        }
        if (array_key_exists('email', $data)) {
            $info->email = array_get($data, 'email');
        }
        if (array_key_exists('code', $data)) {
            $info->code = array_get($data, 'code');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        return $info;
    }

}