<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Components\DateTool;
use App\Components\GZH\WeChatManager;
use App\Models\Vote\VoteCertSend;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class VoteCertSendManager
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
        $info = VoteCertSend::where('id', '=', $id)->first();
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
        $info->status_str = Utils::COMMON_STATUS_VAL[$info->status];

        $info->vote_user = VoteUserManager::getById($info->vote_user_id);

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
        if (array_key_exists('vote_user_id', $data)) {
            $info->vote_user_id = array_get($data, 'vote_user_id');
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
        $infos = new VoteCertSend();
        //相关条件
        if (array_key_exists('vote_user_id', $con_arr) && !Utils::isObjNull($con_arr['vote_user_id'])) {
            $infos = $infos->where('vote_user_id', '=', $con_arr['vote_user_id']);
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
    public static function voteCertSendSchedule()
    {
        $vote_cert_sends = self::getListByCon(['status' => '0'], false);
        foreach ($vote_cert_sends as $vote_cert_send) {
            //如果还没有发送，则优先配置发送，主要原因为生成证书过程较长，避免计划任务重复导致的证书多次派发
            $vote_cert_send = self::getById($vote_cert_send->id);
            if ($vote_cert_send->status != '1') {
                //已经开始处理
                $vote_cert_send->status = '1';
                $vote_cert_send->save();
                $vote_user = VoteUserManager::getById($vote_cert_send->vote_user_id);
                $vote_activity = VoteActivityManager::getById($vote_user->activity_id);
                //配置证书信息
                $cert_no = $vote_activity->code . '-' . $vote_user->code;        //证书编号
                Utils::processLog(__METHOD__, '', " " . "证书编号 cert_no:" . json_encode($cert_no));
                $vote_user_name = VoteUserManager::getVoteUserName($vote_user->name);
                $prize = VoteUserManager::getPrize($vote_user->id);
                $info_arr = [
                    'name' => $vote_user_name,
                    'prize' => $prize,
                    'cert_no' => $cert_no,
                    'date' => DateTool::getYMDChi($vote_activity->vote_end_time)
                ];
                //生成证书
                $cert_path = VoteUserManager::generateCert($info_arr);
                $vote_cert_send->cert_path = $cert_path;
                $vote_cert_send->save();
                //相关配置
                $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[self::BUSI_NAME]);
                $temp_media_id = WeChatManager::createMediaId($cert_path, $app);
                $image = WeChatManager::setImageMessage($temp_media_id);
                $app->customer_service->message($image)
                    ->to($vote_cert_send->to_openid)
                    ->send();
                Utils::processLog(__METHOD__, '', " " . "证书发送完毕");
            }
        }
    }


}