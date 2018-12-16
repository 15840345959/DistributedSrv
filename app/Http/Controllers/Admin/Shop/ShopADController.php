<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Shop;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\Shop\ShopADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Shop\ShopAD;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class ShopADController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $shopADs = ShopADManager::getListByCon($con_arr, true);
        foreach ($shopADs as $shopAD) {
            $shopAD = ShopADManager::getInfoByLevel($shopAD, '0');
        }
//        dd($shopADs);
        return view('admin.shop.shopAD.index', ['datas' => $shopADs, 'con_arr' => $con_arr]);
    }


    //设置活动广告状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数活动广告id$id']);
        }
        $shopAD = ShopADManager::getById($id);
        $shopAD->status = $data['status'];
        $shopAD->save();
        return ApiResponse::makeResponse(true, $shopAD, ApiResponse::SUCCESS_CODE);
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
        $shopAD = new ShopAD();
        if (array_key_exists('id', $data)) {
            $shopAD = ShopADManager::getById($data['id']);
        }
        return view('admin.shop.shopAD.edit', ['admin' => $admin, 'data' => $shopAD, 'upload_token' => $upload_token]);
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
        $shopAD = new ShopAD();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $shopAD = ShopADManager::getById($data['id']);
        }
        $shopAD = ShopADManager::setInfo($shopAD, $data);
        $shopAD->admin_id = $admin->id;      //记录活动广告id
        $shopAD->save();

        return ApiResponse::makeResponse(true, $shopAD, ApiResponse::SUCCESS_CODE);
    }

}