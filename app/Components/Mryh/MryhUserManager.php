<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\UserManager;
use App\Models\Mryh\MryhUser;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhUserManager
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
        $info = MryhUser::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        $user = UserManager::getById($info->user_id);
        $info->user = $user;

        //2018-12-07 由TerryQi补充，此处的成功数、失败数从数据库中获得
        /*
         * 此处暂行，后续可以调整，需要逻辑统一梳理
         */
        $info->success_num = MryhJoinManager::getListByCon(['user_id' => $user->id, 'game_status' => '1'], false)->count();
        $info->fail_num = MryhJoinManager::getListByCon(['user_id' => $user->id, 'game_status' => '2'], false)->count();
        $info->join_num = MryhJoinManager::getListByCon(['user_id' => $user->id], false)->count();

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
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('join_num', $data)) {
            $info->join_num = array_get($data, 'join_num');
        }
        if (array_key_exists('success_num', $data)) {
            $info->success_num = array_get($data, 'success_num');
        }
        if (array_key_exists('fail_num', $data)) {
            $info->fail_num = array_get($data, 'fail_num');
        }
        if (array_key_exists('join_day_num', $data)) {
            $info->join_day_num = array_get($data, 'join_day_num');
        }
        if (array_key_exists('work_num', $data)) {
            $info->work_num = array_get($data, 'work_num');
        }
        if (array_key_exists('level', $data)) {
            $info->level = array_get($data, 'level');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
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
        $infos = new MryhUser();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }

        $infos = $infos->orderby('id', 'desc');

        if ($is_paginate) {
            $page_size = Utils::PAGE_SIZE;
            //如果con_arr中有page_size信息
            if (array_key_exists('page_size', $con_arr) && !Utils::isObjNull($con_arr['page_size'])) {
                $page_size = $con_arr['page_size'];
            }
            $infos = $infos->paginate($page_size);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

    /*
     * 根据用户id获取用户信息
     *
     * By TerryQi
     *
     * 2018-08-14
     */
    public static function getByUserId($user_id)
    {
        $con_arr = array(
            'user_id' => $user_id
        );
        $mryhUser = self::getListByCon($con_arr, false)->first();
        return $mryhUser;
    }


}