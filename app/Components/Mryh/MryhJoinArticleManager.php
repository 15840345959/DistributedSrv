<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\ArticleManager;
use App\Components\DateTool;
use App\Components\UserManager;
use App\Models\Mryh\MryhJoinArticle;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhJoinArticleManager
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
        $info = MryhJoinArticle::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0:带参赛信息 1：带用户信息 2：带大赛信息 3：带作品基本信息
     */
    public static function getInfoByLevel($info, $level)
    {
        if (strpos($level, '0') !== false) {
            $join = MryhJoinManager::getById($info->join_id);
            $join = MryhJoinManager::getInfoByLevel($join, '');
            $info->join = $join;
        }
        if (strpos($level, '1') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        if (strpos($level, '2') !== false) {
            $game = MryhGameManager::getById($info->game_id);
            $game = MryhGameManager::getInfoByLevel($game, '');
            $info->game = $game;
        }
        if (strpos($level, '3') !== false) {
            $info->article = ArticleManager::getById($info->article_id);
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
        if (array_key_exists('join_id', $data)) {
            $info->join_id = array_get($data, 'join_id');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('game_id', $data)) {
            $info->game_id = array_get($data, 'game_id');
        }
        if (array_key_exists('article_id', $data)) {
            $info->article_id = array_get($data, 'article_id');
        }
        if (array_key_exists('date_at', $data)) {
            $info->date_at = array_get($data, 'date_at');
        }
        if (array_key_exists('status', $data)) {
            $info->status = array_get($data, 'status');
        }
        return $info;
    }

    /*
     * 获取列表
     *
     * By Amy
     *
     * 2018-05-10
     *
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new MryhJoinArticle();
        //相关条件
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('join_id', $con_arr) && !Utils::isObjNull($con_arr['join_id'])) {
            $infos = $infos->where('join_id', '=', $con_arr['join_id']);
        }
        if (array_key_exists('game_id', $con_arr) && !Utils::isObjNull($con_arr['game_id'])) {
            $infos = $infos->where('game_id', '=', $con_arr['game_id']);
        }
        if (array_key_exists('article_id', $con_arr) && !Utils::isObjNull($con_arr['article_id'])) {
            $infos = $infos->where('article_id', '=', $con_arr['article_id']);
        }
        if (array_key_exists('date_at', $con_arr) && !Utils::isObjNull($con_arr['date_at'])) {
            $infos = $infos->where('created_at', '>=', $con_arr['date_at'])
                ->where('created_at', '<', DateTool::dateAdd('D', 1, $con_arr['date_at']));
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        $infos = $infos->orderby('id', 'desc');

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
     * 某一用户在某一天的活动下是否上传了作品
     *
     * By TerryQi
     *
     * 2018-11-18
     *
     * @user_id，用户id
     * @join_id，参赛id
     * @date_at，参赛天数
     *
     */
    public static function isUploadAtDate($user_id, $join_id, $date_at)
    {
        $con_arr = array(
            'user_id' => $user_id,
            'join_id' => $join_id,
            'date_at' => $date_at
        );
        $mryhJoinArticles_num = self::getListByCon($con_arr, false)->count();
        if ($mryhJoinArticles_num == 0) {
            return false;
        } else {
            return true;
        }
    }

}