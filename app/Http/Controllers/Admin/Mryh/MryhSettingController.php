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

use App\Components\QNManager;
use App\Components\Mryh\MryhSettingManager;
use App\Components\RuleManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Mryh\MryhSetting;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MryhSettingController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $mryhSettings = MryhSettingManager::getListByCon($con_arr, true);
        foreach ($mryhSettings as $mryhSetting) {
            $mryhSetting = MryhSettingManager::getInfoByLevel($mryhSetting, '0');
        }
//        dd($mryhSettings);
        return view('admin.mryh.mryhSetting.index', ['datas' => $mryhSettings, 'con_arr' => $con_arr]);
    }


    //设置规则状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数规则id$id']);
        }
        $mryhSetting = MryhSettingManager::getById($id);
        $mryhSetting->status = $data['status'];
        $mryhSetting->save();
        return ApiResponse::makeResponse(true, $mryhSetting, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 添加、编辑=-get
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
        $mryhSetting = new MryhSetting();
        if (array_key_exists('id', $data)) {
            $mryhSetting = MryhSettingManager::getById($data['id']);
        }
        $mryhSetting = MryhSettingManager::setInfo($mryhSetting, $data);

        //生成活动规则选项
        $rules = RuleManager::getListByCon(['status' => '1', 'busi_name' => 'mryh', 'position' => '1'], false);

        return view('admin.mryh.mryhSetting.edit', ['admin' => $admin,
            'data' => $mryhSetting, 'upload_token' => $upload_token, 'rules' => $rules]);
    }

    /*
     * 添加、编辑=-post
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
        $mryhSetting = new MryhSetting();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $mryhSetting = MryhSettingManager::getById($data['id']);
        }
        $mryhSetting = MryhSettingManager::setInfo($mryhSetting, $data);
        $mryhSetting->admin_id = $admin->id;      //记录规则id
        $mryhSetting->save();

        return ApiResponse::makeResponse(true, $mryhSetting, ApiResponse::SUCCESS_CODE);
    }

}