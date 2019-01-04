<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Yxhd;

use App\Models\Yxhd\YxhdActivity;
use App\Components\AdminManager;
use App\Components\Utils;
use Illuminate\Support\Facades\Cache;

class YxhdActivityManager
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
        $class = substr(explode('\\', __CLASS__)[count(explode('\\', __CLASS__)) - 1],0, -7);

        if (Cache::get("$class:$id")) {
            Utils::processLog(__METHOD__, '', '命中缓存');
            $info = Cache::get("$class:$id");
            return $info;
        }

        $info = YxhdActivity::where('id', $id)->first();
        Cache::add("$class:$id", $info, 60*24*7);

        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带奖品信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        //基本信息
        $info->type_str = Utils::YXHD_TYPE_VAL[$info->type];
        $info->status_str = Utils::COMMON_STATUS_VAL[$info->status];

        //录入人员
        $info->admin = AdminManager::getById($info->admin_id);

        if (strpos($level, '0') !== false) {
            $yxhdPrizes = YxhdPrizeManager::getListByCon(['activity_id' => $info->id], false);
            foreach ($yxhdPrizes as $yxhdPrize) {
                unset($yxhdPrize->intro_html);
            }
            $info->prizes = $yxhdPrizes;
        }
        return $info;
    }


    /*
     * 设置活动，用于编辑
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('code', $data)) {
            $info->code = array_get($data, 'code');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('intro_html', $data)) {
            $info->intro_html = array_get($data, 'intro_html');
        }
        if (array_key_exists('show_num', $data)) {
            $info->show_num = array_get($data, 'show_num');
        }
        if (array_key_exists('join_num', $data)) {
            $info->join_num = array_get($data, 'join_num');
        }
        if (array_key_exists('join_score', $data)) {
            $info->join_score = array_get($data, 'join_score');
        }
        if (array_key_exists('share_title', $data)) {
            $info->share_title = array_get($data, 'share_title');
        }
        if (array_key_exists('share_img', $data)) {
            $info->share_img = array_get($data, 'share_img');
        }
        if (array_key_exists('share_desc', $data)) {
            $info->share_desc = array_get($data, 'share_desc');
        }
        if (array_key_exists('share_url', $data)) {
            $info->share_url = array_get($data, 'share_url');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
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
        $infos = new YxhdActivity();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('code', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('id', $con_arr) && !Utils::isObjNull($con_arr['id'])) {
            $infos = $infos->where('id', '=', $con_arr['id']);
        }
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $infos = $infos->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('code', $con_arr) && !Utils::isObjNull($con_arr['code'])) {
            $infos = $infos->where('code', '=', $con_arr['code']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('admin_id', $con_arr) && !Utils::isObjNull($con_arr['admin_id'])) {
            $infos = $infos->where('admin_id', '=', $con_arr['admin_id']);
        }

        $infos = $infos->orderby('id', 'desc');
//        dd($infos->toSql());

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


    /*
     * 增加数据
     *
     * By TerryQi
     *
     * 2018-07-18
     *
     * 增加统计数据 item统计项目 join_num：参与人数 show_num：展示数
     */
    public static function addStatistics($activity_id, $item, $num)
    {
        $activity = self::getById($activity_id);
        switch ($item) {
            case "join_num":
                $activity->join_num = $activity->join_num + $num;
                break;
            case "show_num":
                $activity->show_num = $activity->show_num + $num;
                break;
        }
        $activity->save();
    }

}