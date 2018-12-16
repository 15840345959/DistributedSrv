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
use App\Components\Vote\VoteRuleManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteRule;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class VoteRuleController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $voteRules = VoteRuleManager::getListByCon($con_arr, true);
        foreach ($voteRules as $voteRule) {
            $voteRule = VoteRuleManager::getInfoByLevel($voteRule, '0');
        }
//        dd($voteRules);
        return view('admin.vote.voteRule.index', ['datas' => $voteRules, 'con_arr' => $con_arr]);
    }


    //设置规则状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数规则id$id']);
        }
        $voteRule = VoteRuleManager::getById($id);
        $voteRule->status = $data['status'];
        $voteRule->save();
        return ApiResponse::makeResponse(true, $voteRule, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 添加、编辑投票规则-get
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
        $voteRule = new VoteRule();
        if (array_key_exists('id', $data)) {
            $voteRule = VoteRuleManager::getById($data['id']);
        }
        $voteRule = VoteRuleManager::setInfo($voteRule, $data);
        $voteRule = VoteRuleManager::getInfoByLevel($voteRule, '');
        return view('admin.vote.voteRule.edit', ['admin' => $admin, 'data' => $voteRule, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑投票规则-post
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
        $voteRule = new VoteRule();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $voteRule = VoteRuleManager::getById($data['id']);
        }
        $voteRule = VoteRuleManager::setInfo($voteRule, $data);
        $voteRule->admin_id = $admin->id;      //记录规则id
        $voteRule->save();

        return ApiResponse::makeResponse(true, $voteRule, ApiResponse::SUCCESS_CODE);
    }

}