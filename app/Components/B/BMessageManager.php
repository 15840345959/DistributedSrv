<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\B;

use App\Models\B\Message;
use App\Models\Rule;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class BMessageManager
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
        $info = Message::where('id', '=', $id)->first();
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

        $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];
        $info->type_str = Utils::TO_B_MESSAGE_TYPE[$info->type];
        $info->level_str = Utils::TO_B_MESSAGE_LEVEL[$info->level];

        if ($info->admin_id) {
            $info->admin = AdminManager::getById($info->admin_id);
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
        if (array_key_exists('title', $data)) {
            $info->title = array_get($data, 'title');
        }
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('position', $data)) {
            $info->position = array_get($data, 'position');
        }
        if (array_key_exists('content_html', $data)) {
            $info->content_html = array_get($data, 'content_html');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('pos', $data)) {
            $info->pos = array_get($data, 'pos');
        }
        if (array_key_exists('level', $data)) {
            $info->level = array_get($data, 'level');
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
     * 获取列表
     *
     * By Amy
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new Message();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('title', 'like', "%" . $con_arr['search_word'] . "%");
        }
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $infos = $infos->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $infos = $infos->where('type', '=', $con_arr['type']);
        }
        if (array_key_exists('pos', $con_arr) && !Utils::isObjNull($con_arr['pos'])) {
            $infos = $infos->where('pos', '=', $con_arr['pos']);
        }
        if (array_key_exists('top', $con_arr) && !Utils::isObjNull($con_arr['top'])) {
            $infos = $infos->where('top', '=', $con_arr['top']);
        }
        if (array_key_exists('level', $con_arr) && !Utils::isObjNull($con_arr['level'])) {
            $infos = $infos->where('level', '=', $con_arr['level']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }

        $infos = $infos->orderby('seq', 'desc')->orderby('id', 'desc');

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


}