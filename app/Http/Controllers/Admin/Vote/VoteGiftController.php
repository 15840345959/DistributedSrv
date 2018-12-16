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
use App\Components\Vote\VoteGiftManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteGift;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class VoteGiftController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $voteGifts = VoteGiftManager::getListByCon($con_arr, true);
        foreach ($voteGifts as $voteGift) {
            $voteGift = VoteGiftManager::getInfoByLevel($voteGift, '0');
        }
//        dd($voteGifts);
        return view('admin.vote.voteGift.index', ['datas' => $voteGifts, 'con_arr' => $con_arr]);
    }


    //设置礼品状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数礼品id$id']);
        }
        $voteGift = VoteGiftManager::getById($id);
        $voteGift->status = $data['status'];
        $voteGift->save();
        return ApiResponse::makeResponse(true, $voteGift, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 添加、编辑投票礼品-get
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $voteGift = new VoteGift();
        if (array_key_exists('id', $data)) {
            $voteGift = VoteGiftManager::getById($data['id']);
        }
        $voteGift = VoteGiftManager::setInfo($voteGift, $data);
        $voteGift = VoteGiftManager::getInfoByLevel($voteGift, '');
        return view('admin.vote.voteGift.edit', ['admin' => $admin, 'data' => $voteGift, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑投票礼品-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $voteGift = new VoteGift();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $voteGift = VoteGiftManager::getById($data['id']);
        }
        $voteGift = VoteGiftManager::setInfo($voteGift, $data);
        $voteGift->admin_id = $admin->id;      //记录礼品id
        $voteGift->save();

        return ApiResponse::makeResponse(true, $voteGift, ApiResponse::SUCCESS_CODE);
    }

}