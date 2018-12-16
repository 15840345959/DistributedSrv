<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/20
 * Time: 11:15
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\OptRecord;

class OptRecordManager
{
    public static function getById($id)
    {
        $optRecord = OptRecord::where('id', $id)->first();
        return $optRecord;
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
        $optRecords = new OptRecord();
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $optRecords = $optRecords->where('value', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $optRecords = $optRecords->where('type', '=', $con_arr['type']);
        }
        if (array_key_exists('f_table', $con_arr) && !Utils::isObjNull($con_arr['f_table'])) {
            $optRecords = $optRecords->where('f_table', '=', $con_arr['f_table']);
        }
        if (array_key_exists('f_id', $con_arr) && !Utils::isObjNull($con_arr['f_id'])) {
            $optRecords = $optRecords->where('f_id', '=', $con_arr['f_id']);
        }
        $optRecords = $optRecords->orderby('id', 'desc');
        if ($is_paginate) {
            $optRecords = $optRecords->paginate(Utils::PAGE_SIZE);
        } else {
            $optRecords = $optRecords->get();
        }
        //补充操作记录信息
        foreach ($optRecords as $optRecord) {
            $optRecord = OptRecordManager::getInfoByLevel($optRecord, '0');
        }
        return $optRecords;
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
        if (strpos($level, '0') !== false) {
            $info->optInfo = OptInfoManager::getById($info->opt_id);
            $info->admin = AdminManager::getById($info->admin_id);
        }
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
        if (array_key_exists('f_table', $data)) {
            $info->f_table = array_get($data, 'f_table');
        }
        if (array_key_exists('f_id', $data)) {
            $info->f_id = array_get($data, 'f_id');
        }
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
        }
        if (array_key_exists('attach', $data)) {
            $info->attach = array_get($data, 'attach');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        if (array_key_exists('opt_id', $data)) {
            $info->opt_id = array_get($data, 'opt_id');
        }
        return $info;
    }

}







