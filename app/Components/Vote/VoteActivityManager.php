<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Components\DateTool;
use App\Components\GZH\BusiWordManager;
use App\Components\GZH\WeChatManager;
use App\Components\LoginManager;
use App\Components\SMSManager;
use App\Components\Vote\VoteADManager;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteAD;
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

class VoteActivityManager
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
        $info = VoteActivity::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0:带礼品信息 1：带广告图信息 2：带负责人信息 3：带参赛选手信息
     */
    public static function getInfoByLevel($info, $level)
    {
        //基本信息
        $info->apply_status_str = Utils::VOTE_APPLY_STATUS_VAL[$info->apply_status];
        $info->vote_status_str = Utils::VOTE_VOTE_STATUS_VAL[$info->vote_status];
        $info->valid_status_str = Utils::VOTE_ACTIVITY_VALID_STATUS_VAL[$info->valid_status];
        $info->is_settle_str = Utils::VOTE_ACTIVITY_IS_SETTLE_VAL[$info->is_settle];
        $info->status_str = Utils::VOTE_STATUS_VAL[$info->status];

//        dd($info);
        //其中，获取end_time以便报名首页的倒计时计算
        if ($info->apply_status == '1' || $info->apply_status == '0') {
            $info->end_time = $info->apply_end_time;
        } else {
            $info->end_time = $info->vote_end_time;
        }

        //录入人员
        $info->admin = AdminManager::getById($info->admin_id);


        /*
         * 此处存在问题，但未进行优化，应该遵循getInfoByLevel的方式，根据level来设定rule和present_rule对象
         *
         * By TerryQi
         *
         * 2018-12-17
         */
        //大赛规则
        if ($info->rule_id) {
            $rule = VoteRuleManager::getById($info->rule_id);
            $info->rule_html = ($rule == null) ? "" : $rule->rule_html;
        }

        if ($info->present_rule_id) {
            $present_rule = VoteRuleManager::getById($info->present_rule_id);
            $info->present_rule_html = ($present_rule == null) ? "" : $present_rule->rule_html;
        }
//        dd($info);
        //带礼品信息
        if (strpos($level, '0') !== false) {
            //获取礼品列表信息
            if ($info->sel_gift_ids) {
                $gift_ids = explode(",", $info->sel_gift_ids);
                $con_arr = array(
                    'status' => '1',
                    'ids_arr' => $gift_ids
                );
                $info->sel_gifts = VoteGiftManager::getListByCon($con_arr, false);
            }
        }

        //带广告图信息
        if (strpos($level, '1') !== false) {
            //获取首页广告图信息
            if ($info->sel_index_ad_ids) {
                $ad_ids = explode(",", $info->sel_index_ad_ids);
                $con_arr = array(
                    'status' => '1',
                    'ids_arr' => $ad_ids
                );
                $info->sel_index_ads = VoteADManager::getListByCon($con_arr, false);
            }
            //排名页广告信息
            if ($info->sel_pm_ad_ids) {
                $ad_ids = explode(",", $info->sel_pm_ad_ids);
                $con_arr = array(
                    'status' => '1',
                    'ids_arr' => $ad_ids
                );
                $info->sel_pm_ads = VoteADManager::getListByCon($con_arr, false);
            }
            //投票后的弹出层
            if ($info->sel_tp_ad_ids) {
                $ad_ids = explode(",", $info->sel_tp_ad_ids);
                $con_arr = array(
                    'status' => '1',
                    'ids_arr' => $ad_ids
                );
                $info->sel_tp_ads = VoteADManager::getListByCon($con_arr, false);
            }
        }

        //带责任人信息
        if (strpos($level, '2') !== false) {
            //第一责任人
            if ($info->c_admin_id1) {
                $info->c_admin1 = AdminManager::getById($info->c_admin_id1);
            }
            //第二责任人
            if ($info->c_admin_id2) {
                $info->c_admin2 = AdminManager::getById($info->c_admin_id2);
            }
            //地推团队
            if ($info->vote_team_id) {
                $info->vote_team = VoteTeamManager::getById($info->vote_team_id);
            }
        }

        //带参赛选手信息
        if (strpos($level, '3') !== false) {
            //参赛选手
            $con_arr = array(
                'activity_id' => $info->id
            );
//        dd($con_arr);
            $vote_users = VoteUserManager::getListByCon($con_arr, false);
            foreach ($vote_users as $vote_user) {
                $vote_user = VoteUserManager::getInfoByLevel($vote_user, '0');
            }
            $info->vote_users = $vote_users;
        }
//        dd($info);

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
        if (array_key_exists('music', $data)) {
            $info->music = array_get($data, 'music');
        }
        if (array_key_exists('video', $data)) {
            $info->video = array_get($data, 'video');
        }
        if (array_key_exists('gzh_ewm', $data)) {
            $info->gzh_ewm = array_get($data, 'gzh_ewm');
        }
        if (array_key_exists('notice_text', $data)) {
            $info->notice_text = array_get($data, 'notice_text');
        }
        if (array_key_exists('notice_url', $data)) {
            $info->notice_url = array_get($data, 'notice_url');
        }
        if (array_key_exists('intro', $data)) {
            $info->intro = array_get($data, 'intro');
        }
        if (array_key_exists('code', $data)) {
            $info->code = array_get($data, 'code');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('rule_id', $data)) {
            $info->rule_id = array_get($data, 'rule_id');
        }
        if (array_key_exists('present_rule_id', $data)) {
            $info->present_rule_id = array_get($data, 'present_rule_id');
        }
        if (array_key_exists('jg_intro_html', $data)) {
            $info->jg_intro_html = array_get($data, 'jg_intro_html');
        }
        if (array_key_exists('gift_html', $data)) {
            $info->gift_html = array_get($data, 'gift_html');
        }
        if (array_key_exists('apply_html', $data)) {
            $info->apply_html = array_get($data, 'apply_html');
        }
        if (array_key_exists('sel_gift_ids', $data)) {
            $info->sel_gift_ids = array_get($data, 'sel_gift_ids');
        }
        if (array_key_exists('index_ad_img', $data)) {
            $info->index_ad_img = array_get($data, 'index_ad_img');
        }
        if (array_key_exists('index_ad_url', $data)) {
            $info->index_ad_url = array_get($data, 'index_ad_url');
        }
        if (array_key_exists('sel_index_ad_ids', $data)) {
            $info->sel_index_ad_ids = array_get($data, 'sel_index_ad_ids');
        }
        if (array_key_exists('sel_pm_ad_ids', $data)) {
            $info->sel_pm_ad_ids = array_get($data, 'sel_pm_ad_ids');
        }
        if (array_key_exists('sel_tp_ad_ids', $data)) {
            $info->sel_tp_ad_ids = array_get($data, 'sel_tp_ad_ids');
        }
        if (array_key_exists('show_ad_mode', $data)) {
            $info->show_ad_mode = array_get($data, 'show_ad_mode');
        }
        if (array_key_exists('complain_num', $data)) {
            $info->complain_num = array_get($data, 'complain_num');
        }
        if (array_key_exists('join_num', $data)) {
            $info->join_num = array_get($data, 'join_num');
        }
        if (array_key_exists('vote_num', $data)) {
            $info->vote_num = array_get($data, 'vote_num');
        }
        if (array_key_exists('show_num', $data)) {
            $info->show_num = array_get($data, 'show_num');
        }
        if (array_key_exists('gift_num', $data)) {
            $info->gift_num = array_get($data, 'gift_num');
        }
        if (array_key_exists('gift_money', $data)) {
            $info->gift_money = array_get($data, 'gift_money');
        }
        if (array_key_exists('share_num', $data)) {
            $info->share_num = array_get($data, 'share_num');
        }
        if (array_key_exists('activity_start_time', $data)) {
            $info->activity_start_time = array_get($data, 'activity_start_time');
        }
        if (array_key_exists('activity_end_time', $data)) {
            $info->activity_end_time = array_get($data, 'activity_end_time');
        }
        if (array_key_exists('apply_start_time', $data)) {
            $info->apply_start_time = array_get($data, 'apply_start_time');
        }
        if (array_key_exists('apply_end_time', $data)) {
            $info->apply_end_time = array_get($data, 'apply_end_time');
        }
        if (array_key_exists('vote_start_time', $data)) {
            $info->vote_start_time = array_get($data, 'vote_start_time');
        }
        if (array_key_exists('vote_end_time', $data)) {
            $info->vote_end_time = array_get($data, 'vote_end_time');
        }
        if (array_key_exists('vote_mode', $data)) {
            $info->vote_mode = array_get($data, 'vote_mode');
        }
        if (array_key_exists('subscribe_mode', $data)) {
            $info->subscribe_mode = array_get($data, 'subscribe_mode');
        }
        if (array_key_exists('vote_audit_mode', $data)) {
            $info->vote_audit_mode = array_get($data, 'vote_audit_mode');
        }
        if (array_key_exists('daily_vote_to_user_num', $data)) {
            $info->daily_vote_to_user_num = array_get($data, 'daily_vote_to_user_num');
        }
        if (array_key_exists('daily_vote_num', $data)) {
            $info->daily_vote_num = array_get($data, 'daily_vote_num');
        }
        if (array_key_exists('vote_notice_mode', $data)) {
            $info->vote_notice_mode = array_get($data, 'vote_notice_mode');
        }
        if (array_key_exists('gift_notice_mode', $data)) {
            $info->gift_notice_mode = array_get($data, 'gift_notice_mode');
        }
        if (array_key_exists('lock_num_mode', $data)) {
            $info->lock_num_mode = array_get($data, 'lock_num_mode');
        }
        if (array_key_exists('vote_vertify_mode', $data)) {
            $info->vote_vertify_mode = array_get($data, 'vote_vertify_mode');
        }
        if (array_key_exists('apply_min_num', $data)) {
            $info->apply_min_num = array_get($data, 'apply_min_num');
        }
        if (array_key_exists('vote_message_mode', $data)) {
            $info->vote_message_mode = array_get($data, 'vote_message_mode');
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
        if (array_key_exists('first_prize_num', $data)) {
            $info->first_prize_num = array_get($data, 'first_prize_num');
        }
        if (array_key_exists('second_prize_num', $data)) {
            $info->second_prize_num = array_get($data, 'second_prize_num');
        }
        if (array_key_exists('third_prize_num', $data)) {
            $info->third_prize_num = array_get($data, 'third_prize_num');
        }
        if (array_key_exists('honor_prize_num', $data)) {
            $info->honor_prize_num = array_get($data, 'honor_prize_num');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('activity_status', $data)) {
            $info->activity_status = array_get($data, 'activity_status');
        }
        if (array_key_exists('vote_status', $data)) {
            $info->vote_status = array_get($data, 'vote_status');
        }
        if (array_key_exists('apply_status', $data)) {
            $info->apply_status = array_get($data, 'apply_status');
        }
        if (array_key_exists('valid_status', $data)) {
            $info->valid_status = array_get($data, 'valid_status');
        }
        if (array_key_exists('valid_at', $data)) {
            $info->valid_at = array_get($data, 'valid_at');
        }
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
        }
        if (array_key_exists('c_admin_id1', $data)) {
            $info->c_admin_id1 = array_get($data, 'c_admin_id1');
        }
        if (array_key_exists('c_admin_id2', $data)) {
            $info->c_admin_id2 = array_get($data, 'c_admin_id2');
        }
        if (array_key_exists('vote_team_id', $data)) {
            $info->vote_team_id = array_get($data, 'vote_team_id');
        }

        if (array_key_exists('apply_info_1', $data)) {
            $info->apply_info_1 = array_get($data, 'apply_info_1');
        }
        if (array_key_exists('apply_info_2', $data)) {
            $info->apply_info_2 = array_get($data, 'apply_info_2');
        }
        if (array_key_exists('apply_info_3', $data)) {
            $info->apply_info_3 = array_get($data, 'apply_info_3');
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
        $infos = new VoteActivity();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('code', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('id', $con_arr) && !Utils::isObjNull($con_arr['id'])) {
            $infos = $infos->where('id', '=', $con_arr['id']);
        }
        if (array_key_exists('code', $con_arr) && !Utils::isObjNull($con_arr['code'])) {
            $infos = $infos->where('code', '=', $con_arr['code']);
        }
        if (array_key_exists('activity_status', $con_arr) && !Utils::isObjNull($con_arr['activity_status'])) {
            $infos = $infos->where('activity_status', '=', $con_arr['activity_status']);
        }
        if (array_key_exists('vote_status', $con_arr) && !Utils::isObjNull($con_arr['vote_status'])) {
            $infos = $infos->where('vote_status', '=', $con_arr['vote_status']);
        }
        if (array_key_exists('vote_status_arr', $con_arr) && !Utils::isObjNull($con_arr['vote_status_arr'])) {
            $infos = $infos->wherein('vote_status', $con_arr['vote_status_arr']);
        }
        if (array_key_exists('apply_status', $con_arr) && !Utils::isObjNull($con_arr['apply_status'])) {
            $infos = $infos->where('apply_status', '=', $con_arr['apply_status']);
        }
        if (array_key_exists('apply_status_arr', $con_arr) && !Utils::isObjNull($con_arr['apply_status_arr'])) {
            $infos = $infos->wherein('apply_status', $con_arr['apply_status_arr']);
        }
        if (array_key_exists('valid_status', $con_arr) && !Utils::isObjNull($con_arr['valid_status'])) {
            $infos = $infos->where('valid_status', '=', $con_arr['valid_status']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('c_admin_id1', $con_arr) && !Utils::isObjNull($con_arr['c_admin_id1'])) {
            $infos = $infos->where('c_admin_id1', '=', $con_arr['c_admin_id1']);
        }
        if (array_key_exists('admin_id', $con_arr) && !Utils::isObjNull($con_arr['admin_id'])) {
            $infos = $infos->where('admin_id', '=', $con_arr['admin_id']);
        }
        if (array_key_exists('created_start_at', $con_arr) && !Utils::isObjNull($con_arr['created_start_at'])) {
            $infos = $infos->where('created_at', '=', $con_arr['created_start_at']);
        }
        if (array_key_exists('vote_team_id', $con_arr) && !Utils::isObjNull($con_arr['vote_team_id'])) {
            $infos = $infos->where('vote_team_id', '=', $con_arr['vote_team_id']);
        }
        if (array_key_exists('vote_end_at', $con_arr) && !Utils::isObjNull($con_arr['vote_end_at'])) {
            $infos = $infos->where('vote_end_time', '>=', $con_arr['vote_end_at'])
                ->where('vote_end_time', '<=', DateTool::dateAdd('D', 1, $con_arr['vote_end_at']));
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
     * 增加统计数据 item统计项目 join_num：参与人数 vote_num：投票数 show_num：展示数 gift_num：礼物数 gift_money：礼物价格 share_num：分享数
     */
    public static function addStatistics($activity_id, $item, $num)
    {
        $activity = self::getById($activity_id);
        switch ($item) {
            case "join_num":
                $activity->join_num = $activity->join_num + $num;
                break;
            case "vote_num":
                $activity->vote_num = $activity->vote_num + $num;
                break;
            case "show_num":
                $activity->show_num = $activity->show_num + $num;
                break;
            case "gift_num":
                $activity->gift_num = $activity->gift_num + $num;
                break;
            case "gift_money":
                $activity->gift_money = $activity->gift_money + $num;
                break;
            case "share_num":
                $activity->share_num = $activity->share_num + $num;
                break;
            case "complain_num":
                $activity->complain_num = $activity->complain_num + $num;
                break;
        }
        $activity->save();
    }

    /*
     * 根据活动id获取首页广告
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public static function getIndexAdArr($activity_id)
    {
        $activity = VoteActivityManager::getById($activity_id);
        $show_ad_mode = $activity->show_ad_mode;

        $index_ads = array();

        if ($activity->index_ad_img) {
            array_push($index_ads, ['img' => $activity->index_ad_img, 'url' => $activity->index_ad_url]);
        }

        $countdown_banner = null;

        if ($show_ad_mode == 1) {
            $carbon_vote_end_time = Carbon::createFromFormat('Y-m-d', substr($activity->vote_end_time, 0, 10));
            $carbon_now = Carbon::now();

            $diff_day = $carbon_now->diffInDays($carbon_vote_end_time, false);

            switch ($diff_day) {
                case 0:
                    $countdown_banner = VoteADManager::getListByCon(['mode' => 'day1'], false)->first();
                    break;
                case 1:
                    $countdown_banner = VoteADManager::getListByCon(['mode' => 'day2'], false)->first();
                    break;
                case 2:
                    $countdown_banner = VoteADManager::getListByCon(['mode' => 'day3'], false)->first();
                    break;
                case 3:
                    $countdown_banner = VoteADManager::getListByCon(['mode' => 'day4'], false)->first();
                    break;
                case 4:
                    $countdown_banner = VoteADManager::getListByCon(['mode' => 'day5'], false)->first();
                    break;
            }
        }

        if ($activity->sel_index_ad_ids) {
            $ad_ids_arr = explode(',', $activity->sel_index_ad_ids);
            $countdown_banner_index = array_search(0, $ad_ids_arr);

            if ($show_ad_mode == 1) {
                if ($countdown_banner) {
                    if ($countdown_banner_index === false) {
                        array_unshift($ad_ids_arr, $countdown_banner->id);
                    } else {
                        $ad_ids_arr[$countdown_banner_index] = $countdown_banner->id;
                    }
                }
            } else {
                if ($countdown_banner_index !== false) {
                    array_splice($ad_ids_arr, $countdown_banner_index, 1);
                }
            }

            $vote_ads = VoteAD::whereIn('id', $ad_ids_arr)
                ->orderByRaw(DB::raw("FIND_IN_SET(id, '" . implode(',', $ad_ids_arr) . "')"))
                ->get();

            foreach ($vote_ads as $vote_ad) {
                array_push($index_ads, ['img' => $vote_ad->img, 'url' => $vote_ad->url]);
            }
        } else {
            if ($show_ad_mode && $countdown_banner) {
                array_push($index_ads, ['img' => $countdown_banner->img, 'url' => $countdown_banner->url]);
            }
        }

        return $index_ads;
    }


    /*
     * 根据活动id获取排名页广告
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public static function getPMAdArr($activity_id)
    {
        $activity = VoteActivityManager::getById($activity_id);
        //首页广告
        $pm_ads = array();
        if ($activity->index_ad_img) {
            array_push($pm_ads, ['img' => $activity->index_ad_img, 'url' => $activity->index_ad_url]);
        }
        if ($activity->sel_index_ad_ids) {
            $con_arr = array(
                'ids_arr' => explode(',', $activity->sel_pm_ad_ids)
            );
            $vote_ads = VoteADManager::getListByCon($con_arr, false);
            foreach ($vote_ads as $vote_ad) {
                array_push($pm_ads, ['img' => $vote_ad->img, 'url' => $vote_ad->url]);
            }
        }
        return $pm_ads;
    }

    /*
     * 根据活动id获取投票成功后的广告-随机
     *
     * By TerryQi
     *
     * 2018-07-19
     *
     */
    public static function getTPAdArr($activity_id)
    {
        $activity = VoteActivityManager::getById($activity_id);
        //首页广告
        $tp_ads = array();
        if ($activity->sel_tp_ad_ids) {
            $con_arr = array(
                'ids_arr' => explode(',', $activity->sel_tp_ad_ids)
            );
            $vote_ads = VoteADManager::getListByCon($con_arr, false);
            foreach ($vote_ads as $vote_ad) {
                array_push($tp_ads, ['img' => $vote_ad->img, 'url' => $vote_ad->url]);
            }
        }
        return $tp_ads;
    }

    //根据活动信息，获取图文消息
    public static function convertToNewsItem($activity_id)
    {
        $activity = self::getById($activity_id);
        $newsItem = new NewsItem([
            'title' => $activity->share_title,
            'description' => $activity->share_desc,
            'url' => URL::asset('/vote/index') . "?activity_id=" . $activity_id,
            'image' => $activity->img
        ]);
        $news = new News([$newsItem]);
        return $news;
    }


    //计划任务
    /*
     * 设置活动的相关状态-报名状态、投票状态
     *
     * By TerryQi
     *
     * 2018-07-23
     */
    public static function activitySchedule()
    {
        $activities = VoteActivityManager::getListByCon([], false);
        foreach ($activities as $activity) {
            //报名状态设置///////////////////////////////////////////////////////////////////////////////////////////
            //当前时间小于报名开始时间-报名未开始
            if (DateTool::dateDiff('N', $activity->apply_start_time, DateTool::getCurrentTime()) < 0) {
                $activity->apply_status = '0';  //未开始
                Utils::processLog(__METHOD__, '', "报名未开始 " . "activity:" . $activity->name . " id:" . $activity->id);
            }
            //当前时间大于报名开始时间、小于报名结束时间-报名未开始
            if (DateTool::dateDiff('N', $activity->apply_start_time, DateTool::getCurrentTime()) >= 0
                && DateTool::dateDiff('N', $activity->apply_end_time, DateTool::getCurrentTime()) < 0) {
                $activity->apply_status = '1';  //已经开始
                Utils::processLog(__METHOD__, '', "报名已开始 " . "activity:" . $activity->name . " id:" . $activity->id);
            }
            //当前时间大于报名结束时间
            if (DateTool::dateDiff('N', $activity->apply_end_time, DateTool::getCurrentTime()) >= 0) {
                $activity->apply_status = '2';  //已经结束
                Utils::processLog(__METHOD__, '', "报名已结束 " . "activity:" . $activity->name . " id:" . $activity->id);
            }
            //投票状态设置////////////////////////////////////////////////////////////////////////////////////////////
            //当前时间小于投票开始时间-投票未开始
            if (DateTool::dateDiff('N', $activity->vote_start_time, DateTool::getCurrentTime()) < 0) {
                $activity->vote_status = '0';  //未开始
                Utils::processLog(__METHOD__, '', "投票未开始 " . "activity:" . $activity->name . " id:" . $activity->id);
            }

            //当前时间大于投票开始时间、小于投票结束时间-投票未开始
            if (DateTool::dateDiff('N', $activity->vote_start_time, DateTool::getCurrentTime()) >= 0
                && DateTool::dateDiff('N', $activity->vote_end_time, DateTool::getCurrentTime()) < 0) {
                $activity->vote_status = '1';  //已经开始
                Utils::processLog(__METHOD__, '', "投票已开始 " . "activity:" . $activity->name . " id:" . $activity->id);
            }
            //当前时间大于投票结束时间
            if (DateTool::dateDiff('N', $activity->vote_end_time, DateTool::getCurrentTime()) >= 0) {
                $activity->vote_status = '2';  //已经结束
                Utils::processLog(__METHOD__, '', "投票已结束 " . "activity:" . $activity->name . " id:" . $activity->id);
            }

            $activity->save();
        }
    }


    //激活动活提醒
    /*
     * By TerryQi
     *
     * 2018-07-23
     *
     */
    public static function validActivitySchedule()
    {
        $activities = VoteActivityManager::getListByCon(['status' => '1'], false);
        foreach ($activities as $activity) {
            //活动报名结束时间五天内
            if (DateTool::dateDiff('D', $activity->apply_end_time, DateTool::getCurrentTime()) <= 0
                && DateTool::dateDiff('D', $activity->apply_end_time, DateTool::getCurrentTime()) > -5) {
                //活动未激活
                if (!self::isActivityValid($activity->id)) {
                    //活动的激活状态变为未激活
                    $activity->valid_status = '0';
                    //发送提醒通知
                    if ($activity->c_admin_id1) {
                        $admin = AdminManager::getById($activity->c_admin_id1); //获取第一责任人
                        //向第一责任人发送短信
                        if ($admin && $admin->phonenum) {
                            $sms_text = $activity->name . " " . $activity->apply_end_time;
                            SMSManager::sendSMS($admin->phonenum, Utils::VOTE_SMS_TEMPLATE_ACTIVITY_NOTE_VALID
                                , $sms_text);
                            Utils::processLog(__METHOD__, '', "活动 " . "activity:" . $activity->name . " 管理员:" . $admin->name . " 短信内容:" . $sms_text);
                        }
                    }
                } else {
                    //活动的状态变为已激活
                    $activity->valid_status = '1';
                    $activity->valid_at = DateTool::getCurrentTime();
                }
                $activity->save();
            }
        }
    }

    /*
     * 选手待审核提醒
     *
     * By TerryQi
     *
     * 2018-07-23
     */
    public static function auditVoteUserSchedule()
    {
        $activities = VoteActivityManager::getListByCon([], false);
        foreach ($activities as $activity) {
            $con_arr = array(
                'activity_id' => $activity->id,
                'audit_status' => '0'
            );
            $not_audit_vote_users = VoteUserManager::getListByCon($con_arr, false);
            if ($not_audit_vote_users->count() > 0) {
                Utils::processLog(__METHOD__, '', "活动 " . "activity:" . $activity->name . " id:" . $activity->id . " 待审核选手数：" . $not_audit_vote_users->count());
                //发送提醒通知
                if ($activity->c_admin_id1) {
                    $admin = AdminManager::getById($activity->c_admin_id1); //获取第一责任人
                    //向第一责任人发送短信
                    if ($admin && $admin->phonenum) {
                        $sms_text = $activity->name;
                        SMSManager::sendSMS($admin->phonenum, Utils::VOTE_SMS_TEMPLATE_AUDIT_NOTICE
                            , $sms_text);
                        Utils::processLog(__METHOD__, '', "活动 " . "activity:" . $activity->name . " 管理员:" . $admin->name . " 短信内容:" . $sms_text);
                    }
                }
            }
        }
    }

    /*
     * 设置投票期的选手排名
     *
     * By TerryQi
     *
     * 2018-07-24
     *
     */
    public static function activityVoteUserPMSchedule()
    {
        //活动在投票中
        $con_arr = array(
            'vote_status' => '1',
        );
        $activities = VoteActivityManager::getListByCon($con_arr, false);
        foreach ($activities as $activity) {
            Utils::processLog(__METHOD__, '', "设置活动排名 " . "activity:" . $activity->name . " id:" . $activity->id);
            $i = 0;
            $con_arr = array(
                'activity_id' => $activity->id,
                'status' => '1',            //生效
                'audit_status' => '1',      //审核通过
                'orderby' => [
                    'vote_num' => 'desc'
                ]
            );
            $vote_users = VoteUserManager::getListByCon($con_arr, false);
            foreach ($vote_users as $vote_user) {
                $vote_user->yes_pm = (++$i);
                $vote_user->save();
            }
        }
    }


    /*
     * 向关注着发送消息
     *
     * By TerryQi
     *
     * 2018-07-24
     *
     */
    public static function voteGuanzhuMessageSchedule()
    {
        //在投票中的活动
        $con_arr = array(
            'vote_status' => '1',
            'status' => '1'
        );
        $activities = VoteActivityManager::getListByCon($con_arr, false);
        foreach ($activities as $activity) {
            Utils::processLog(__METHOD__, '', "发送关注者消息 " . "activity:" . $activity->name . " id:" . $activity->id);
            $con_arr = array(
                'activity_id' => $activity->id,
            );
            //该活动下所有的选手
            $vote_users = VoteUserManager::getListByCon($con_arr, false);
            foreach ($vote_users as $vote_user) {
                //如果选手是生效状态
                if ($vote_user->status == '1') {
                    $con_arr = array(
                        'vote_user_id' => $vote_user->id
                    );
                    //获取全部的关注
                    $vote_guanzhus = VoteGuanZhuManager::getListByCon($con_arr, false);
                    foreach ($vote_guanzhus as $vote_guanzhu) {
                        //准备发送消息
                        $con_arr = array(
                            'user_id' => $vote_guanzhu->user_id,
                            'account_type' => 'fwh',
                            'busi_name' => 'isart'
                        );
                        //获取登录信息
                        $login = LoginManager::getListByCon($con_arr, false)->first();
                        //如果存在登录信息-即已经关注服务号
                        /*
                         * 此处有一个逻辑未考虑，即用户取消关注服务号
                         *
                         */
                        if ($login) {
                            $openid = $login->ve_value1;
                            //获取业务话术
                            $busi_word = BusiWordManager::getListByCon(['template_id' => 'TEMPLATE_VOTE_GUANZHU_MESSAGE'], false)->first();
                            if (!$busi_word) {
                                return;
                            }
                            //替换其中的关键字
                            $text = $busi_word->content;
                            $vote_user = VoteUserManager::getInfoByLevel($vote_user, '2');
                            $text = str_replace("{vote_user_id}", $vote_user->id, $text);
                            $text = str_replace("{vote_user_name}", $vote_user->name, $text);
                            $text = str_replace("{vote_user_vote_num}", $vote_user->vote_num, $text);
                            $text = str_replace("{vote_user_pm}", $vote_user->pm, $text);
                            Utils::processLog(__METHOD__, '', "发送关注者消息 " . "login:" . json_encode($login));
                            Utils::processLog(__METHOD__, '', "消息内容 " . "text:" . $text);
                            //通过服务号发送
                            $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL['isart']);
                            WeChatManager::sendCustomerMessage($text, $openid, $app);
                        }
                    }
                }
            }
        }
    }


    /*
     * 根据激活状态设定投票的结束时间
     *
     * By TerryQi
     *
     * 2018-09-13
     *
     */
    public static function setVoteEndTimeSchedule()
    {
        //获取全部未激活的活动并且报名未结束的活动
        $con_arr = array(
            'apply_status_arr' => ['0', '1'],    //报名未结束
            'valid_status' => '0',
            'status' => '1'
        );
        $activities = VoteActivityManager::getListByCon($con_arr, false);
        //循环活动
        foreach ($activities as $activity) {
            Utils::processLog(__METHOD__, '', "根据激活状态设定投票的结束时间 " . "活动名称:" . $activity->name . " 活动id:" . $activity->id);
            //如果活动已经激活，则设置vote_end_time，并设置激活状态
            if (self::isActivityValid($activity->id)) {     //活动已经激活
                Utils::processLog(__METHOD__, '', "活动激活设置投票结束时间 " . "活动名称:" . $activity->name . " 活动id:" . $activity->id);
                $activity->valid_status = '1';
                $activity->valid_at = DateTool::getCurrentTime();
                $activity->vote_end_time = DateTool::dateAdd('D', '15', DateTool::getToday(), 'Y-m-d') . " " . "22:10:00";      //默认设置为15天后的22:10
                $activity->save();
            }
            //如果活动即将结束，则设置投票结束时间
            if (DateTool::dateDiff('N', DateTool::getCurrentTime(), $activity->apply_end_time) <= 2) {
                Utils::processLog(__METHOD__, '', "活动即将结束设置投票结束时间 " . "活动名称:" . $activity->name . " 活动id:" . $activity->id);
                $activity->vote_end_time = DateTool::dateAdd('D', '15', DateTool::getToday(), 'Y-m-d') . " " . "22:10:00";      //默认设置为15天后的22:10
//                dd($activity);
                $activity->save();
            }
        }
    }


    /*
     * 设置报名结束、投票未结束的活动的激活状态
     *
     * By TerryQi
     *
     * 2018-09-27
     */

    public static function setVoteValidWhenApplyStatus2VoteStatus12()
    {
        //获取全部的报名已经结束、投票中的活动
        $con_arr = array(
            'apply_status' => '2',          //报名结束
            'vote_status_arr' => ['0', '1'],       //投票中
            'status' => '1'
        );
        $activities = VoteActivityManager::getListByCon($con_arr, false);
        //循环活动
        foreach ($activities as $activity) {
            //如果活动已经激活，则设置vote_end_time，并设置激活状态
            if (self::isActivityValid($activity->id)) {     //活动已经激活
                $activity->valid_status = '1';
                $activity->valid_at = DateTool::getCurrentTime();
                $activity->save();
            }
        }
    }


    /*
     * 增加自动刷票逻辑，逻辑为每2小时随机为未结束的活动的选手增加0-2票
     *
     * By TerryQi
     *
     * 2018-10-11
     */
    public static function addVoteUserVoteNumSchedule()
    {
        $con_arr = array(
            'vote_status' => '1',      //投票开始
            'status' => '1'
        );
        $activities = VoteActivityManager::getListByCon($con_arr, false);
        //循环活动
        foreach ($activities as $activity) {
            //！！！此处再次获取活动信息
            //此处主要考虑计划任务在进行中时，运营人员停止任务或者活动停止，因为计划任务执行需要一段时间，此处再次获取数据，确保数据的一致性
            $activity = self::getById($activity->id);
            if ($activity->vote_status != '2') {        //活动未结束&&活动生效
                $con_arr = array(
                    'activity_id' => $activity->id,
                    'audit_status' => '1',
                    'status' => '1'
                );
                $vote_users = VoteUserManager::getListByCon($con_arr, false);
                foreach ($vote_users as $vote_user) {
                    $add_vote_num = random_int(0, 2);
                    //选手增加票数
                    VoteUserManager::addStatistics($vote_user->id, 'vote_num', $add_vote_num);
                    //大赛增加票数
                    self::addStatistics($activity->id, 'vote_num', $add_vote_num);
                }
            }
        }
    }


    //判断活动是否到激活条件
    /*
     * 活动是否达到激活条件
     *
     * By TerryQi
     *
     * 2018-09-13
     */
    public static function isActivityValid($activity_id)
    {
        //全部审核通过的选手
        $con_arr = array(
            'activity_id' => $activity_id,
            'audit_status' => '1',
            'status' => '1'
        );
        $all_vote_users = VoteUserManager::getListByCon($con_arr, false);
        //全部审核通过未激活的选手
        $con_arr = array(
            'activity_id' => $activity_id,
            'audit_status' => '1',
            'valid_status' => '0',
            'status' => '1'
        );
        $not_valid_vote_users = VoteUserManager::getListByCon($con_arr, false);
        Utils::processLog(__METHOD__, '', "活动 " . "activity:" . $activity_id . " 全部审核通过选手数:" . $all_vote_users->count() . " 未激活选手数：" . $not_valid_vote_users->count());
        //2018年11月2日，此处优化除0的问题
        if ($all_vote_users->count() == 0) {
            return false;
        }
        //正常计算
        if ((double)$not_valid_vote_users->count() / $all_vote_users->count() >= 0.2) {
            return false;       //未激活
        } else {
            return true;        //已激活
        }
    }


}