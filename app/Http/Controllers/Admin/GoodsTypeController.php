<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/20
 * Time: 10:50
 */

namespace App\Http\Controllers\Admin;


use App\Components\GoodsTypeManager;
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\GoodsType;
use Illuminate\Http\Request;

class GoodsTypeController
{

    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
        //相关搜素条件
        $busi_name = null;
        $status = null;
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        if (array_key_exists('status', $data) && !Utils::isObjNull($data['status'])) {
            $status = $data['status'];
        }
        $con_arr = array(
            'busi_name' => $busi_name,
            'status' => $status
        );
        $goodsTypes = GoodsTypeManager::getListByCon($con_arr, true);
        foreach ($goodsTypes as $goodsType) {
            $goodsType = GoodsTypeManager::getInfoByLevel($goodsType, '');
        }

        return view('admin.goodsType.index', ['admin' => $admin, 'datas' => $goodsTypes, 'con_arr' => $con_arr]);
    }

    //编辑
    public function edit(Request $request)
    {
        $method = $request->method();
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        //通过method来区别
        switch ($method) {
            case 'GET':
                $goodsType = new GoodsType();
                if (array_key_exists('id', $data)) {
                    $goodsType = GoodsTypeManager::getById($data['id']);
                }
                return view('admin.goodsType.edit', ['admin' => $admin, 'data' => $goodsType, 'upload_token' => $upload_token]);
                break;
            case 'POST':
                $goodsType = new GoodsType();
                $return = null;
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $goodsType = GoodsTypeManager::getById($data['id']);
                }
                if (!$goodsType) {
                    $goodsType = new GoodsType();
                }
                $goodsType = GoodsTypeManager::setInfo($goodsType, $data);
                $result = $goodsType->save();
                if ($result) {
                    return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
                } else {
                    return ApiResponse::makeResponse(false, $result, ApiResponse::INNER_ERROR);
                }
                break;
            default:
                break;
        }
    }


    /*
    * 设置作品类型状态
    *
    * By mtt
    *
    * 2018-4-9
    */
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数id$id']);
        }
        $goodsType = GoodsTypeManager::getById($data['id']);
        $goodsType = GoodsTypeManager::setInfo($goodsType, $data);
        $goodsType->save();
        return ApiResponse::makeResponse(true, $goodsType, ApiResponse::SUCCESS_CODE);
    }


}











