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
use App\Components\Vote\VoteADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteAD;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class VoteADController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $voteADs = VoteADManager::getListByCon($con_arr, true);
        foreach ($voteADs as $voteAD) {
            $voteAD = VoteADManager::getInfoByLevel($voteAD, '0');
        }
//        dd($voteADs);
        return view('admin.vote.voteAD.index', ['datas' => $voteADs, 'con_arr' => $con_arr]);
    }


    //设置活动广告状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数活动广告id$id']);
        }
        $voteAD = VoteADManager::getById($id);
        $voteAD->status = $data['status'];
        $voteAD->save();
        return ApiResponse::makeResponse(true, $voteAD, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 添加、编辑投票活动广告-get
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
        $voteAD = new VoteAD();
        if (array_key_exists('id', $data)) {
            $voteAD = VoteADManager::getById($data['id']);
        }
        $voteAD = VoteADManager::setInfo($voteAD, $data);
        $voteAD = VoteADManager::getInfoByLevel($voteAD, '');
        return view('admin.vote.voteAD.edit', ['admin' => $admin, 'data' => $voteAD, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑投票活动广告-post
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
        $voteAD = new VoteAD();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $voteAD = VoteADManager::getById($data['id']);
        }
        $voteAD = VoteADManager::setInfo($voteAD, $data);
        $voteAD->admin_id = $admin->id;      //记录活动广告id
        $voteAD->save();

        return ApiResponse::makeResponse(true, $voteAD, ApiResponse::SUCCESS_CODE);
    }

}