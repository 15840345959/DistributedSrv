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
use App\Components\Vote\VoteUserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteUser;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class VoteUserController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $search_word = null;
        $activity_id = null;
        $vote_user_id = null;
        $audit_status = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $activity_id = $data['activity_id'];
        }
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        if (array_key_exists('audit_status', $data) && !Utils::isObjNull($data['audit_status'])) {
            $audit_status = $data['audit_status'];
        }
        $con_arr = array(
            'search_word' => $search_word,
            'activity_id' => $activity_id,
            'vote_user_id' => $vote_user_id,
            'audit_status' => $audit_status
        );

        $voteUsers = VoteUserManager::getListByCon($con_arr, true);
        foreach ($voteUsers as $voteUser) {
            $voteUser = VoteUserManager::getInfoByLevel($voteUser, '01');
        }
//        dd($voteUsers);
        return view('admin.vote.voteUser.index', ['datas' => $voteUsers, 'con_arr' => $con_arr]);
    }


    //设置选手状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数选手id$id']);
        }
        $voteUser = VoteUserManager::getById($id);
        $voteUser->status = $data['status'];
        $voteUser->save();
        return ApiResponse::makeResponse(true, $voteUser, ApiResponse::SUCCESS_CODE);
    }

    //审核选手信息
    public function setAuditStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数选手id$id']);
        }
        $voteUser = VoteUserManager::getById($id);
        $voteUser->audit_status = $data['audit_status'];
        $voteUser->save();
        return ApiResponse::makeResponse(true, $voteUser, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 添加、编辑投票选手-get
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
//        dd($data);
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $voteUser = new VoteUser();
        if (array_key_exists('id', $data)) {
            $voteUser = VoteUserManager::getById($data['id']);
//            dd($voteUser);
        } else {
            //如果是新建用户其中大赛的id必传
            $requestValidationResult = RequestValidator::validator($request->all(), [
                'activity_id' => 'required',
            ]);
            if ($requestValidationResult !== true) {
                return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
            }
        }
        $voteUser = VoteUserManager::setInfo($voteUser, $data);
//        $voteUser = VoteUserManager::getInfoByLevel($voteUser, '');
        return view('admin.vote.voteUser.edit', ['admin' => $admin, 'data' => $voteUser, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑投票选手-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     */
    public function editPost(Request $request)
    {
        $data = $request->all();

//        dd($data);
        $admin = $request->session()->get('admin');
        $voteUser = null;

        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $voteUser = VoteUserManager::getById($data['id']);
            $voteUser = VoteUserManager::setInfo($voteUser, $data);
        } else {
            $voteUser = new VoteUser();
            $voteUser->admin_id = $admin->id;      //记录管理员id
            //从系统导入的参赛选手自动设置为生效+审核通过
            $voteUser->audit_status = '1';
            $voteUser->status = '1';
            $voteUser = VoteUserManager::setInfo($voteUser, $data);
            $voteUser->save();
            //如果是新建用户-增加活动统计记录
            VoteActivityManager::addStatistics($voteUser->activity_id, 'join_num', 1);
        }

        $voteUser->save();
        //记录编号
        VoteUserManager::setCode($voteUser);
        return ApiResponse::makeResponse(true, $voteUser, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 详细信息
     *
     * By TerryQi
     *
     * 2018-07-22
     *
     */
    public function info(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //如果是新建用户其中大赛的id必传
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $vote_user = VoteUserManager::getById($data['id']);
        $vote_user = VoteUserManager::getInfoByLevel($vote_user, '012');

        return view('admin.vote.voteUser.info', ['admin' => $admin, 'data' => $vote_user]);
    }

    /*
     * 导入参赛选手
     *
     * By TerryQi
     *
     * 2018-07-31
     */
    public function importVoteUser(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数大赛id']);
        }
        //获取大赛信息
        $voteActivity = new VoteActivity();
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $voteActivity = VoteActivityManager::getById($data['activity_id']);
        }

        //生成七牛token
        $upload_token = QNManager::uploadToken();
        return view('admin.vote.voteActivity.importVoteUser', ['admin' => $admin, 'data' => $voteActivity
            , 'upload_token' => $upload_token]);
    }

    /*
     * 导入参赛选手-post
     *
     * By TerryQi
     *
     * 2018-07-31
     */
    public function importVoteUserPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
//        dd($data);
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, "没有大赛id，请联系管理员处理", ApiResponse::INNER_ERROR);
        }
        $activity_id = $data['activity_id'];
        //业务数据
        $name_arr = [];
        $img_arr = [];
        $video_arr = [];
        //加载业务数据
        if (array_key_exists('name', $data)) {
            $name_arr = $data['name'];
        }
        if (array_key_exists('img', $data)) {
            $img_arr = $data['img'];
        }
        if (array_key_exists('video', $data)) {
            $video_arr = $data['video'];
        }
        //如果没有作者信息
        if (count($name_arr) == 0) {
            return ApiResponse::makeResponse(false, "未传入选手姓名", ApiResponse::INNER_ERROR);
        }
        //如果数组长度不同
        if (count($img_arr) > 0) {
            if (count($name_arr) != count($img_arr)) {
                return ApiResponse::makeResponse(false, "选手与作品数不匹配", ApiResponse::INNER_ERROR);
            }
        }
        if (count($video_arr) > 0) {
            if (count($name_arr) != count($video_arr)) {
                return ApiResponse::makeResponse(false, "选手与作品数不匹配", ApiResponse::INNER_ERROR);
            }
        }

        for ($i = 0; $i < count($name_arr); $i++) {
            $voteUser = new VoteUser();
            $data_obj = array(
                'activity_id' => $activity_id,
            );
            $data_obj['activity_id'] = $activity_id;
            $data_obj['name'] = $name_arr[$i];
            if (count($img_arr) > 0) {
                $data_obj['img'] = $img_arr[$i];
            }
            if (count($video_arr) > 0) {
                $data_obj['video'] = $video_arr[$i];
            }
//            dd($data_obj);
            $voteUser = VoteUserManager::setInfo($voteUser, $data_obj);
            $voteUser->admin_id = $admin->id;      //记录管理员id
            //从系统导入的参赛选手自动设置为生效+审核通过
            $voteUser->audit_status = '1';
            $voteUser->status = '1';
            $voteUser->save();
            //记录编号
            VoteUserManager::setCode($voteUser);
            VoteActivityManager::addStatistics($activity_id, 'join_num', 1);
        }
        return ApiResponse::makeResponse(true, "批量导入成功", ApiResponse::SUCCESS_CODE);
    }


    /*
    * 导入参赛选手-视频类
    *
    * By TerryQi
    *
    * 2018-08-25
    */
    public function importVoteUserVideo(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'activity_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数大赛id']);
        }
        //获取大赛信息
        $voteActivity = new VoteActivity();
        if (array_key_exists('activity_id', $data) && !Utils::isObjNull($data['activity_id'])) {
            $voteActivity = VoteActivityManager::getById($data['activity_id']);
        }

        //生成七牛token
        $upload_token = QNManager::uploadToken();
        return view('admin.vote.voteActivity.importVoteUserVideo', ['admin' => $admin, 'data' => $voteActivity
            , 'upload_token' => $upload_token]);
    }

    /**
     * @param \App\Http\Controllers\Admin\Vote\Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrCode(Request $request, $id)
    {
        $data = $request->all();

        return view('admin.vote.voteUser.qrcode', [
            'id' => $id
        ]);
    }

}