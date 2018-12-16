<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\Address;

class AddressManager
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
        $address = Address::where('id', $id)->first();
        return $address;
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

        $addresses = new Address();
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $addresses = $addresses->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $addresses = $addresses->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $addresses = $addresses->where('status', '=', $con_arr['status']);
        }
        $addresses = $addresses->orderby('id', 'desc');
        if ($is_paginate) {
            $addresses = $addresses->paginate(Utils::PAGE_SIZE);
        } else {
            $addresses = $addresses->get();
        }
        return $addresses;
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
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('rec_name', $data)) {
            $info->rec_name = array_get($data, 'rec_name');
        }
        if (array_key_exists('rec_tel', $data)) {
            $info->rec_tel = array_get($data, 'rec_tel');
        }
        if (array_key_exists('province', $data)) {
            $info->province = array_get($data, 'province');
        }
        if (array_key_exists('city', $data)) {
            $info->city = array_get($data, 'city');
        }
        if (array_key_exists('detail', $data)) {
            $info->detail = array_get($data, 'detail');
        }
        if (array_key_exists('zip_code', $data)) {
            $info->zip_code = array_get($data, 'zip_code');
        }
        if (array_key_exists('default_flag', $data)) {
            $info->default_flag = array_get($data, 'default_flag');
        }
        return $info;
    }

}