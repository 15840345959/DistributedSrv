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
use App\Components\Mryh\MryhADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Mryh\MryhAD;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MryhADController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $mryhADs = MryhADManager::getListByCon($con_arr, true);
        foreach ($mryhADs as $mryhAD) {
            $mryhAD = MryhADManager::getInfoByLevel($mryhAD, '0');
        }
//        dd($mryhADs);
        return view('admin.mryh.mryhAD.index', ['datas' => $mryhADs, 'con_arr' => $con_arr]);
    }


    //设置活动广告状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数活动广告id$id']);
        }
        $mryhAD = MryhADManager::getById($id);
        $mryhAD->status = $data['status'];
        $mryhAD->save();
        return ApiResponse::makeResponse(true, $mryhAD, ApiResponse::SUCCESS_CODE);
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
        $mryhAD = new MryhAD();
        if (array_key_exists('id', $data)) {
            $mryhAD = MryhADManager::getById($data['id']);
        }
        return view('admin.mryh.mryhAD.edit', ['admin' => $admin, 'data' => $mryhAD, 'upload_token' => $upload_token]);
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
        $mryhAD = new MryhAD();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $mryhAD = MryhADManager::getById($data['id']);
        }
        $mryhAD = MryhADManager::setInfo($mryhAD, $data);
        $mryhAD->admin_id = $admin->id;      //记录活动广告id
        $mryhAD->save();

        return ApiResponse::makeResponse(true, $mryhAD, ApiResponse::SUCCESS_CODE);
    }

}