<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Mryh\MryhUserCouponManager;

use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhUserCoupon;
use Illuminate\Http\Request;


class MryhUserCouponController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //配置条件
        $user_id = null;
        $coupon_id = null;
        $valid_status = null;
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('coupon_id', $data) && !Utils::isObjNull($data['coupon_id'])) {
            $coupon_id = $data['coupon_id'];
        }
        if (array_key_exists('valid_status', $data) && !Utils::isObjNull($data['valid_status'])) {
            $valid_status = $data['valid_status'];
        }
        $con_arr = array(
            'user_id' => $user_id,
            'coupon_id' => $coupon_id,
            'valid_status' => $valid_status
        );
        $mryhUserCoupons = MryhUserCouponManager::getListByCon($con_arr, true);
        foreach ($mryhUserCoupons as $mryhUserCoupon) {
            $mryhUserCoupon = MryhUserCouponManager::getInfoByLevel($mryhUserCoupon, '01');
        }
//        dd($mryhUserCoupons);
        return view('admin.mryh.mryhUserCoupon.index', ['datas' => $mryhUserCoupons, 'con_arr' => $con_arr]);
    }




}