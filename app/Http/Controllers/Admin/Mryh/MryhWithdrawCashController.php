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
use App\Components\Mryh\MryhWithdrawCashManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Mryh\MryhUserCouponManager;

use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhUserCoupon;
use Illuminate\Http\Request;


class MryhWithdrawCashController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //配置条件
        $user_id = null;
        $start_time = null;
        $end_time = null;
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('start_time', $data) && !Utils::isObjNull($data['start_time'])) {
            $start_time = $data['start_time'];
        }
        if (array_key_exists('end_time', $data) && !Utils::isObjNull($data['end_time'])) {
            $end_time = $data['end_time'];
        }
        $con_arr = array(
            'user_id' => $user_id,
            'start_time' => $start_time,
            'end_time' => $end_time
        );
        $mryhWithdrawCashs = MryhWithdrawCashManager::getListByCon($con_arr, true);
//        dd($mryhWithdrawCashs);
        foreach ($mryhWithdrawCashs as $mryhWithdrawCash) {
            $mryhWithdrawCash = MryhWithdrawCashManager::getInfoByLevel($mryhWithdrawCash, '01');
        }

        return view('admin.mryh.mryhWithdrawCash.index', ['datas' => $mryhWithdrawCashs, 'con_arr' => $con_arr]);
    }

    //详情信息
    public function info(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }

        $mryhWithdrawCash = MryhWithdrawCashManager::getById($data['id']);
        if (!$mryhWithdrawCash) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '没有找到提现记录 id:' . $data['id']]);
        }
        $mryhWithdrawCash = MryhWithdrawCashManager::getInfoByLevel($mryhWithdrawCash, '01');

        return view('admin.mryh.mryhWithdrawCash.info', ['data' => $mryhWithdrawCash]);
    }

}