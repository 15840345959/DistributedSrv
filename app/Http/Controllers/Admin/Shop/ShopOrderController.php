<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Shop;

use App\Components\AdminManager;
use App\Components\OptRecordManager;
use App\Components\QNManager;
use App\Components\Shop\ShopActivityManager;
use App\Components\Shop\ShopOrderManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Shop\ShopUser;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class ShopOrderController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $search_word = null;
        $pay_status = null;
        $send_status = null;
        $refund_status = null;
        $goods_id = null;
        $user_id = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('pay_status', $data) && !Utils::isObjNull($data['pay_status'])) {
            $pay_status = $data['pay_status'];
        }
        if (array_key_exists('send_status', $data) && !Utils::isObjNull($data['send_status'])) {
            $send_status = $data['send_status'];
        }
        if (array_key_exists('refund_status', $data) && !Utils::isObjNull($data['refund_status'])) {
            $refund_status = $data['refund_status'];
        }
        if (array_key_exists('goods_id', $data) && !Utils::isObjNull($data['goods_id'])) {
            $goods_id = $data['goods_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'pay_status' => $pay_status,
            'send_status' => $send_status,
            'refund_status' => $refund_status,
            'goods_id' => $goods_id,
            'user_id' => $user_id,
            'search_word' => $search_word
        );

        $shop_orders = ShopOrderManager::getListByCon($con_arr, true);
        foreach ($shop_orders as $shopUser) {
            $shopUser = ShopOrderManager::getInfoByLevel($shopUser, '01');
        }
//        dd($shop_orders);
        return view('admin.shop.shopOrder.index', ['datas' => $shop_orders, 'con_arr' => $con_arr]);
    }


    /*
     * 获取订单详情
     *
     * By TerryQi
     *
     * 2018-11-18
     */
    public function info(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }

        //订单信息
        $shopOrder = ShopOrderManager::getById($data['id']);
        $shopOrder = ShopOrderManager::getInfoByLevel($shopOrder, '01'); //丰富订单信息

        //操作过程
        $optRecords = OptRecordManager::getListByCon(['f_table' => 'shopOrder', 'f_id' => $shopOrder->id], false);
        foreach ($optRecords as $optRecord) {
            $optRecord = OptRecordManager::getInfoByLevel($optRecord, '0');
        }

        return view('admin.shop.shopOrder.info', ['data' => $shopOrder, 'optRecords' => $optRecords]);
    }

}