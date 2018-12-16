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
use App\Components\Mryh\MryhComputePrizeManager;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Mryh\MryhUserCouponManager;

use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhUserCoupon;
use Illuminate\Http\Request;


class MryhComputePrizeController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //配置条件
        $game_id = null;
        if (array_key_exists('game_id', $data) && !Utils::isObjNull($data['game_id'])) {
            $game_id = $data['game_id'];
        }
        $con_arr = array(
            'game_id' => $game_id,
        );
        $mryhComputePrizes = MryhComputePrizeManager::getListByCon($con_arr, true);
        foreach ($mryhComputePrizes as $mryhComputePrize) {
            $mryhComputePrize = MryhComputePrizeManager::getInfoByLevel($mryhComputePrize, '0');
        }
//        dd($mryhComputePrize);
        return view('admin.mryh.mryhComputePrize.index', ['datas' => $mryhComputePrizes, 'con_arr' => $con_arr]);
    }

}