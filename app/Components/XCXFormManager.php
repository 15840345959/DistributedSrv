<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\XCXForm;

class XCXFormManager
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
        $xcxForm = XCXForm::where('id', $id)->first();
        return $xcxForm;
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

        $xcxForms = new XCXForm();
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $xcxForms = $xcxForms->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $xcxForms = $xcxForms->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('f_table', $con_arr) && !Utils::isObjNull($con_arr['f_table'])) {
            $xcxForms = $xcxForms->where('f_table', '=', $con_arr['f_table']);
        }
        if (array_key_exists('used_flag', $con_arr) && !Utils::isObjNull($con_arr['used_flag'])) {
            $xcxForms = $xcxForms->where('used_flag', '=', $con_arr['used_flag']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $xcxForms = $xcxForms->where('status', '=', $con_arr['status']);
        }


        $xcxForms = $xcxForms->orderby('id', 'desc');
        if ($is_paginate) {
            $xcxForms = $xcxForms->paginate(Utils::PAGE_SIZE);
        } else {
            $xcxForms = $xcxForms->get();
        }
        return $xcxForms;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-12-30
     *
     * 0带用户信息
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->used_flag_str = Utils::XCX_FORM_USED_FLAG_VAL[$info->used_flag];
        $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];

        //0带用户信息
        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }

        return $info;
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
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('form_id', $data)) {
            $info->form_id = array_get($data, 'form_id');
        }
        if (array_key_exists('f_table', $data)) {
            $info->f_table = array_get($data, 'f_table');
        }
        if (array_key_exists('f_id', $data)) {
            $info->f_id = array_get($data, 'f_id');
        }
        if (array_key_exists('total_num', $data)) {
            $info->total_num = array_get($data, 'total_num');
        }
        if (array_key_exists('used_num', $data)) {
            $info->used_num = array_get($data, 'used_num');
        }
        if (array_key_exists('used_flag', $data)) {
            $info->used_flag = array_get($data, 'used_flag');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        return $info;
    }

}