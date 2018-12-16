<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 19:49
 */

namespace App\Libs;

use Illuminate\Http\Request;


class CommonUtils
{
    const PAGE_SIZE = 25;       //分页大小

    //七牛相关

    const size_w_500_h_200 = '?imageView2/2/w/500/h/200/interlace/1/q/75|imageslim';
    const size_w_200_h_200 = '?imageView2/2/w/200/h/200/interlace/1/q/75|imageslim';
    const size_w_500_h_300 = '?imageView2/2/w/500/h/300/interlace/1/q/75|imageslim';
    const size_w_500_h_250 = '?imageView2/2/w/500/h/250/interlace/1/q/75|imageslim';

    const size_w_500 = '?imageView1/1/w/500/interlace/1/q/75';


    public static function qiniuUrlTool($img_url, $type)
    {
        //如果不是七牛的头像，则直接返回图片
        //consoledebug.log("$img_url:" + $img_url + " indexOf('isart.me'):" + $img_url.indexOf('isart.me'));
        if (!(strpos($img_url, '7xku37.com') || strpos($img_url, 'isart.me'))) {
            return $img_url;
        }
        //七牛链接
        $qn_img_url = '';

        //除去参数
        if (strpos($img_url, '?') >= 0) {
            $img_url = explode('?', $img_url)[0];
        }
        //封装七牛链接
        switch ($type) {
            case "ad":  //广告图片
                $qn_img_url = $img_url . self::size_w_500_h_300;
                break;
            case "folder_list":  //作品列表图片样式
                $qn_img_url = $img_url . self::size_w_500_h_200;
                break;
            case  'head_icon':      //头像信息
                $qn_img_url = $img_url . self::size_w_200_h_200;
                break;
            case  'work_detail':      //作品详情的图片信息
                $qn_img_url = $img_url . self::size_w_500;
                break;
            default:
                $qn_img_url = $img_url;
                break;
        }
        return $qn_img_url;
    }
}