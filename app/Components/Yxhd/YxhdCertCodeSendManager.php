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
use App\Models\Yxhd\YxhdCertCodeSend;
use Carbon\Carbon;
use function Couchbase\defaultDecoder;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;
use Illuminate\Support\Facades\URL;

class YxhdCertCodeSendManager
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
        $info = YxhdCertCodeSend::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带用户信息 1：带活动信息 2：带礼品信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        //基本信息
        $info->used_status_str = Utils::COMMON_USED_STATUS_VAL[$info->used_status];

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
        if (array_key_exists('winning_record_id', $data)) {
            $info->winning_record_id = array_get($data, 'winning_record_id');
        }
        if (array_key_exists('certCode', $data)) {
            $info->certCode = array_get($data, 'certCode');
        }
        if (array_key_exists('used_status', $data)) {
            $info->used_status = array_get($data, 'used_status');
        }
        if (array_key_exists('used_at', $data)) {
            $info->used_at = array_get($data, 'used_at');
        }
        if (array_key_exists('expiry_date', $data)) {
            $info->expiry_date = array_get($data, 'expiry_date');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
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
        $infos = new YxhdCertCodeSend();
        //相关条件
        if (array_key_exists('winning_record_id', $con_arr) && !Utils::isObjNull($con_arr['winning_record_id'])) {
            $infos = $infos->where('winning_record_id', '=', $con_arr['winning_record_id']);
        }
        if (array_key_exists('certCode', $con_arr) && !Utils::isObjNull($con_arr['certCode'])) {
            $infos = $infos->where('certCode', '=', $con_arr['certCode']);
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