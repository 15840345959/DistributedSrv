<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\DateTool;
use App\Components\UserManager;
use App\Models\Mryh\MryhComputePrize;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhComputePrizeManager
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
        $info = MryhComputePrize::where('id', '=', $id)->first();
        unset($info->password);
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     */
    public static function getInfoByLevel($info, $level)
    {
        //基本信息
        $info->compute_status_str = Utils::MRYH_COMPUTE_PRIZE_COMPUTE_STATUS[$info->compute_status];

        //活动信息
        if (strpos($level, '0') !== false) {
            $info->game = MryhGameManager::getById($info->game_id);
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
        if (array_key_exists('game_id', $data)) {
            $info->game_id = array_get($data, 'game_id');
        }
        if (array_key_exists('success_num', $data)) {
            $info->success_num = array_get($data, 'success_num');
        }
        if (array_key_exists('fail_num', $data)) {
            $info->fail_num = array_get($data, 'fail_num');
        }
        if (array_key_exists('ave_prize', $data)) {
            $info->ave_prize = array_get($data, 'ave_prize');
        }
        if (array_key_exists('compute_num', $data)) {
            $info->compute_num = array_get($data, 'compute_num');
        }
        if (array_key_exists('compute_status', $data)) {
            $info->compute_status = array_get($data, 'compute_status');
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
        $infos = new MryhComputePrize();
        //相关条件
        if (array_key_exists('game_id', $con_arr) && !Utils::isObjNull($con_arr['game_id'])) {
            $infos = $infos->where('game_id', '=', $con_arr['game_id']);
        }
        if (array_key_exists('compute_status', $con_arr) && !Utils::isObjNull($con_arr['compute_status'])) {
            $infos = $infos->where('compute_status', '=', $con_arr['compute_status']);
        }
        if (array_key_exists('created_start_at', $con_arr) && !Utils::isObjNull($con_arr['created_start_at'])) {
            $infos = $infos->where('created_at', '>=', $con_arr['created_start_at']);
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
     * 统计信息
     *
     * By TerryQi
     *
     * 2018-08-15
     *
     */
    public static function addStatistics($mryhComputePrize_id, $item, $num)
    {
        $mryhComputePrize = self::getById($mryhComputePrize_id);
        switch ($item) {
            case "compute_num":
                $mryhComputePrize->compute_num = $mryhComputePrize->compute_num + $num;
                break;
        }
        $mryhComputePrize->save();
        return $mryhComputePrize;
    }

}