<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Mryh;

use App\Components\UserManager;
use App\Models\Mryh\MryhJoin;
use App\Models\User;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class MryhFriendManager
{

    /*
     * 根据user_id获取我的朋友中参加活动的列表
     *
     * By TerryQi
     *
     * 2018-08-19
     *
     * @param user_id:为用户id is_paginate为是否分页
     *
     * @return 为user信息
     */
    public static function getListByConJoinGame($user_id, $is_paginate)
    {
        //配置条件
        $rel_sql_str = "select a_user_id as user_id from t_user_rel where b_user_id = ? UNION ALL select b_user_id as user_id from t_user_rel where a_user_id = ? group by user_id order by user_id desc";
        $join_sql_str = "select user_id from mryh_join_info where user_id in (" . $rel_sql_str . ")";
        Utils::processLog(__METHOD__, '', json_encode($join_sql_str));
        $infos = User::whereRaw("id in (" . $join_sql_str . ")", [$user_id, $user_id]);
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }


    /*
     * 根据user_id获取我的朋友中参加互动的列表，我的朋友为二级关联条件
     *
     * By TerryQi
     *
     * 2018-08-19
     *
     * @param user_id:为用户id， is_paginate为是否分页
     *
     * @return 为user信息
     *
     */
    public static function getListByConJoinGameLeve2($user_id, $is_paginate)
    {
        //配置条件
        $rel_sql_str = "select a_user_id as user_id from t_user_rel where b_user_id = ? UNION ALL select b_user_id as user_id from t_user_rel where a_user_id = ? group by user_id order by user_id desc";
        $level2_rel_sql_str = "select a_user_id as user_id from t_user_rel where b_user_id in (" . $rel_sql_str . ") UNION ALL select b_user_id as user_id from t_user_rel where a_user_id in (" . $rel_sql_str . ") group by user_id order by user_id desc";
        $join_sql_str = "select user_id from mryh_join_info where user_id in (" . $rel_sql_str . ")";
        Utils::processLog(__METHOD__, '', json_encode($join_sql_str));
        $infos = User::whereRaw("id in (" . $join_sql_str . ")", [$user_id, $user_id, $user_id, $user_id]);
        if ($is_paginate) {
            $infos = $infos->paginate(Utils::PAGE_SIZE);
        } else {
            $infos = $infos->get();
        }
        return $infos;
    }

}