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
use App\Models\Mryh\MryhGame;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhGameManager
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
        $info = MryhGame::where('id', '=', $id)->first();
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
        $info->type_str = Utils::MRYH_GAME_TYPE_VAL[$info->type];
        $info->game_mode_str = Utils::MRYH_GAME_MODE_VAL[$info->game_mode];
        $info->show_status_str = Utils::MRYH_GAME_SHOW_STATUS_VAL[$info->show_status];
        $info->join_status_str = Utils::MRYH_GAME_JOIN_STATUS_VAL[$info->join_status];
        $info->game_status_str = Utils::MRYH_GAME_GAME_STATUS_VAL[$info->game_status];
        $info->jiesuan_status_str = Utils::MRYH_GAME_JIESUAN_STATUS_VAL[$info->jiesuan_status];
        $info->creator_type_str = Utils::MRYH_GAME_CREATOR_TYPE_VAL[$info->creator_type];

        //2018年12月2日，为解决活动开始时奖金池太少的问题，可以由公司投入奖金
        /*
         * By TerryQi
         *
         * adv_price+total_moeny为总的奖金池
         */
        $info->total_money = $info->total_money + $info->adv_price;

        //计算预计奖金额
        if (($info->join_num - $info->fail_num) == 0) {
            //如果还没人参加，则默认为*2 join_price
            if ($info->join_num == 0) {
                $info->anti_prize = round(($info->join_price * 2), 2);
            } else {
                $info->anti_prize = round($info->total_money, 2);
            }
        } else {
            $info->anti_prize = round(($info->total_money / ($info->join_num - $info->fail_num)), 2);
        }

        //创建者
        if ($info->creator_type == 0) {         //管理员创建
            $info->creator = AdminManager::weakInfo(AdminManager::getById($info->creator_id), '');
        } else {
            $info->creator = UserManager::getById($info->creator_id);
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
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('intro_html', $data)) {
            $info->intro_html = array_get($data, 'intro_html');
        }
        if (array_key_exists('intro_text', $data)) {
            $info->intro_text = array_get($data, 'intro_text');
        }
        if (array_key_exists('rule_id', $data)) {
            $info->rule_id = array_get($data, 'rule_id');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('join_price', $data)) {
            $info->join_price = array_get($data, 'join_price');
        }
        if (array_key_exists('adv_price', $data)) {
            $info->adv_price = array_get($data, 'adv_price');
        }
        if (array_key_exists('password', $data)) {
            $info->password = array_get($data, 'password');
        }
        if (array_key_exists('max_join_num', $data)) {
            $info->max_join_num = array_get($data, 'max_join_num');
        }
        if (array_key_exists('complain_num', $data)) {
            $info->complain_num = array_get($data, 'complain_num');
        }
        if (array_key_exists('join_num', $data)) {
            $info->join_num = array_get($data, 'join_num');
        }
        if (array_key_exists('work_num', $data)) {
            $info->work_num = array_get($data, 'work_num');
        }
        if (array_key_exists('show_num', $data)) {
            $info->show_num = array_get($data, 'show_num');
        }
        if (array_key_exists('total_money', $data)) {
            $info->total_money = array_get($data, 'total_money');
        }
        if (array_key_exists('success_num', $data)) {
            $info->success_num = array_get($data, 'success_num');
        }
        if (array_key_exists('fail_num', $data)) {
            $info->fail_num = array_get($data, 'fail_num');
        }
        if (array_key_exists('show_start_time', $data)) {
            $info->show_start_time = array_get($data, 'show_start_time');
        }
        if (array_key_exists('show_end_time', $data)) {
            $info->show_end_time = array_get($data, 'show_end_time');
        }
        if (array_key_exists('join_start_time', $data)) {
            $info->join_start_time = array_get($data, 'join_start_time');
        }
        if (array_key_exists('join_end_time', $data)) {
            $info->join_end_time = array_get($data, 'join_end_time');
        }
        if (array_key_exists('game_start_time', $data)) {
            $info->game_start_time = array_get($data, 'game_start_time');
        }
        if (array_key_exists('game_end_time', $data)) {
            $info->game_end_time = array_get($data, 'game_end_time');
        }
        if (array_key_exists('game_mode', $data)) {
            $info->game_mode = array_get($data, 'game_mode');
        }
        if (array_key_exists('target_join_day', $data)) {
            $info->target_join_day = array_get($data, 'target_join_day');
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
        if (array_key_exists('show_flag', $data)) {
            $info->show_flag = array_get($data, 'show_flag');
        }
        if (array_key_exists('recomm_flag', $data)) {
            $info->recomm_flag = array_get($data, 'recomm_flag');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        if (array_key_exists('show_status', $data)) {
            $info->show_status = array_get($data, 'show_status');
        }
        if (array_key_exists('join_status', $data)) {
            $info->join_status = array_get($data, 'join_status');
        }
        if (array_key_exists('game_status', $data)) {
            $info->game_status = array_get($data, 'game_status');
        }
        if (array_key_exists('jiesuan_status', $data)) {
            $info->jiesuan_status = array_get($data, 'jiesuan_status');
        }
        if (array_key_exists('creator_type', $data)) {
            $info->creator_type = array_get($data, 'creator_type');
        }
        if (array_key_exists('creator_id', $data)) {
            $info->creator_id = array_get($data, 'creator_id');
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
        $infos = new MryhGame();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', "%" . $con_arr['search_word'] . "%");
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $infos = $infos->where('type', '=', $con_arr['type']);
        }
        if (array_key_exists('show_status', $con_arr) && !Utils::isObjNull($con_arr['show_status'])) {
            $infos = $infos->where('show_status', '=', $con_arr['show_status']);
        }
        if (array_key_exists('game_status', $con_arr) && !Utils::isObjNull($con_arr['game_status'])) {
            $infos = $infos->where('game_status', '=', $con_arr['game_status']);
        }
        if (array_key_exists('game_status_arr', $con_arr) && !Utils::isObjNull($con_arr['game_status_arr'])) {
            $infos = $infos->wherein('game_status', $con_arr['game_status_arr']);
        }
        if (array_key_exists('join_status', $con_arr) && !Utils::isObjNull($con_arr['join_status'])) {
            $infos = $infos->where('join_status', '=', $con_arr['join_status']);
        }
        if (array_key_exists('creator_type', $con_arr) && !Utils::isObjNull($con_arr['creator_type'])) {
            $infos = $infos->where('creator_type', '=', $con_arr['creator_type']);
        }
        if (array_key_exists('creator_id', $con_arr) && !Utils::isObjNull($con_arr['creator_id'])) {
            $infos = $infos->where('creator_id', '=', $con_arr['creator_id']);
        }
        if (array_key_exists('id', $con_arr) && !Utils::isObjNull($con_arr['id'])) {
            $infos = $infos->where('id', '=', $con_arr['id']);
        }
        if (array_key_exists('ids_arr', $con_arr) && !Utils::isObjNull($con_arr['ids_arr'])) {
            $infos = $infos->wherein('id', $con_arr['ids_arr']);
        }
        if (array_key_exists('no_ids_arr', $con_arr) && !Utils::isObjNull($con_arr['no_ids_arr'])) {
            $infos = $infos->wherenotin('id', $con_arr['no_ids_arr']);
        }

        $infos = $infos->orderby('seq', 'desc')->orderby('id', 'desc');

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
    public static function addStatistics($mryhGame_id, $item, $num)
    {
        $mryhGame = MryhGameManager::getById($mryhGame_id);
        switch ($item) {
            case "complain_num":
                $mryhGame->complain_num = $mryhGame->complain_num + $num;
                break;
            case "join_num":
                $mryhGame->join_num = $mryhGame->join_num + $num;
                break;
            case "work_num":
                $mryhGame->work_num = $mryhGame->work_num + $num;
                break;
            case "show_num":
                $mryhGame->show_num = $mryhGame->show_num + $num;
                break;
            case "total_money":
                $mryhGame->total_money = $mryhGame->total_money + $num;
                break;
            case "success_num":
                $mryhGame->success_num = $mryhGame->success_num + $num;
                break;
            case "fail_num":
                $mryhGame->fail_num = $mryhGame->fail_num + $num;
                break;
        }
        $mryhGame->save();
        return $mryhGame;
    }


    /*
     * 计划任务，设置活动状态-展示状态、参与状态和活动状态
     *
     * By TerryQi
     *
     * 2018-08-20
     */
    public static function gameSchedule()
    {
        $mryhGames = self::getListByCon(['status' => '1', 'game_status_arr' => ['0', '1']], false);
        foreach ($mryhGames as $mryhGame) {
            //展示状态设置///////////////////////////////////////////////////////////
            Utils::processLog(__METHOD__, '', "game:" . json_encode($mryhGame));
            //当前时间小于开始时间或者当前时间大于结束时间
            if (DateTool::dateDiff('N', $mryhGame->show_start_time, DateTool::getCurrentTime()) < 0
                || DateTool::dateDiff('N', DateTool::dateAdd('D', '1', $mryhGame->show_end_time), DateTool::getCurrentTime()) > 0) {
                $mryhGame->show_status = '0';  //不展示
                Utils::processLog(__METHOD__, '', "不展示活动 " . "game:" . $mryhGame->name . " id:" . $mryhGame->id);
            } else {
                $mryhGame->show_status = '1';  //展示
                Utils::processLog(__METHOD__, '', " 展示活动" . "game:" . $mryhGame->name . " id:" . $mryhGame->id);
            }

            //参与状态设置
            //当前时间小于参与开始时间 or 当前时间大于结束时间-参与未开始
            if (DateTool::dateDiff('N', $mryhGame->join_start_time, DateTool::getCurrentTime()) < 0
                || DateTool::dateDiff('N', DateTool::dateAdd('D', '1', $mryhGame->join_end_time), DateTool::getCurrentTime()) > 0) {
                $mryhGame->join_status = '0';  //不可以参加
                Utils::processLog(__METHOD__, '', "活动不可以参与 " . "game:" . $mryhGame->name . " id:" . $mryhGame->id);
            } else {
                $mryhGame->join_status = '1';
                Utils::processLog(__METHOD__, '', "活动可以参与 " . "game:" . $mryhGame->name . " id:" . $mryhGame->id);
            }

            //活动状态
            //当前时间小于参与开始时间-参与未开始
            if (DateTool::dateDiff('N', $mryhGame->game_start_time, DateTool::getCurrentTime()) < 0) {
                $mryhGame->game_status = '0';  //活动未开始
                Utils::processLog(__METHOD__, '', "活动未开始 " . "game:" . $mryhGame->name . " id:" . $mryhGame->id);
            }
            //当前时间大于活动开始时间、小于活动结束时间-活动进行中
            if (DateTool::dateDiff('N', $mryhGame->game_start_time, DateTool::getCurrentTime()) >= 0
                && DateTool::dateDiff('N', DateTool::dateAdd('D', 1, $mryhGame->game_end_time), DateTool::getCurrentTime()) < 0) {
                $mryhGame->game_status = '1';  //活动进行中
                Utils::processLog(__METHOD__, '', "活动进行中 " . "game:" . $mryhGame->name . " id:" . $mryhGame->id);
            }
            //当前时间大于活动结束时间-活动已结束
            if (DateTool::dateDiff('N', DateTool::dateAdd('D', 1, $mryhGame->game_end_time), DateTool::getCurrentTime()) >= 0) {
                $mryhGame->game_status = '2';  //活动已经结束
                Utils::processLog(__METHOD__, '', "活动已经结束 " . "game:" . $mryhGame->name . " id:" . $mryhGame->id);
                //对于已经结束的活动，添加至清分任务
                $mryhComputePrize = new MryhComputePrize();
                $mryhComputePrize->game_id = $mryhGame->id;
                $mryhComputePrize->save();
            }
            $mryhGame->save();
        }
    }

    /*
     * 活动清分调度任务
     *
     * By TerryQi
     *
     * 2018-08-23
     */
    public static function computePrizeSchedule()
    {
        $con_arr = array(
            'compute_status' => '0'
        );
        $mryhComputePrizes = MryhComputePrizeManager::getListByCon($con_arr, false);
        foreach ($mryhComputePrizes as $mryhComputePrize) {
            self::computePrize($mryhComputePrize->id);
        }
    }


    //活动结束后，进行资金清分
    /*
     * 活动如果结束，则进行活动资金的清分
     *
     * By TerryQi
     *
     * 2018-08-20
     */
    public static function computePrize($mryhComputePrize_id)
    {
        //获取清分任务
        $mryhComputePrize = MryhComputePrizeManager::getById($mryhComputePrize_id);
        //清分任务立刻设置为已清分状态
        $mryhComputePrize->compute_status = '1';
        $mryhComputePrize->save();
        $mryhGame = MryhGameManager::getById($mryhComputePrize->game_id);
        Utils::processLog(__METHOD__, '', "活动结束，进行活动清分 " . "game:" . json_encode($mryhGame));
        //如果不存在活动或者活动已经被清分过，则不进行活动清分
        if (!$mryhGame || $mryhGame->jiesuan_status == '1') {
            return false;
        }
        //记录总体参与活动
        $con_arr = array(
            'game_id' => $mryhGame->id,
        );
        $mryhGame->join_num = MryhJoinManager::getListByCon($con_arr, false)->count();      //记录总数

        //进行活动清分
        $con_arr = array(
            'game_id' => $mryhGame->id,
            'game_status' => '1',
        );
        $success_mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        $mryhComputePrize->success_num = $success_mryhJoins->count();       //记录数据
        $mryhGame->success_num = $success_mryhJoins->count();       //记录数据
        Utils::processLog(__METHOD__, '', "活动结束，成功参赛记录 数量：" . $success_mryhJoins->count() . "  success_mryhJoins:" . json_encode($success_mryhJoins));
        $con_arr = array(
            'game_id' => $mryhGame->id,
            'game_status' => '2',
        );
        $fail_mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        $mryhComputePrize->fail_num = $fail_mryhJoins->count();       //记录数据
        $mryhGame->fail_num = $fail_mryhJoins->count();       //记录数据
        Utils::processLog(__METHOD__, '', "活动结束，失败参赛记录 数量：" . $fail_mryhJoins->count() . "  fail_mryhJoins:" . json_encode($fail_mryhJoins));
        //计算失败人数，获取总的清分金额
        $fail_total_money = 0;
        foreach ($fail_mryhJoins as $fail_mryhJoin) {
            $fail_total_money += $fail_mryhJoin->total_fee;
            Utils::processLog(__METHOD__, '', "失败金额累计数量：" . $fail_total_money . " fail_mryhJoin:" . json_encode($fail_mryhJoin));
        }
        $mryhComputePrize->save();      //保存信息
        $mryhGame->save();          //保存信息
        //成功人数不为0
        if ($success_mryhJoins->count() > 0) {
            //平均分润金额
            Utils::processLog(__METHOD__, '', "计算分润金额 faid_total_money:" . $fail_total_money . " adv_prize:" . $mryhGame->adv_price);
            $ave_prize = round(((double)($fail_total_money + $mryhGame->adv_price) / $success_mryhJoins->count()), 2);
            Utils::processLog(__METHOD__, '', "活动结束，平均分润金额 " . "ave_prize:" . $ave_prize);
            $mryhComputePrize->ave_prize = $ave_prize;
            $mryhComputePrize->save();
            //进行分润
            foreach ($success_mryhJoins as $success_mryhJoin) {
                //如果未清分
                if ($success_mryhJoin->clear_status == '0') {
                    $success_mryhJoin->jiesuan_price = $ave_prize;  //配置结算金额
                    $success_mryhJoin->clear_status = '1';      //2018-12-11 此处注意，在最后将会把全部的清分记录都设置为已清分，但此处仍然重新记录一下
                    $success_mryhJoin->save();
                    Utils::processLog(__METHOD__, '', "成功参赛最终记录 " . "success_mryhJoin:" . json_encode($success_mryhJoin));
                    MryhComputePrizeManager::addStatistics($mryhComputePrize->id, 'compute_num', 1);
                }
            }
        }
        //完成清分后，活动设置为已经结算
        $mryhGame->jiesuan_status = '1';
        $mryhGame->save();
        //将活动参与记录都设置为已经清分
        /*
         * 设置清分时间
         *
         * 2018-12-10 设置一下清分时间
         *
         */
        $con_arr = array(
            'game_id' => $mryhGame->id
        );
        $mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        foreach ($mryhJoins as $mryhJoin) {
            $mryhJoin->clear_status = '1';      //已清分
            $mryhJoin->clear_time = DateTool::getCurrentTime();
            $mryhJoin->save();
        }

        return true;
    }


    /*
     * 获取推荐活动
     *
     * By TerryQi
     *
     * 2018-12-05
     *
     * @para
     * user_id：如果传入user_id代表获取的活动不在用户已参与的活动中
     * no_id_arr：可以提出的活动信息，为数组形式，即如果不推荐某些活动，可以将id放置在该数组中，例如不推荐当前的活动
     * num：获取活动数目，默认num=5
     *
     * @return 根据num获取返回活动数量
     */
    public static function getRecommendGames($user_id, $no_id_arr, $num = 5)
    {
        $con_arr = array(
            'user_id' => $user_id
        );
        $already_join_mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        //补充数据-game_id
        foreach ($already_join_mryhJoins as $already_join_mryhJoin) {
            array_push($no_id_arr, $already_join_mryhJoin->game_id);
        }

        $con_arr = array(
            'join_status' => '1',
            'game_status' => '1',
            'no_ids_arr' => $no_id_arr,
            'page_size' => $num
        );
        $other_mryhGames = self::getListByCon($con_arr, true);
        foreach ($other_mryhGames as $other_mryhGame) {
            $other_mryhGame = self::getInfoByLevel($other_mryhGame, '');
            unset($other_mryhGame->intro_html);
        }
        return $other_mryhGames;
    }

}