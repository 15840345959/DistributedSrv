<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\GZH;

use App\Models\GZH\BusiWord;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class BusiWordManager
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
        $info = BusiWord::where('id', '=', $id)->first();
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
        if (isset($info->busi_name)) {
            $info->busi_name_str = Utils::BUSI_NAME_VAL[$info->busi_name];
        }
        if (isset($info->type)) {
            $info->type_str = Utils::MESSAGE_TYPE_VAL[$info->type];
        }

        $info->admin = AdminManager::getById($info->admin_id);

        return $info;
    }


    /*
     * 设置业务话术信息，用于编辑
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('busi_name', $data)) {
            $info->busi_name = array_get($data, 'busi_name');
        }
        if (array_key_exists('template_id', $data)) {
            $info->template_id = array_get($data, 'template_id');
        }
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('content', $data)) {
            $info->content = array_get($data, 'content');
        }
        if (array_key_exists('media_id', $data)) {
            $info->media_id = array_get($data, 'media_id');
        }
        if (array_key_exists('thumb_media_id', $data)) {
            $info->thumb_media_id = array_get($data, 'thumb_media_id');
        }
        if (array_key_exists('title', $data)) {
            $info->title = array_get($data, 'title');
        }
        if (array_key_exists('description', $data)) {
            $info->description = array_get($data, 'description');
        }
        if (array_key_exists('image', $data)) {
            $info->image = array_get($data, 'image');
        }
        if (array_key_exists('url', $data)) {
            $info->url = array_get($data, 'url');
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
     * 获取业务话术列表
     *
     * By Amy
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new BusiWord();
        //相关条件
        if (array_key_exists('type', $con_arr) && !Utils::isObjNull($con_arr['type'])) {
            $infos = $infos->where('type', '=', $con_arr['type']);
        }
        if (array_key_exists('busi_name', $con_arr) && !Utils::isObjNull($con_arr['busi_name'])) {
            $infos = $infos->where('busi_name', '=', $con_arr['busi_name']);
        }
        if (array_key_exists('template_id', $con_arr) && !Utils::isObjNull($con_arr['template_id'])) {
            $infos = $infos->where('template_id', '=', $con_arr['template_id']);
        }

        $infos = $infos->orderby('seq', 'desc');
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


    /*
     * 根据业务话术发送消息
     *
     * By TerryQi
     *
     * 2018-07-06
     */
    /*
     * 根据回复内容生成消息
     *
     * By TerryQi
     *
     * 2018-07-06
     *
     */
    public static function setWechatMessage($busiWord, $user)
    {
        Utils::processLog(__METHOD__, '', " " . "busiWord:" . json_encode($busiWord));
        Utils::processLog(__METHOD__, '', " " . "user:" . json_encode($user));
        switch ($busiWord->type) {
            case "text":        //文本消息
                $content = $busiWord->content;
                $content = str_replace("{user_name}", $user->nick_name, $content);
                $text = new Text($content);
                return $text;
                break;
            case "image":       //图片消息
                $image = new Image($busiWord->media_id);
                return $image;
                break;
            case "voice":       //语音消息
                $voice = new Voice($busiWord->media_id);
                return $voice;
                break;
            case "video":   //视频消息
                $video = new Video($busiWord->media_id, [
                    'title' => $busiWord->title,
                    'description' => $busiWord->description,
                    'thumb_media_id' => $busiWord->thumb_media_id
                ]);
                return $video;
                break;
            case "news":        //图文消息
                $newsItem = new NewsItem([
                    'title' => $busiWord->title,
                    'description' => $busiWord->description,
                    'url' => $busiWord->url,
                    'image' => $busiWord->image
                ]);
                $news = new News([$newsItem]);
                return $news;
                break;
        }
        return "";
    }
}