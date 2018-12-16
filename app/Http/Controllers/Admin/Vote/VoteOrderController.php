<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Vote;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteUser;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class VoteOrderController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $search_word = null;
        $pay_status = null;
        $activity_id = null;
        $vote_user_id = null;
        $user_id = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('pay_status', $data) && !Utils::isObjNull($data['pay_status'])) {
            $pay_status = $data['pay_status'];
        }
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $activity_id = $data['activity_id'];
        }
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'pay_status' => $pay_status,
            'activity_id' => $activity_id,
            'vote_user_id' => $vote_user_id,
            'user_id' => $user_id,
            'search_word' => $search_word
        );

        $vote_orders = VoteOrderManager::getListByCon($con_arr, true);
        foreach ($vote_orders as $voteUser) {
            $voteUser = VoteOrderManager::getInfoByLevel($voteUser, '0123');
        }
//        dd($vote_orders);
        return view('admin.vote.voteOrder.index', ['datas' => $vote_orders, 'con_arr' => $con_arr]);
    }


    /*
     * 设置订单状态
     *
     * By TerryQi
     *
     * 2018-09-11
     */
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数订单id$id']);
        }
        $vote_order = VoteOrderManager::getById($id);
        $vote_order->pay_status = $data['pay_status'];
        $vote_order->save();
        return ApiResponse::makeResponse(true, $vote_order, ApiResponse::SUCCESS_CODE);
    }

}