<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Components\UserManager;
use App\Models\Vote\VoteRecord;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class VoteRecordManager
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
        $info = VoteRecord::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带活动信息 1：带选手信息 2：带投诉人信息
     */
    public static function getInfoByLevel($info, $level)
    {

        $info->vote_type_str = Utils::VOTE_TYPE_VAL[$info->vote_type];

        //活动信息
        if (strpos($level, '0') !== false) {
            if ($info->activity_id) {
                $activity = VoteActivityManager::getById($info->activity_id);
                $activity = VoteActivityManager::getInfoByLevel($activity, '2');
                $info->activity = $activity;
            }
        }
        //选手信息
        if (strpos($level, '1') !== false) {
            if ($info->vote_user_id) {
                $info->vote_user = VoteUserManager::getById($info->vote_user_id);
            }
        }
        //投票人信息
        if (strpos($level, '2') !== false) {
            if ($info->user_id) {
                $info->user = UserManager::getById($info->user_id);
            }
        }

        return $info;
    }


    /*
     * 设置自动回复信息，用于编辑
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('activity_id', $data)) {
            $info->activity_id = array_get($data, 'activity_id');
        }
        if (array_key_exists('vote_user_id', $data)) {
            $info->vote_user_id = array_get($data, 'vote_user_id');
        }
        if (array_key_exists('vote_num', $data)) {
            $info->vote_num = array_get($data, 'vote_num');
        }
        if (array_key_exists('vote_type', $data)) {
            $info->vote_type = array_get($data, 'vote_type');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        return $info;
    }

    /*
     * 获取自动回复列表
     *
     * By Amy
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new VoteRecord();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('vote_user_id', $con_arr) && !Utils::isObjNull($con_arr['vote_user_id'])) {
            $infos = $infos->where('vote_user_id', '=', $con_arr['vote_user_id']);
        }
        if (array_key_exists('activity_id', $con_arr) && !Utils::isObjNull($con_arr['activity_id'])) {
            $infos = $infos->where('activity_id', '=', $con_arr['activity_id']);
        }
        //定位到哪天
        if (array_key_exists('at_date', $con_arr) && !Utils::isObjNull($con_arr['at_date'])) {
            $infos = $infos->whereDate('created_at', $con_arr['at_date']);
        }
        $infos = $infos->orderby('id', 'desc');

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


}