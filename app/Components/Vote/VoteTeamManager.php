<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Models\Vote\VoteTeam;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class VoteTeamManager
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
        $info = VoteTeam::where('id', '=', $id)->first();
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

        $info->status_str = Utils::VOTE_TEAM_STATUS_VAL[$info->status];
        $info->admin = AdminManager::getById($info->admin_id);

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
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('province', $data)) {
            $info->province = array_get($data, 'province');
        }
        if (array_key_exists('city', $data)) {
            $info->city = array_get($data, 'city');
        }
        if (array_key_exists('contact', $data)) {
            $info->contact = array_get($data, 'contact');
        }
        if (array_key_exists('email', $data)) {
            $info->email = array_get($data, 'email');
        }
        if (array_key_exists('phonenum', $data)) {
            $info->phonenum = array_get($data, 'phonenum');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
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
        if (array_key_exists('amount', $data)) {
            $info->amount = array_get($data, 'amount');
        }
        if (array_key_exists('password', $data)) {
            $info->password = array_get($data, 'password');
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
        $infos = new VoteTeam();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('name', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('contact', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('phonenum', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('province', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('city', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('id', $con_arr) && !Utils::isObjNull($con_arr['id'])) {
            $infos = $infos->where('id', '=', $con_arr['id']);
        }
        if (array_key_exists('phonenum', $con_arr) && !Utils::isObjNull($con_arr['phonenum'])) {
            $infos = $infos->where('phonenum', '=', $con_arr['phonenum']);
        }
        if (array_key_exists('password', $con_arr) && !Utils::isObjNull($con_arr['password'])) {
            $infos = $infos->where('password', '=', $con_arr['password']);
        }


        $infos = $infos->orderby('id', 'desc');

        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


}