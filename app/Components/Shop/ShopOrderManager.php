<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/18
 * Time: 8:59
 */

namespace App\Components\Shop;

use App\Components\GoodsManager;
use App\Components\UserManager;
use App\Models\Shop\ShopOrder;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Components\AdminManager;
use App\Components\Utils;

class ShopOrderManager
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
        $info = ShopOrder::where('id', '=', $id)->first();
        return $info;
    }

    /*
     * 根据订单号查询
     *
     * By TerryQi
     *
     * 2018-07-24
     */
    public static function getByTradeNo($trade_no)
    {
        $info = ShopOrder::where('trade_no', '=', $trade_no)->first();
        return $info;
    }

    /*
     * 根据级别获取信息
     *
     * By TerryQi
     *
     * 2018-07-05
     *
     * 0：带商品信息 1：带顾客信息
     */
    public static function getInfoByLevel($info, $level)
    {
        $info->pay_status_str = Utils::SHOP_ORDER_PAY_STATUS_VAL[$info->pay_status];
        $info->send_status_str = Utils::SHOP_ORDER_SEND_STATUS_VAL[$info->send_status];
        $info->refund_status_str = Utils::SHOP_ORDER_REFUND_STATUS_VAL[$info->refund_status];
        $info->pay_type_str = Utils::SHOP_ORDER_PAY_TYPE_VAL[$info->pay_type];

        //商品信息
        if (strpos($level, '0') !== false) {
            if ($info->goods_id) {
                $goods = GoodsManager::getById($info->goods_id);
                $goods = GoodsManager::getInfoByLevel($goods, '0');
                $info->goods = $goods;
            }
        }
        //顾客信息
        if (strpos($level, '1') !== false) {
            if ($info->user_id) {
                $info->user = UserManager::getById($info->user_id);
            }
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
        if (array_key_exists('trade_no', $data)) {
            $info->trade_no = array_get($data, 'trade_no');
        }
        if (array_key_exists('prepay_id', $data)) {
            $info->prepay_id = array_get($data, 'prepay_id');
        }
        if (array_key_exists('user_id', $data)) {
            $info->user_id = array_get($data, 'user_id');
        }
        if (array_key_exists('goods_id', $data)) {
            $info->goods_id = array_get($data, 'goods_id');
        }
        if (array_key_exists('goods_num', $data)) {
            $info->goods_num = array_get($data, 'goods_num');
        }
        if (array_key_exists('rec_name', $data)) {
            $info->rec_name = array_get($data, 'rec_name');
        }
        if (array_key_exists('rec_zip_code', $data)) {
            $info->rec_zip_code = array_get($data, 'rec_zip_code');
        }
        if (array_key_exists('rec_tel', $data)) {
            $info->rec_tel = array_get($data, 'rec_tel');
        }
        if (array_key_exists('rec_address', $data)) {
            $info->rec_address = array_get($data, 'rec_address');
        }
        if (array_key_exists('total_fee', $data)) {
            $info->total_fee = array_get($data, 'total_fee');
        }
        if (array_key_exists('content', $data)) {
            $info->content = array_get($data, 'content');
        }
        if (array_key_exists('remark', $data)) {
            $info->remark = array_get($data, 'remark');
        }
        if (array_key_exists('pay_type', $data)) {
            $info->pay_type = array_get($data, 'pay_type');
        }
        if (array_key_exists('pay_status', $data)) {
            $info->pay_status = array_get($data, 'pay_status');
        }
        if (array_key_exists('pay_at', $data)) {
            $info->pay_at = array_get($data, 'pay_at');
        }
        if (array_key_exists('refund_status', $data)) {
            $info->refund_status = array_get($data, 'refund_status');
        }
        if (array_key_exists('refund_at', $data)) {
            $info->refund_at = array_get($data, 'refund_at');
        }
        if (array_key_exists('refund_fee', $data)) {
            $info->refund_fee = array_get($data, 'refund_fee');
        }
        if (array_key_exists('send_status', $data)) {
            $info->send_status = array_get($data, 'send_status');
        }
        if (array_key_exists('send_no', $data)) {
            $info->send_no = array_get($data, 'send_no');
        }
        if (array_key_exists('send_at', $data)) {
            $info->send_at = array_get($data, 'send_at');
        }
        return $info;
    }

    /*
     * 获取列表
     *
     * By TerryQi
     *
     * 2018-05-10
     */
    public static function getListByCon($con_arr, $is_paginate)
    {
        $infos = new ShopOrder();
        //相关条件
        if (array_key_exists('search_word', $con_arr) && !Utils::isObjNull($con_arr['search_word'])) {
            $infos = $infos->where('trade_no', 'like', '%' . $con_arr['search_word'] . '%')
                ->orwhere('send_no', 'like', '%' . $con_arr['search_word'] . '%');
        }
        if (array_key_exists('user_id', $con_arr) && !Utils::isObjNull($con_arr['user_id'])) {
            $infos = $infos->where('user_id', '=', $con_arr['user_id']);
        }
        if (array_key_exists('goods_id', $con_arr) && !Utils::isObjNull($con_arr['goods_id'])) {
            $infos = $infos->where('goods_id', '=', $con_arr['goods_id']);
        }
        if (array_key_exists('pay_status', $con_arr) && !Utils::isObjNull($con_arr['pay_status'])) {
            $infos = $infos->where('pay_status', '=', $con_arr['pay_status']);
        }
        if (array_key_exists('refund_status', $con_arr) && !Utils::isObjNull($con_arr['refund_status'])) {
            $infos = $infos->where('refund_status', '=', $con_arr['refund_status']);
        }
        if (array_key_exists('send_status', $con_arr) && !Utils::isObjNull($con_arr['send_status'])) {
            $infos = $infos->where('send_status', '=', $con_arr['send_status']);
        }
        //定位到哪天
        if (array_key_exists('at_date', $con_arr) && !Utils::isObjNull($con_arr['at_date'])) {
            $infos = $infos->whereDate('created_at', $con_arr['at_date']);
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