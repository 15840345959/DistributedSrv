<?php

/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Vote;

use App\Components\QNManager;
use App\Components\Utils;
use App\Components\RequestValidator;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteTeamManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class VoteTeamController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');

        $search_word = null;
        $id = null;
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }

        $con_arr = array(
            'search_word' => $search_word,
            'id' => $id
        );

        $voteTeams = VoteTeamManager::getListByCon($con_arr, true);
        foreach ($voteTeams as $voteTeam) {
            $voteTeam = VoteTeamManager::getInfoByLevel($voteTeam, '0');
        }
//        dd($voteTeams);
        return view('admin.vote.voteTeam.index', ['datas' => $voteTeams, 'con_arr' => $con_arr]);
    }

    //设置礼品状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数礼品id$id']);
        }
        $voteTeam = VoteTeamManager::getById($id);
        $voteTeam->status = $data['status'];
        $voteTeam->save();
        return ApiResponse::makeResponse(true, $voteTeam, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 添加、编辑地推团队-get
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
        $voteTeam = new VoteTeam();
        if (array_key_exists('id', $data)) {
            $voteTeam = VoteTeamManager::getById($data['id']);
            $voteTeam = VoteTeamManager::setInfo($voteTeam, $data);
            $voteTeam = VoteTeamManager::getInfoByLevel($voteTeam, '');
        }
        return view('admin.vote.voteTeam.edit', ['admin' => $admin, 'data' => $voteTeam, 'upload_token' => $upload_token]);
    }

    /**
     * 添加、编辑地推团队-post
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $voteTeam = new VoteTeam();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $voteTeam = VoteTeamManager::getById($data['id']);
        }
        $find_voteTeam = VoteTeamManager::getListByCon(['phonenum' => array_get($data, 'phonenum')], false);

        if ($find_voteTeam->first()) {
            return ApiResponse::makeResponse(false, '此联系电话已经存在', ApiResponse::INNER_ERROR);
        }
        $data['password'] = md5(array_get($data, 'password'));
        $voteTeam = VoteTeamManager::setInfo($voteTeam, $data);
        $voteTeam->admin_id = $admin->id;
        $voteTeam->save();

        return ApiResponse::makeResponse(true, $voteTeam, ApiResponse::SUCCESS_CODE);
    }

    /**
     * @param \App\Http\Controllers\Admin\Vote\Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrCode(Request $request, $id)
    {
        $data = $request->all();

        return view('admin.vote.voteTeam.qrcode', [
            'id' => $id
        ]);
    }

    /*
     * 地推团队详情
     *
     * By TerryQi
     *
     * 2018-12-06
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
        $voteTeam = VoteTeamManager::getById($data['id']);
        $voteTeam = VoteTeamManager::getInfoByLevel($voteTeam, '');

        //他负责的近15个的活动
        $voteActivities = VoteActivityManager::getListByCon(['vote_team_id' => $voteTeam->id], false);
        foreach ($voteActivities as $voteActivity) {
            $voteActivity = VoteActivityManager::getInfoByLevel($voteActivity, '');
        }

        return view('admin.vote.voteTeam.info', ['admin' => $admin, 'data' => $voteTeam, 'voteActivities' => $voteActivities]);
    }


}
