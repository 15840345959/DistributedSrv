<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/9/28
 * Time: 10:30
 */

namespace App\Components;

use App\Models\Login;
use App\Models\User;

class FTableManager
{

    /*
     * 根据f_fable，通过f_id获取业务对象
     *
     * By TerryQi
     *
     * 2018-06-13
     *
     * info：带有f_table和f_id的信息项  $level:信息级别
     *
     */
    public static function getObjWithFtable($info, $level)
    {
        switch ($info->f_table) {
            case Utils::F_TABLB_ARTICLE:            //作品信息
                $info->article = ArticleManager::getById($info->f_id);
                break;
        }
        return $info;
    }


    /*
     * 增加统计数据
     *
     * By TerryQo
     *
     * 2018-09-20
     *
     */
    public static function addStatistics($f_table, $f_id, $item, $num)
    {
        switch ($f_table) {
            case Utils::F_TABLB_ARTICLE:            //作品信息
                ArticleManager::addStatistics($f_id, $item, $num);
                break;
            case Utils::F_TABLB_GOODS:            //商品信息
                GoodsManager::addStatistics($f_id, $item, $num);
                break;
        }
    }

}