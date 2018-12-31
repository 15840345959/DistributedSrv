<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\DateTool;
use App\Components\GZH\WeChatManager;
use App\Models\Mryh\MryhCertSend;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhCertSendManager
{
    const BUSI_NAME = "isart";      //业务名称

    /*
     * 根据id获取信息
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function getById($id)
    {
        $info = MryhCertSend::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带参赛信息，参赛信息中带 活动名称和用户信息
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->status_str = Utils::COMMON_STATUS_VAL[$info->status];

        if (strpos($level, '0') !== false) {
            $mryhJoin = MryhJoinManager::getById($info->join_id);
            $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '01');

            $info->join = $mryhJoin;
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
        if (array_key_exists('join_id', $data)) {
            $info->join_id = array_get($data, 'join_id');
        }
        if (array_key_exists('to_openid', $data)) {
            $info->to_openid = array_get($data, 'to_openid');
        }
        if (array_key_exists('cert_path', $data)) {
            $info->cert_path = array_get($data, 'cert_path');
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
        $infos = new MryhCertSend();
        //相关条件
        if (array_key_exists('join_id', $con_arr) && !Utils::isObjNull($con_arr['join_id'])) {
            $infos = $infos->where('join_id', '=', $con_arr['join_id']);
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
     * 发送证书计划任务
     *
     * By TerryQi
     *
     * 2018-09-12
     *
     */
    public static function mryhCertSendSchedule()
    {
        $mryh_cert_sends = self::getListByCon(['status' => '0'], false);
        foreach ($mryh_cert_sends as $mryh_cert_send) {
            //如果还没有发送，则优先配置发送，主要原因为生成证书过程较长，避免计划任务重复导致的证书多次派发
            $mryh_cert_send = self::getById($mryh_cert_send->id);
            if ($mryh_cert_send->status != '1') {
                //已经开始处理
                $mryh_cert_send->status = '1';
                $mryh_cert_send->save();
                //获取配置信息
                $mryh_join = MryhJoinManager::getById($mryh_cert_send->join_id);
                $mryh_join = MryhJoinManager::getInfoByLevel($mryh_join, '01');
                $cert_no = "MRYH-" . strval(100000 + $mryh_join->id);       //证书编号
                $name = $mryh_join->user->nick_name;
                $game_name = $mryh_join->game->name;
                $info_arr = [
                    'name' => $name,
                    'game_name' => $game_name,
                    'cert_no' => $cert_no,
                    'date' => DateTool::getYMDChi($mryh_join->game->game_end_time)
                ];
                //生成证书
                $cert_path = MryhJoinManager::generateCert($info_arr);
                $mryh_cert_send->cert_path = $cert_path;
                $mryh_cert_send->save();
                //发送个性化消息

                //相关配置
                $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
                $temp_media_id = WeChatManager::createMediaId($cert_path, $app);
                $image = WeChatManager::setImageMessage($temp_media_id);
                $app->customer_service->message($image)
                    ->to($mryh_cert_send->to_openid)
                    ->send();
                Utils::processLog(__METHOD__, '', " " . "证书发送完毕");
            }
        }
    }


}