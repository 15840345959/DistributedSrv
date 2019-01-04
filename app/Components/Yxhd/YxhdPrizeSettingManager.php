<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Yxhd;

use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\SMSManager;
use App\Components\Vote\VoteADManager;
use App\Models\Yxhd\YxhdPrizeSetting;
use Carbon\Carbon;
use function Couchbase\defaultDecoder;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;
use Illuminate\Support\Facades\URL;

class YxhdPrizeSettingManager
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
            $info = Cache::get("$class:$id");
            return $info;
        }

        $info = YxhdPrizeSetting::where('id', '=', $id)->first();
        Cache::put("$class:$id", $info, 60*24*7);

        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-12-10
     *
     * 0：带活动信息 1：带奖品信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        //基本信息
        $info->status_str = Utils::COMMON_STATUS_VAL[$info->status];

        //录入人员
        $info->admin = AdminManager::getById($info->admin_id);

        //带活动信息
        if (strpos($level, '0') !== false) {
            $info->activity = YxhdActivityManager::getById($info->activity_id);
        }

        //1带奖品信息
        if (strpos($level, '1') !== false) {
            $info->prize = YxhdPrizeManager::getById($info->prize_id);
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
        if (array_key_exists('activity_id', $data)) {
            $info->activity_id = array_get($data, 'activity_id');
        }
        if (array_key_exists('prize_id', $data)) {
            $info->prize_id = array_get($data, 'prize_id');
        }
        if (array_key_exists('rate', $data)) {
            $info->rate = array_get($data, 'rate');
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
        $infos = new YxhdPrizeSetting();
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