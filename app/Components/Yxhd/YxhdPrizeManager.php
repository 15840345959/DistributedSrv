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
use App\Models\Yxhd\YxhdPrize;
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

class YxhdPrizeManager
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
//            Utils::processLog(__METHOD__, '', '命中缓存');
            $info = Cache::get("$class:$id");
            return $info;
        }

        $info = YxhdPrize::where('id', '=', $id)->first();
        Cache::put("$class:$id", $info, 60*24*7);

        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        //基本信息
        $info->status_str = Utils::COMMON_STATUS_VAL[$info->status];
        $info->type_str = Utils::YXHD_PRIZE_TYPE_VAL[$info->type];

        //录入人员
        $info->admin = AdminManager::getById($info->admin_id);

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
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('intro_html', $data)) {
            $info->intro_html = array_get($data, 'intro_html');
        }
        if (array_key_exists('total_num', $data)) {
            $info->total_num = array_get($data, 'total_num');
        }
        if (array_key_exists('send_num', $data)) {
            $info->send_num = array_get($data, 'send_num');
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
        $infos = new YxhdPrize();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('id', $con_arr) && !Utils::isObjNull($con_arr['id'])) {
            $infos = $infos->where('id', '=', $con_arr['id']);
        }
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $infos = $infos->where('type', '=', $con_arr['type']);
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

}