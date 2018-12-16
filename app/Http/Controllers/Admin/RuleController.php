<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\RuleManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Rule;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class RuleController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //配置条件
        $busi_name = null;
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        $con_arr = array(
            'busi_name' => $busi_name
        );
//        dd($con_arr);
        $rules = RuleManager::getListByCon($con_arr, true);
        foreach ($rules as $rule) {
            $rule = RuleManager::getInfoByLevel($rule, '0');
        }
//        dd($rules);
        return view('admin.rule.index', ['datas' => $rules, 'con_arr' => $con_arr]);
    }


    //设置规则状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数规则id$id']);
        }
        $rule = RuleManager::getById($id);
        $rule->status = $data['status'];
        $rule->save();
        return ApiResponse::makeResponse(true, $rule, ApiResponse::SUCCESS_CODE);
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
        $rule = new Rule();
        if (array_key_exists('id', $data)) {
            $rule = RuleManager::getById($data['id']);
        }
        $rule = RuleManager::setInfo($rule, $data);
        return view('admin.rule.edit', ['admin' => $admin, 'data' => $rule, 'upload_token' => $upload_token]);
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
        $rule = new Rule();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $rule = RuleManager::getById($data['id']);
        }
        $rule = RuleManager::setInfo($rule, $data);
        $rule->admin_id = $admin->id;      //记录规则id
        $rule->save();

        return ApiResponse::makeResponse(true, $rule, ApiResponse::SUCCESS_CODE);
    }

}