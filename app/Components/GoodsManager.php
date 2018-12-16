<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Components;


use App\Components\Utils;
use App\Models\Goods;

class GoodsManager
{

    /*
     * 根据id获取轮播图信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getById($id)
    {
        $info = Goods::where('id', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-06-14
     *
     * 0:带类型信息  4：带小编信息 5：带管理员信息
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->status_str = Utils::GOODS_STATUS_VAL[$info->status];
        $info->recomm_flag_str = Utils::GOODS_RECOMM_FLAG_VAL[$info->recomm_flag];

        //0 带类型信息
        if (strpos($level, '0') !== false) {

            $info->goods_type = GoodsTypeManager::getById($info->goods_type_id);
        }

        //1带点赞明细
        if (strpos($level, '1') !== false) {
            $con_arr = array(
                'f_id' => $info->id,
                'f_table' => 'goods',
            );
            $zans = ZanManager::getListByCon($con_arr, true);
            foreach ($zans as $zan) {
                $zan = ZanManager::getInfoByLevel($zan, 0);
            }
            $info->zans = $zans;
        }
        //2带收藏明细
        if (strpos($level, '2') !== false) {
            $con_arr = array(
                'f_id' => $info->id,
                'f_table' => 'goods',
            );
            $collects = CollectManager::getListByCon($con_arr, true);
            foreach ($collects as $collect) {
                $collect = ZanManager::getInfoByLevel($collect, 0);
            }
            $info->collects = $collects;
        }
        //3带评论信息
        if (strpos($level, '3') !== false) {
            $con_arr = array(
                'f_id' => $info->id,
                'f_table' => 'goods',
            );
            $comments = CommentManager::getListByCon($con_arr, true);
            foreach ($comments as $comment) {
                $comment = CommentManager::getInfoByLevel($comment, 0);
            }
            $info->comments = $comments;
        }

        //4 带小编信息
        if (strpos($level, '4') !== false) {
            $info->editor = UserManager::getById($info->editor_id);
        }

        //5 带管理员信息
        if (strpos($level, '5') !== false) {
            $info->admin = AdminManager::getById($info->admin_id);
        }
        return $info;
    }


    /*
     * 根据条件获取信息
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new Goods();
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $infos = $infos->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('recomm_flag', $con_arr) && !Utils::isObjNull($con_arr['recomm_flag'])) {
            $infos = $infos->where('recomm_flag', '=', $con_arr['recomm_flag']);
        }

        $infos = $infos->orderby('seq', 'desc')->orderby('id', 'desc');

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

    /*
     * 配置信息
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('desc', $data)) {
            $info->desc = array_get($data, 'desc');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('video', $data)) {
            $info->video = array_get($data, 'video');
        }
        if (array_key_exists('content_html', $data)) {
            $info->content_html = array_get($data, 'content_html');
        }
        if (array_key_exists('editor_id', $data)) {
            $info->editor_id = array_get($data, 'editor_id');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        if (array_key_exists('show_price', $data)) {
            $info->show_price = array_get($data, 'show_price');
        }
        if (array_key_exists('price', $data)) {
            $info->price = array_get($data, 'price');
        }
        if (array_key_exists('show_num', $data)) {
            $info->show_num = array_get($data, 'show_num');
        }
        if (array_key_exists('comm_num', $data)) {
            $info->comm_num = array_get($data, 'comm_num');
        }
        if (array_key_exists('zan_num', $data)) {
            $info->zan_num = array_get($data, 'zan_num');
        }
        if (array_key_exists('coll_num', $data)) {
            $info->coll_num = array_get($data, 'coll_num');
        }
        if (array_key_exists('trans_num', $data)) {
            $info->trans_num = array_get($data, 'trans_num');
        }
        if (array_key_exists('sale_num', $data)) {
            $info->sale_num = array_get($data, 'sale_num');
        }
        if (array_key_exists('left_num', $data)) {
            $info->left_num = array_get($data, 'left_num');
        }
        if (array_key_exists('total_num', $data)) {
            $info->total_num = array_get($data, 'total_num');
        }
        if (array_key_exists('goods_type_id', $data)) {
            $info->goods_type_id = array_get($data, 'goods_type_id');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
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
        if (array_key_exists('admin_id', $data)) {
            $info->admin_id = array_get($data, 'admin_id');
        }
        return $info;
    }

    /*
     * 增加数据
     *
     * By TerryQi
     *
     * 2018-07-18
     *
     * 增加统计数据
     */
    public static function addStatistics($goods_id, $item, $num)
    {
        $goods = self::getById($goods_id);
        switch ($item) {
            case "sale_num":
                $goods->sale_num = $goods->sale_num + $num;
                break;
            case "show_num":
                $goods->show_num = $goods->show_num + $num;
                break;
            case "comm_num":
                $goods->comm_num = $goods->comm_num + $num;
                break;
            case "zan_num":
                $goods->zan_num = $goods->zan_num + $num;
                break;
            case "coll_num":
                $goods->coll_num = $goods->coll_num + $num;
                break;
            case "trans_num":
                $goods->trans_num = $goods->trans_num + $num;
                break;
        }
        $goods->save();
    }

    /*
     * 减少数据
     *
     * By TerryQi
     *
     * 2018-07-18
     *
     * 减少统计数据
     */
    public static function minusStatistics($goods_id, $item, $num)
    {
        $goods = self::getById($goods_id);
        switch ($item) {
            case "left_num":
                $goods->left_num = $goods->left_num - $num;
                break;
        }
        $goods->save();
    }

}