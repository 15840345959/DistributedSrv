<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\YSB;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\YSB\YSBADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\YSB\YSBAD;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class YSBADController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $ysbADs = YSBADManager::getListByCon($con_arr, true);
        foreach ($ysbADs as $ysbAD) {
            $ysbAD = YSBADManager::getInfoByLevel($ysbAD, '0');
        }
//        dd($ysbADs);
        return view('admin.ysb.ysbAD.index', ['datas' => $ysbADs, 'con_arr' => $con_arr]);
    }


    //设置活动广告状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数活动广告id$id']);
        }
        $ysbAD = YSBADManager::getById($id);
        $ysbAD->status = $data['status'];
        $ysbAD->save();
        return ApiResponse::makeResponse(true, $ysbAD, ApiResponse::SUCCESS_CODE);
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
        $ysbAD = new YSBAD();
        if (array_key_exists('id', $data)) {
            $ysbAD = YSBADManager::getById($data['id']);
        }
        return view('admin.ysb.ysbAD.edit', ['admin' => $admin, 'data' => $ysbAD, 'upload_token' => $upload_token]);
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
        $ysbAD = new YSBAD();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $ysbAD = YSBADManager::getById($data['id']);
        }
        $ysbAD = YSBADManager::setInfo($ysbAD, $data);
        $ysbAD->admin_id = $admin->id;      //记录活动广告id
        $ysbAD->save();

        return ApiResponse::makeResponse(true, $ysbAD, ApiResponse::SUCCESS_CODE);
    }

}