<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Components\UserManager;
use App\Models\Vote\VoteComplain;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class VoteComplainManager
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
        $info = VoteComplain::where('id', '=', $id)->first();
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
        //投诉人信息
        if (strpos($level, '2') !== false) {
            if ($info->user_id) {
                $info->user = UserManager::getById($info->user_id);
            }
        }
        return $info;
    }


    /*
     * 设置信息，用于编辑
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('activity_id', $data)) {
            $info->activity_id = array_get($data, 'activity_id');
        }
        if (array_key_exists('vote_user_id', $data)) {
            $info->vote_user_id = array_get($data, 'vote_user_id');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('phonenum', $data)) {
            $info->phonenum = array_get($data, 'phonenum');
        }
        if (array_key_exists('content', $data)) {
            $info->content = array_get($data, 'content');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('vote_team', $data)) {
            $info->vote_team = array_get($data, 'vote_team');
        }
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
        }
        return $info;
    }

    /*
     * 获取列表
     *
     * By Amy
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new VoteComplain();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('phonenum', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
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