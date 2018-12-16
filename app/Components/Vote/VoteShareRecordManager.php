<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Components\UserManager;
use App\Models\Vote\VoteShareRecord;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class VoteShareRecordManager
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
        $info = VoteShareRecord::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带用户信息 1：带选手信息
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->type_str = Utils::VOTE_SHARE_TYPE_VAL[$info->type];

        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        if (strpos($level, '1') !== false) {
            $info->vote_user = VoteUserManager::getById($info->vote_user_id);
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
        if (array_key_exists('vote_user_id', $data)) {
            $info->vote_user_id = array_get($data, 'vote_user_id');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
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
        $infos = new VoteShareRecord();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('vote_user_id', $con_arr) && !Utils::isObjNull($con_arr['vote_user_id'])) {
            $infos = $infos->where('vote_user_id', '=', $con_arr['vote_user_id']);
        }
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $infos = $infos->where('type', '=', $con_arr['type']);
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