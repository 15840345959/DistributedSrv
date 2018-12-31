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
use App\Components\Mryh\MryhSendXCXTplMessageManager;
use App\Models\Mryh\MryhJoin;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhJoinManager
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
        $info = MryhJoin::where('id', '=', $id)->first();
        return $info;
    }


    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带活动信息 1：带用户信息  2:带作品信息 3:带订单信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->game_status_str = Utils::MRYH_JOIN_GAME_STATUS_VAL[$info->game_status];
        $info->jiesuan_status_str = Utils::MRYH_JOIN_JIESUAN_STATUS_VAL[$info->jiesuan_status];
        $info->clear_status_str = Utils::MRYH_JOIN_CLEAR_STATUS_VAL[$info->clear_status];

        //带今日是否已经上传的标识
        $info->today_already_upload_flag = MryhJoinArticleManager::isUploadAtDate($info->user_id, $info->id, DateTool::getToday());

        if (strpos($level, '0') !== false) {
            $game = MryhGameManager::getById($info->game_id);
            $game = MryhGameManager::getInfoByLevel($game, '');
            unset($game->intro_html);
            $info->game = $game;
        }

        if (strpos($level, '1') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        if (strpos($level, '2') !== false) {
            $con_arr = array(
                'join_id' => $info->id
            );
            $mryh_join_articles = MryhJoinArticleManager::getListByCon($con_arr, false);
            foreach ($mryh_join_articles as $mryh_join_article) {
                $mryh_join_article = MryhJoinArticleManager::getInfoByLevel($mryh_join_article, '3');
            }
            $info->articles = $mryh_join_articles;
        }

        if (strpos($level, '3') !== false) {
            if (!Utils::isObjNull($info->trade_no)) {
                $info->order = MryhJoinOrderManager::getByTradeNo($info->trade_no);
            }
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
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('trade_no', $data)) {
            $info->trade_no = array_get($data, 'trade_no');
        }
        if (array_key_exists('total_fee', $data)) {
            $info->total_fee = array_get($data, 'total_fee');
        }
        if (array_key_exists('user_coupon_id', $data)) {
            $info->user_coupon_id = array_get($data, 'user_coupon_id');
        }
        if (array_key_exists('join_time', $data)) {
            $info->join_time = array_get($data, 'join_time');
        }
        if (array_key_exists('join_status', $data)) {
            $info->join_status = array_get($data, 'join_status');
        }
        if (array_key_exists('join_day_num', $data)) {
            $info->join_day_num = array_get($data, 'join_day_num');
        }
        if (array_key_exists('work_num', $data)) {
            $info->work_num = array_get($data, 'work_num');
        }
        if (array_key_exists('jiesuan_status', $data)) {
            $info->jiesuan_status = array_get($data, 'jiesuan_status');
        }
        if (array_key_exists('jiesuan_time', $data)) {
            $info->jiesuan_time = array_get($data, 'jiesuan_time');
        }
        if (array_key_exists('jiesuan_price', $data)) {
            $info->jiesuan_price = array_get($data, 'jiesuan_price');
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
        $infos = new MryhJoin();
        //相关条件
        if (array_key_exists('game_id', $con_arr) && !Utils::isObjNull($con_arr['game_id'])) {
            $infos = $infos->where('game_id', '=', $con_arr['game_id']);
        }
        if (array_key_exists('game_status', $con_arr) && !Utils::isObjNull($con_arr['game_status'])) {
            $infos = $infos->where('game_status', '=', $con_arr['game_status']);
        }
        if (array_key_exists('jiesuan_status', $con_arr) && !Utils::isObjNull($con_arr['jiesuan_status'])) {
            $infos = $infos->where('jiesuan_status', '=', $con_arr['jiesuan_status']);
        }
        if (array_key_exists('clear_status', $con_arr) && !Utils::isObjNull($con_arr['clear_status'])) {
            $infos = $infos->where('clear_status', '=', $con_arr['clear_status']);
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('bigger_than_jiesuan_price', $con_arr) && !Utils::isObjNull($con_arr['bigger_than_jiesuan_price'])) {
            $infos = $infos->where('jiesuan_price', '>', $con_arr['bigger_than_jiesuan_price']);
        }
        if (array_key_exists('id_arr', $con_arr) && !Utils::isObjNull($con_arr['id_arr'])) {
            $infos = $infos->whereIn('id', $con_arr['id_arr']);
        }
        if (array_key_exists('user_id_arr', $con_arr) && !Utils::isObjNull($con_arr['user_id_arr'])) {
            $infos = $infos->whereIn('user_id', $con_arr['user_id_arr']);
        }
        if (array_key_exists('not_user_id_arr', $con_arr) && !Utils::isObjNull($con_arr['not_user_id_arr'])) {
            $infos = $infos->whereNotIn('user_id', $con_arr['not_user_id_arr']);
        }
        if (array_key_exists('date_at', $con_arr) && !Utils::isObjNull($con_arr['date_at'])) {
            $infos = $infos->where('created_at', '>=', $con_arr['date_at'])
                ->where('created_at', '<', DateTool::dateAdd('D', 1, $con_arr['date_at']));
        }

        $infos = $infos->orderByRaw("locate('0',game_status) desc")->orderby('id', 'desc');

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
     * 统计信息
     *
     * By TerryQi
     *
     * 2018-08-15
     *
     */
    public static function addStatistics($join_id, $item, $num)
    {
        $mryhJoin = MryhJoinManager::getById($join_id);
        switch ($item) {
            case "join_day_num":
                $mryhJoin->join_day_num = $mryhJoin->join_day_num + $num;
                break;
            case "work_num":
                $mryhJoin->work_num = $mryhJoin->work_num + $num;
                break;
        }
        $mryhJoin->save();
        return $mryhJoin;
    }


    /*
     * 判断某个join_id的某一天用户是否上传了作品
     *
     * By TerryQi
     *
     * 2018-11-25
     *
     */
    public static function uploadNumAtDate($join_id, $date_at)
    {
        $mryhJoin = self::getById($join_id);
        //如果不存在参与记录，则返回未上传
        if (!$mryhJoin) {
            return false;
        }
        $con_arr = array(
            'user_id' => $mryhJoin->user_id,
            'join_id' => $mryhJoin->id,
            'date_at' => $date_at
        );
        Utils::processLog(__METHOD__, '', " " . "每天一画活动参与是否成功计划 mryhJoinArticle con_arr:" . json_encode($con_arr));
        Utils::processLog(__METHOD__, '', " " . "每天一画活动参与是否成功计划 mryhJoinArticle yesterday:" . $con_arr['date_at']);
        Utils::processLog(__METHOD__, '', " " . "每天一画活动参与是否成功计划 mryhJoinArticle today:" . DateTool::dateAdd('D', 1, $con_arr['date_at']));
        $mryhJoinArticles_num = MryhJoinArticleManager::getListByCon($con_arr, false)->count();
        Utils::processLog(__METHOD__, '', " " . "每天一画活动参与是否成功计划 mryhJoinArticle exist_mryhJoinArticles_num:" . $mryhJoinArticles_num);

        return $mryhJoinArticles_num;
    }


    /*
     * 用户参与记录的计划任务，查看哪些用户参赛失败了
     *
     * By TerryQi
     *
     * 2018-08-20
     */
    public static function joinSchedule()
    {
        $con_arr = array(
            'game_status' => '0'
        );
        $mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        foreach ($mryhJoins as $mryhJoin) {
            //参与中用户是否昨日已经上传文章，如果已经上传
            Utils::processLog(__METHOD__, '', "每天一画活动参与是否成功计划 mryhJoin:" . json_encode($mryhJoin));
            $mryhJoinArticles_num = self::uploadNumAtDate($mryhJoin->id, DateTool::dateAdd('D', -1, DateTool::getToday()));
            Utils::processLog(__METHOD__, '', "每天一画活动参与是否成功计划 mryhJoinArticle exit_mryhJoinArticles_num:" . $mryhJoinArticles_num);
            //用户昨天没有上传作品，则设置为失败
            /*
             * 2018-12-18进行逻辑优化，即用户前一天没有上传作品 并且 不是24小时内参赛的，则将活动设置为失败
             *
             * By TerryQi
             *
             */
            if ($mryhJoinArticles_num == 0 && (DateTool::dateDiff('H', $mryhJoin->join_time, DateTool::getCurrentTime()) >= 24)) {
                $mryhJoin->game_status = '2';
                $mryhJoin->save();
                //失败后，增加活动的失败次数 2018-11-29日进行逻辑测试时发现
                MryhGameManager::addStatistics($mryhJoin->game_id, 'fail_num', 1);
                //失败后应该增加通知，此处预留///////////////////////////////
                //小程序发送失败模板
                MryhSendXCXTplMessageManager::sendJoinResultMessage($mryhJoin->id);
                /////////////////////////////////////////////////////////////
                //2018-12-17增加逻辑，即如果失败，则增加mryh_user_info中的join_notify_num，即参赛提醒消息数
                $mryhUser = MryhUserManager::getByUserId($mryhJoin->user_id);
                $mryhUser->join_notify_num = $mryhUser->join_notify_num + 1;        //参赛提醒消息数+1
                $mryhJoin->save();
            }
        }
    }


    /*
     * 根据参赛编号获取join_id，参赛编号样式为 MRYH-100041
     *
     * By TerryQi
     *
     * 2018-10-14
     */
    public static function getIdByCode($code)
    {
        $code_arr = explode('-', $code);
        //长度不为2，代表编码有错误，返回null
        if (count($code_arr) != 2) {
            return null;
        }
        //第一个字符串必须是MRYH
        if (strtoupper($code_arr[0]) != 'MRYH') {
            return null;
        }
        //第二个字符串必须是数字
        if (!is_numeric($code_arr[1])) {
            return null;
        }
        $join_id = intval($code_arr[1]) - 100000;
        if ($join_id <= 0) {
            return null;
        }
        return $join_id;
    }


    /*
     * 生成参赛证书
     *
     * By TerryQi
     *
     * 2018-10-14
     *
     * @入参为info_arr，其中需要包含 cert_no：证书编号 name:选手姓名 date：日期 game_name：大赛名称
     *
     * @return 为生成的证书path
     *
     */
    public static function generateCert($info_arr)
    {
        //合规校验参数
        if (!array_key_exists('name', $info_arr) || !array_key_exists('cert_no', $info_arr) || !array_key_exists('game_name', $info_arr) || !array_key_exists('date', $info_arr)) {
            return null;
        }
        //相关信息
        $name = $info_arr['name'];
        $game_name = $info_arr['game_name'];
        $cert_no = $info_arr['cert_no'];
        $date = $info_arr['date'];

        //生成证书
        //基础文件
        $cert_base_path = public_path('img/mryh/cert/mryh_cert_base.jpg');
        $cert_base_img = imagecreatefromjpeg($cert_base_path);

        //生成新图片
        $generate_cert_path = public_path('img/mryh/cert/' . Utils::generateTradeNo() . '.jpg');
        imagejpeg($cert_base_img, $generate_cert_path);
        $generate_cert_img = imagecreatefromjpeg($generate_cert_path);

        $fontfile = public_path('docs/css/fonts/msyh.ttf');

        // 姓名
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 32, 0, 220, 1010, $color, $fontfile, $name);

        // 大赛名称
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 32, 0, 700, 1120, $color, $fontfile, $game_name);

        // 证书编号
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 24, 0, 1000, 160, $color, $fontfile, "证书编号：" . $cert_no);

        // 发证日期
        $color = imagecolorallocatealpha($generate_cert_img, 33, 33, 33, 0);
        imagettftext($generate_cert_img, 22, 0, 1050, 1900, $color, $fontfile, $date);

        //微信公众号二维码
        $ewm_code_size = 250;
        $ewm_code_img = imagecreatefromjpeg(public_path('img/mryh/cert/isart_fwh_ewm.jpg'));
        $ewm_code_img = Utils::resizeImage($ewm_code_img, $ewm_code_size, $ewm_code_size);      //调整二维码的大小
        imagecopymerge($generate_cert_img, $ewm_code_img, 200, 1700, 0, 0, imagesx($ewm_code_img), imagesy($ewm_code_img), 100);

        //生成图片数据
        imagejpeg($generate_cert_img, $generate_cert_path);
        //销毁数据
        imagedestroy($generate_cert_img);

        return $generate_cert_path;

    }


    /*
     * 发送每日作品提醒
     *
     * By TerryQi
     *
     * 2018-11-25
     */
    public static function sendJoinNotifySchedule()
    {
        $con_arr = array(
            'game_status' => '0'
        );
        $mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        foreach ($mryhJoins as $mryhJoin) {
            //如果没有上传
            if (!MryhJoinArticleManager::isUploadAtDate($mryhJoin->user_id, $mryhJoin->id, DateTool::getToday())) {
                Utils::processLog(__METHOD__, '', "发送提醒" . json_encode($mryhJoin));
                MryhSendXCXTplMessageManager::sendJoinNotifyMessage($mryhJoin->id);
            }
        }
    }

}