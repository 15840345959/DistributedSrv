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
use App\Components\UserManager;
use App\Components\Vote\VoteADManager;
use App\Models\Yxhd\YxhdOrder;
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

class YxhdOrderManager
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
        $info = YxhdOrder::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带用户信息 1：带活动信息 2：带奖品信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        //基本信息
        $info->status_str = Utils::COMMON_STATUS_VAL[$info->status];
        $info->pay_status_str = Utils::COMMON_PAY_STATUS_VAL[$info->pay_status];
        $info->winning_status_str = Utils::YXHD_ORDER_WINNING_STATUS_VAL[$info->winning_status];

        //保留订单号后6位，用于前端展示中奖码
        $info->trade_no_str = substr($info->trade_no, -6);
        //日期为保留日期即可
        $info->created_at_str = DateTool::getYMD($info->created_at);

        //带抽奖用户id
        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        //带活动信息
        if (strpos($level, '1') !== false) {
            $activity = YxhdActivityManager::getById($info->activity_id);
            unset($activity->intro_html);
            $info->activity = $activity;
        }

        //带奖品信息
        if (strpos($level, '2') !== false) {
            $prize = YxhdPrizeManager::getById($info->prize_id);
            unset($prize->intro_html);
            $info->prize = $prize;

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
        if (array_key_exists('trade_no', $data)) {
            $info->trade_no = array_get($data, 'trade_no');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('activity_id', $data)) {
            $info->activity_id = array_get($data, 'activity_id');
        }
        if (array_key_exists('total_score', $data)) {
            $info->total_score = array_get($data, 'total_score');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        if (array_key_exists('pay_status', $data)) {
            $info->pay_status = array_get($data, 'pay_status');
        }
        if (array_key_exists('pay_at', $data)) {
            $info->pay_at = array_get($data, 'pay_at');
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
        $infos = new YxhdOrder();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('trade_no', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('activity_id', $con_arr) && !Utils::isObjNull($con_arr['activity_id'])) {
            $infos = $infos->where('activity_id', '=', $con_arr['activity_id']);
        }
        if (array_key_exists('prize_id', $con_arr) && !Utils::isObjNull($con_arr['prize_id'])) {
            $infos = $infos->where('prize_id', '=', $con_arr['prize_id']);
        }
        if (array_key_exists('winning_status', $con_arr) && !Utils::isObjNull($con_arr['winning_status'])) {
            $infos = $infos->where('winning_status', '=', $con_arr['winning_status']);
        }

        $infos = $infos->orderby('id', 'desc');
//        dd($infos->toSql());

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
     * 抽奖方法
     *
     * By TerryQi
     *
     * 2018-12-11
     *
     * @activity_id为抽奖的活动   @default_prize_id为默认的奖品id
     *
     * @return 返回奖品id 如果未抽中奖品返回null
     *
     */
    public static function draw($activity_id, $default_prize_id = null)
    {
        Utils::processLog(__METHOD__, '', "开始抽奖 activity_id："
            . json_encode($activity_id) . " 默认奖品 default_prize_id：" . $default_prize_id);
        //获取全部礼品数
        $yxhdPrizeSettings = YxhdPrizeSettingManager::getListByCon(['activity_id' => $activity_id], false);
        Utils::processLog(__METHOD__, '', "奖品配置信息 yxhdPrizeSettings：" . json_encode($yxhdPrizeSettings));
        $total_rate = $yxhdPrizeSettings->sum('rate');      //概率总和
        Utils::processLog(__METHOD__, '', "总体概率 total_rate：" . json_encode($total_rate));
        //精度为100,000 10万
        $ACCURACY_VALUE = 100000;
        foreach ($yxhdPrizeSettings as $yxhdPrizeSetting) {
            //rate_value是具体的值
            $yxhdPrizeSetting->rate_value = (int)(((double)$yxhdPrizeSetting->rate / $total_rate) * $ACCURACY_VALUE);
        }
        Utils::processLog(__METHOD__, '', "奖品配置信息，设置概率后 yxhdPrizeSettings：" . json_encode($yxhdPrizeSettings));
        $point_value = random_int(0, $ACCURACY_VALUE);
        Utils::processLog(__METHOD__, '', "随机数 point_value：" . json_encode($point_value));
        //进行抽奖
        foreach ($yxhdPrizeSettings as $yxhdPrizeSetting) {
            if ($point_value < $yxhdPrizeSetting->rate_value) {     //抽中奖
                Utils::processLog(__METHOD__, '', " 奖品配置信息 yxhdPrizeSetting：" . json_encode($yxhdPrizeSetting));
                $yxhdPrize = YxhdPrizeManager::getById($yxhdPrizeSetting->prize_id);
                Utils::processLog(__METHOD__, '', " 中奖奖品信息 yxhdPrize：" . json_encode($yxhdPrize));
                //如果不存在奖品或者库存不足
                if (!$yxhdPrize || (($yxhdPrize->total_num - $yxhdPrize->send_num) <= 0)) {
                    Utils::processLog(__METHOD__, '', " 奖品不足" . json_encode($yxhdPrize));
                    return $default_prize_id;
                } else {
                    //抽中奖品，需要将库存减掉1
                    $yxhdPrize->send_num = $yxhdPrize->send_num + 1;
                    $yxhdPrize->save();
                    return $yxhdPrize->id;
                }
            } else {
                $point_value = $point_value - $yxhdPrizeSetting->rate_value;        //减去第一个概率
            }
        }
        return $default_prize_id;
    }

}