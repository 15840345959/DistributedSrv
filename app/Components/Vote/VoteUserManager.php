<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Vote;

use App\Components\UserManager;
use App\Models\Vote\VoteUser;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class VoteUserManager
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
        $info = VoteUser::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带用户信息 1：带活动信息 2：带当前排名信息
     *
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->type_str = Utils::VOTE_USER_TYPE_VAL[$info->type];
        $info->audit_status_str = Utils::VOTE_USER_AUDIT_STATUS_VAL[$info->audit_status];
        $info->valid_status_str = Utils::VOTE_USER_VALID_STATUS_VAL[$info->valid_status];
        $info->status_str = Utils::VOTE_USER_STATUS_VAL[$info->status];

        if ($info->admin_id != 0) {
            $info->admin = AdminManager::getById($info->admin_id);
        }

        //此处增加逻辑-或者有图片，或者有视频
        /*
         * By TerryQi
         *
         * 2018-08-25，根据运营组的要求，增加视频的作品，那么img_arr来自于视频的截取帧数
         */
        $info->img_arr = [];        //初始化
        //如果图片不为空
        if (!Utils::isObjNull($info->img)) {
            $info->img_arr = explode(",", $info->img);
        }

        //审核状态
        if ($info->audit_status) {
            $info->audit_status_str = Utils::VOTE_USER_AUDIT_STATUS_VAL[$info->audit_status];
        }
        if (strpos($level, '0') !== false) {
            $info->user = UserManager::getById($info->user_id);
        }
        if (strpos($level, '1') !== false) {
            $info->activity = VoteActivityManager::getById($info->activity_id);
        }
        if (strpos($level, '2') !== false) {
            $con_arr = array(
                'activity_id' => $info->activity_id,
                'bigger_than_vote_num' => $info->vote_num
            );
            $front_vote_users = self::getListByCon($con_arr, false);
            $info->pm = $front_vote_users->count() + 1;
        }
        return $info;
    }


    /*
     * 设置投票用户信息，用于编辑
     *
     * By Amy
     *
     * 2018-05-09
     */
    public static function setInfo($info, $data)
    {
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('activity_id', $data)) {
            $info->activity_id = array_get($data, 'activity_id');
        }
        if (array_key_exists('code', $data)) {
            $info->code = array_get($data, 'code');
        }
        if (array_key_exists('yes_pm', $data)) {
            $info->yes_pm = array_get($data, 'yes_pm');
        }
        if (array_key_exists('name', $data)) {
            $info->name = array_get($data, 'name');
        }
        if (array_key_exists('declaration', $data)) {
            $info->declaration = array_get($data, 'declaration');
        }
        if (array_key_exists('phonenum', $data)) {
            $info->phonenum = array_get($data, 'phonenum');
        }
        if (array_key_exists('work_name', $data)) {
            $info->work_name = array_get($data, 'work_name');
        }
        if (array_key_exists('work_desc', $data)) {
            $info->work_desc = array_get($data, 'work_desc');
        }
        if (array_key_exists('img', $data)) {
            $info->img = array_get($data, 'img');
        }
        if (array_key_exists('video', $data)) {
            $info->video = array_get($data, 'video');
            /*
             * 此处由TerryQi进行优化
             *
             * 2018-08-25，增加视频类作品，其中，如果上传视频且没有img，则从视频中截图，然后补充至img中
             *
             */
            if (Utils::isObjNull($info->img)) {
                $video_url = $info->video;
                $video_info = Utils::curl($video_url . '?avinfo', false);
                Utils::processLog(__METHOD__, '', $video_info);
                $duration = 20;
                if (!Utils::isObjNull(json_decode($video_info)->format)) {
                    $duration = intval(json_decode($video_info)->format->duration);     //视频时长
                }
                Utils::processLog(__METHOD__, '', $duration);
                $video_frame_num = 4;       //随机获取帧数
                $img_arr = [];
                for ($i = 0; $i < $video_frame_num; $i++) {
                    if ($i == 0) {
                        array_push($img_arr, $info->video . "?vframe/png/offset/" . Utils::getRandInRang(1, $duration));
                    } else {
                        array_push($img_arr, $info->video . "?vframe/png/offset/" . Utils::getRandInRang(1, $duration));
                    }
                    $info->img = implode(',', $img_arr);
                }
            }
        }
        if (array_key_exists('lock_status', $data)) {
            $info->lock_status = array_get($data, 'lock_status');
        }
        if (array_key_exists('lock_to', $data)) {
            $info->lock_to = array_get($data, 'lock_to');
        }
        if (array_key_exists('vote_num', $data)) {
            $info->vote_num = array_get($data, 'vote_num');
        }
        if (array_key_exists('gift_money', $data)) {
            $info->gift_money = array_get($data, 'gift_money');
        }
        if (array_key_exists('show_num', $data)) {
            $info->show_num = array_get($data, 'show_num');
        }
        if (array_key_exists('share_num', $data)) {
            $info->share_num = array_get($data, 'share_num');
        }
        if (array_key_exists('fans_num', $data)) {
            $info->fans_num = array_get($data, 'fans_num');
        }
        if (array_key_exists('type', $data)) {
            $info->type = array_get($data, 'type');
        }
        if (array_key_exists('seq', $data)) {
            $info->seq = array_get($data, 'seq');
        }
        if (array_key_exists('audit_status', $data)) {
            $info->audit_status = array_get($data, 'audit_status');
        }
        if (array_key_exists('valid_status', $data)) {
            $info->valid_status = array_get($data, 'valid_status');
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
     * 获取投票用户列表
     *
     * By Amy
     *
     * 2018-05-10
     *
     * 如果con_arr存在orderby参数，则需要通过orderby进行排序
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new VoteUser();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $keyword = $con_arr['search_word'];
            $infos = $infos->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orwhere('phonenum', 'like', "%{$keyword}%")
                    ->orwhere('code', 'like', "%{$keyword}%");
            });
        }
        if (array_key_exists('search_word_by_code_or_name', $con_arr) && !Utils::isObjNull($con_arr['search_word_by_code_or_name'])) {
            $keyword = $con_arr['search_word_by_code_or_name'];
            $infos = $infos->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orwhere('code', 'like', "%{$keyword}%");
            });
        }
        if (array_key_exists('name', $con_arr) && !Utils::isObjNull($con_arr['name'])) {
            $infos = $infos->where('name', '=', $con_arr['name']);
        }
        if (array_key_exists('activity_id', $con_arr) && !Utils::isObjNull($con_arr['activity_id'])) {
            $infos = $infos->where('activity_id', '=', $con_arr['activity_id']);
        }
        if (array_key_exists('where_in_activity_id', $con_arr) && !Utils::isObjNull($con_arr['where_in_activity_id'])) {
            $infos = $infos->wherein('activity_id', $con_arr['where_in_activity_id']);
        }
        //此处由TerryQi优化，与where_in_activity_id一致的功能，但参数上统一管理为id_arr
        if (array_key_exists('id_arr', $con_arr) && !Utils::isObjNull($con_arr['id_arr'])) {
            $infos = $infos->wherein('activity_id', $con_arr['id_arr']);
        }
        if (array_key_exists('code', $con_arr) && !Utils::isObjNull($con_arr['code'])) {
            $infos = $infos->where('code', '=', $con_arr['code']);
        }
        if (array_key_exists('vote_user_id', $con_arr) && !Utils::isObjNull($con_arr['vote_user_id'])) {
            $infos = $infos->where('id', '=', $con_arr['vote_user_id']);
        }
        if (array_key_exists('bigger_than_vote_num', $con_arr) && !Utils::isObjNull($con_arr['bigger_than_vote_num'])) {
//            dd($con_arr['bigger_than_vote_num']);
            $infos = $infos->where('vote_num', '>', $con_arr['bigger_than_vote_num']);
        }
        if (array_key_exists('audit_status', $con_arr) && !Utils::isObjNull($con_arr['audit_status'])) {
            $infos = $infos->where('audit_status', '=', $con_arr['audit_status']);
        }
        if (array_key_exists('status', $con_arr) && !Utils::isObjNull($con_arr['status'])) {
            $infos = $infos->where('status', '=', $con_arr['status']);
        }
        if (array_key_exists('valid_status', $con_arr) && !Utils::isObjNull($con_arr['valid_status'])) {
            $infos = $infos->where('valid_status', '=', $con_arr['valid_status']);
        }
        //排序设定
        if (array_key_exists('orderby', $con_arr) && is_array($con_arr['orderby'])) {
            $orderby_arr = $con_arr['orderby'];
            if (array_key_exists('vote_num', $orderby_arr) && !Utils::isObjNull($orderby_arr['vote_num'])) {
                $infos = $infos->orderby('vote_num', $orderby_arr['vote_num']);
            }
            if (array_key_exists('show_num', $orderby_arr) && !Utils::isObjNull($orderby_arr['show_num'])) {
                $infos = $infos->orderby('show_num', $orderby_arr['show_num']);
            }
            if (array_key_exists('created_at', $orderby_arr) && !Utils::isObjNull($orderby_arr['created_at'])) {
                $infos = $infos->orderby('created_at', $orderby_arr['created_at']);
            }
        } else {
            $infos = $infos->orderby('id', 'desc');
        }
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

    //设置参赛选手的编号
    public static function setCode($vote_user)
    {
        //如果没有用户参赛编号
        if (!$vote_user->code || $vote_user->code == 0) {
            $con_arr = array(
                'activity_id' => $vote_user->activity_id
            );
            $vote_user_num = self::getListByCon($con_arr, false)->count();
            $vote_user->code = $vote_user_num;
            $vote_user->save();
        }
    }

    /*
     * 设置大赛选手排名
     *
     * By TerryQi
     *
     * 2018-07-19
     */
    public static function setPM($activity_id)
    {
        $con_arr = array(
            'activity_id' => $activity_id,
            'orderby' => [
                'vote_num' => 'desc'
            ]
        );
        $vote_users = VoteUserManager::getListByCon($con_arr, false);
        $i = 0;
        foreach ($vote_users as $vote_user) {
            $vote_user->yes_pm = ++$i;      //设定排名
            $vote_user->save();
        }
        return;
    }

    /*
    * 增加数据
    *
    * By TerryQi
    *
    * 2018-07-18
    *
    * 增加统计数据 item统计项目 fans_num：粉丝人数 vote_num：投票数 show_num：展示数 gift_money：礼物价格 share_num：分享数
    */
    public static function addStatistics($vote_user_id, $item, $num)
    {
        $vote_user = self::getById($vote_user_id);
        switch ($item) {
            case "vote_num":
                $vote_user->vote_num = $vote_user->vote_num + $num;
                break;
            case "show_num":
                $vote_user->show_num = $vote_user->show_num + $num;
                break;
            case "gift_money":
                $vote_user->gift_money = $vote_user->gift_money + $num;
                break;
            case "share_num":
                $vote_user->share_num = $vote_user->share_num + $num;
                break;
            case "fans_num":
                $vote_user->fans_num = $vote_user->fans_num + $num;
                break;
        }
        $vote_user->save();
    }


    /*
     * 根据选手编号获取选手id
     *
     * By TerryQi
     *
     * 2018-09-11
     *
     * @code 此处code为编号信息，即HBA-2
     *
     * @return null代表没有获取到数据  id为选手的id
     */
    public static function getIdByCode($code)
    {
        $code_arr = explode('-', $code);
        //长度不为2，代表编码有错误，返回null
        if (count($code_arr) != 2) {
            return null;
        }
        $activity_code = $code_arr[0];
        $vote_user_code = $code_arr[1];
        //活动是否存在
        $activity = VoteActivityManager::getListByCon(['code' => $activity_code], false)->first();
        if (!$activity) {
            return null;
        }
        //选手是否存在
        $vote_user = VoteUserManager::getListByCon(['activity_id' => $activity->id, 'code' => $vote_user_code], false)->first();
        if (!$vote_user) {
            return null;
        }
        return $vote_user->id;
    }

    //获取用户姓名
    /*
     * 此处用户姓名为根据空格分割，获取第一个字符串
     *
     * By TerryQi
     *
     * 2018-09-11
     */
    public static function getVoteUserName($name)
    {
        Utils::processLog(__METHOD__, '', " " . "处理前 name:" . $name);
        $name = explode(' ', $name)[0];
        Utils::processLog(__METHOD__, '', " " . "处理后 name:" . $name);
        return $name;
    }

    /*
     * 生成选手海报
     *
     * By TerryQi
     *
     * 2018-09-11
     *
     * @入参为info_arr，其中需要包含的键值有 name：选手姓名 prize：奖项 cert_no：证书编号（选手编号） date：日期
     *
     * @return 为生成的证书path
     */

    public static function generateCert($info_arr)
    {
        //合规校验参数
        if (!array_key_exists('name', $info_arr) || !array_key_exists('prize', $info_arr) || !array_key_exists('cert_no', $info_arr) || !array_key_exists('date', $info_arr)) {
            return null;
        }

        $name = $info_arr['name'];
        $prize = $info_arr['prize'];
        $cert_no = $info_arr['cert_no'];
        $date = $info_arr['date'];

        //生成证书
        //证书基础文件
        $cert_base_path = public_path('img/vote/cert/vote_cert_base.jpg');
        $cert_base_img = imagecreatefromjpeg($cert_base_path);

        //生成新图片
        $generate_cert_path = public_path('img/vote/cert/' . Utils::generateTradeNo() . '.jpg');
        imagejpeg($cert_base_img, $generate_cert_path);
        $generate_cert_img = imagecreatefromjpeg($generate_cert_path);

        $fontfile = public_path('docs/css/fonts/msyh.ttf');

        // 分配颜色和透明度
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 35, 0, 1450, 340, $color, $fontfile, $name);

        //所获奖项
        $color = imagecolorallocatealpha($generate_cert_img, 245, 0, 0, 0);
        imagettftext($generate_cert_img, 40, 0, 1500, 600, $color, $fontfile, $prize);

        //证书编号
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 25, 0, 475, 1325, $color, $fontfile, $cert_no);

        //发证日期
        $color = imagecolorallocatealpha($generate_cert_img, 0, 0, 0, 0);
        imagettftext($generate_cert_img, 25, 0, 475, 1395, $color, $fontfile, $date);

        //生成图片数据
        imagejpeg($generate_cert_img, $generate_cert_path);
        //销毁数据
        imagedestroy($generate_cert_img);

        return $generate_cert_path;
    }


    /*
     * 获取奖项
     *
     * By TerryQi
     *
     * 2018-09-11
     */
    public static function getPrize($vote_user_id)
    {
        $vote_user = VoteUserManager::getById($vote_user_id);
        $activity = VoteActivityManager::getById($vote_user->activity_id);
        if ($activity->vote_status != '2') {
            return null;
        }
        //获取条件
        $con_arr = array(
            'activity_id' => $vote_user->activity_id,
            'audit_status' => '1',
            'status' => '1',
            'bigger_than_vote_num' => $vote_user->vote_num,
            'orderby' => [
                'vote_num' => 'desc'
            ]
        );
        //排在改名用户前面的用户数
        $curr_pm = self::getListByCon($con_arr, false)->count() + 1;
        $first_prize_num = $activity->first_prize_num;
        $second_prize_num = $activity->second_prize_num;
        $third_prize_num = $activity->third_prize_num;
        $honor_prize_num = $activity->honor_prize_num;

        //一等奖
        if ($curr_pm <= $first_prize_num) {
            return "金 奖";
        }
        //二等奖
        if ($curr_pm > $first_prize_num && $curr_pm <= ($first_prize_num + $second_prize_num)) {
            return "银 奖";
        }
        //三等奖
        if ($curr_pm > ($first_prize_num + $second_prize_num) && $curr_pm <= ($first_prize_num + $second_prize_num + $third_prize_num)) {
            return "铜 奖";
        }
        //优秀奖
        if ($vote_user->vote_num >= 500) {
            return "优秀奖";
        }

        return null;
    }


    //根据选手名称或者参赛编号返回参赛选手id
    /*
     * 其中，入参为keyword
     *
     * 返回值为id，其中id==null代表没有获取到选手信息 id==-1代表有多个重名用户
     *
     * By TerryQi
     *
     */
    public static function getIdByKeyword($keyword)
    {
        //根据姓名查询
        $vote_users = self::getListByCon(['search_word' => $keyword, 'status' => '1', 'audit_status' => '1'], false);
        //多个重名用户
        if ($vote_users->count() > 1) {
            return -1;      //有多个重名用户
        }
        //如果只有一个用户，直接返回这个用户id
        if ($vote_users->count() == 1) {
            return $vote_users->first()->id;      //有多个重名用户
        }
        //否则用大赛编码获取用户
        return self::getIdByCode($keyword);
    }

}