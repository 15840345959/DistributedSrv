<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\YSB;

use App\Components\ArticleManager;
use App\Components\GuanZhuManager;
use App\Components\UserManager;
use App\Models\YSB\YSBUser;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class YSBUserManager
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
        $info = YSBUser::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0带用户信息 1带作品数 2带粉丝数 3带关注数
     */
    public static function getInfoByLevel($info, $level)
    {
        //0 带用户信息
        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        //1 带作品数
        if (strpos($level, '1') !== false) {
            //获取作品数
            $con_arr = array(
                'busi_name' => 'ysb',
                'user_id' => $info->user_id,
                'status' => '1'
            );
            $info->zp_num = ArticleManager::getListByCon($con_arr, false)->count();     //作品数
        }
        //2 带粉丝数
        if (strpos($level, '2') !== false) {
            //粉丝数
            $con_arr = array(
                'busi_name' => 'ysb',
                'gz_user_id' => $info->user_id
            );
            $info->fans_num = GuanZhuManager::getListByCon($con_arr, false)->count();
        }
        //3 带关注数
        if (strpos($level, '3') !== false) {
            //关注数
            $con_arr = array(
                'busi_name' => 'ysb',
                'fan_user_id' => $info->user_id
            );
            $info->gz_num = GuanZhuManager::getListByCon($con_arr, false)->count();
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
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('level', $data)) {
            $info->level = array_get($data, 'level');
        }
        if (array_key_exists('inf_value', $data)) {
            $info->inf_value = array_get($data, 'inf_value');
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
        $infos = new YSBUser();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
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
        $ysbUser = self::getListByCon($con_arr, false)->first();
        return $ysbUser;
    }


}