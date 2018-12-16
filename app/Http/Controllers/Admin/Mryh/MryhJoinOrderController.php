<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteUser;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MryhJoinOrderController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $search_word = null;
        $pay_status = null;
        $game_id = null;
        $user_id = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('pay_status', $data) && !Utils::isObjNull($data['pay_status'])) {
            $pay_status = $data['pay_status'];
        }
        if (array_key_exists('game_id', $data) && !Utils::isObjNull($data['game_id'])) {
            $game_id = $data['game_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'pay_status' => $pay_status,
            'game_id' => $game_id,
            'user_id' => $user_id,
            'search_word' => $search_word
        );

        $mryhJoinOrders = MryhJoinOrderManager::getListByCon($con_arr, true);
        foreach ($mryhJoinOrders as $mryhJoinOrder) {
            $mryhJoinOrder = MryhJoinOrderManager::getInfoByLevel($mryhJoinOrder, '01');
        }
//        dd($mryhJoinOrders);
        return view('admin.mryh.mryhJoinOrder.index', ['datas' => $mryhJoinOrders, 'con_arr' => $con_arr]);
    }
}