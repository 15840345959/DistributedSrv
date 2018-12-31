<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/20
 * Time: 10:50
 */

namespace App\Http\Controllers\Admin;


use App\Components\OptInfoManager;
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\OptInfo;
use Illuminate\Http\Request;

class OptInfoController
{

    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
        //相关搜素条件
        $f_table = null;
        if (array_key_exists('f_table', $data) && !Utils::isObjNull($data['f_table'])) {
            $f_table = $data['f_table'];
        }
        $con_arr = array(
            'f_table' => $f_table
        );
        $optInfos = OptInfoManager::getListByCon($con_arr, true);
        foreach ($optInfos as $optInfo) {
            $optInfo = OptInfoManager::getInfoByLevel($optInfo, '');
        }

        return view('admin.optInfo.index', ['admin' => $admin, 'datas' => $optInfos, 'con_arr' => $con_arr]);
    }

    //编辑
    public function edit(Request $request)
    {
        $method = $request->method();
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();

        switch ($method) {
            case 'GET':
                $optInfo = new OptInfo();
                if (array_key_exists('id', $data)) {
                    $optInfo = OptInfoManager::getById($data['id']);
                }
                return view('admin.optInfo.edit', ['admin' => $admin, 'data' => $optInfo, 'upload_token' => $upload_token]);
                break;
            case 'POST':
                $optInfo = new OptInfo();
                $return = null;
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $optInfo = OptInfoManager::getById($data['id']);
                }
                if (!$optInfo) {
                    $optInfo = new OptInfo();
                }
                $optInfo = OptInfoManager::setInfo($optInfo, $data);
                $result = $optInfo->save();


                if ($result) {
//                    $return['result'] = true;
//                    $return['msg'] = '添加成功';
                    return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
                } else {
//                    $return['result'] = false;
//                    $return['msg'] = '添加失败';
                    return ApiResponse::makeResponse(false, $result, ApiResponse::INNER_ERROR);
                }
//                return $return;
                break;
            default:
                break;
        }
    }


}











