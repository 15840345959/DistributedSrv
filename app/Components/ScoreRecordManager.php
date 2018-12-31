<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;

use App\Components\Utils;
use App\Models\ScoreRecord;

class ScoreRecordManager
{

    /*
     * 根据id信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getById($id)
    {
        $info = ScoreRecord::where('id', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-08-14
     *
     * 0：带用户信息
     */
    public static function getInfoByLevel($info, $level)
    {
        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->gz_user_id);
        }

        return $info;
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
        $infos = new ScoreRecord();
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', $con_arr['user_id']);
        }
        $infos = $infos->orderby('id', 'desc');
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
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
        if (array_key_exists('score', $data)) {
            $info->score = array_get($data, 'score');
        }
        if (array_key_exists('opt', $data)) {
            $info->opt = array_get($data, 'opt');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('f_table', $data)) {
            $info->f_table = array_get($data, 'f_table');
        }
        if (array_key_exists('f_id', $data)) {
            $info->f_id = array_get($data, 'f_id');
        }
        return $info;
    }

    /*
     * 设置用户积分变更
     *
     * By TerryQi
     *
     * 2018-12-12
     *
     * data为数组信息，其中必须有user_id、score、opt 0代表消减 1代表增加，形式为
     *
     * [
     *  'user_id'=>'11',            //用户id必填
     *  'score'=>25,            //积分变动值 25
     *  'opt'=>'1',                 //变更动作 0：消减 1：增加
     *  'remark'=>'XXX活动抽奖'     //积分变动说明
     * ]
     *
     */
    public static function change($data)
    {
        //如果用户id为空
        if (!array_key_exists('user_id', $data) || Utils::isObjNull($data['user_id'])) {
            return null;
        }
        //如果积分值为空
        if (!array_key_exists('score', $data) || Utils::isObjNull($data['score'])) {
            return null;
        }
        //如果opt为空
        if (!array_key_exists('opt', $data) || Utils::isObjNull($data['opt'])) {
            if ($data['opt'] != 1 && $data['opt'] != 0) {
                return null;
            }
        }
        $user = UserManager::getByIdWithToken($data['user_id']);        //此处一定注意需要带token，否则会导致用户丢失的问题
        //扣减积分///////////////////////////////////////////////////////////
        //此处在并发量大的情况下可能存在逻辑错误
        if ($data['opt'] == '0') {
            $user->score = $user->score - $data['score'];
        }
        //增加积分
        if ($data['opt'] == '1') {
            $user->score = $user->score + $data['score'];
        }
        $user->save();
        ////////////////////////////////////////////////////////////////////
        //进行积分扣减记录的保存
        $scoreRecord = new ScoreRecord();
        self::setInfo($scoreRecord, $data);
        $scoreRecord->save();

        return $scoreRecord;
    }

}