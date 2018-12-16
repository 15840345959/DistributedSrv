<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/20
 * Time: 10:50
 */

namespace App\Http\Controllers\Admin;


use App\Components\ArticleTypeManager;
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\ArticleType;
use Illuminate\Http\Request;

class ArticleTypeController
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
        $articleTypes = ArticleTypeManager::getListByCon($con_arr, true);
        foreach ($articleTypes as $articleType) {
            $articleType = ArticleTypeManager::getInfoByLevel($articleType, '');
        }

        return view('admin.articleType.index', ['admin' => $admin, 'datas' => $articleTypes, 'con_arr' => $con_arr]);
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
                $articleType = new ArticleType();
                if (array_key_exists('id', $data)) {
                    $articleType = ArticleTypeManager::getById($data['id']);
                }
                return view('admin.articleType.edit', ['admin' => $admin, 'data' => $articleType, 'upload_token' => $upload_token]);
                break;
            case 'POST':
                $articleType = new ArticleType();
                $return = null;
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $articleType = ArticleTypeManager::getById($data['id']);
                }
                if (!$articleType) {
                    $articleType = new ArticleType();
                }
                $articleType = ArticleTypeManager::setInfo($articleType, $data);
                $result = $articleType->save();
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
        $articleType = ArticleTypeManager::getById($data['id']);
        $articleType = ArticleTypeManager::setInfo($articleType, $data);
        $articleType->save();
        return ApiResponse::makeResponse(true, $articleType, ApiResponse::SUCCESS_CODE);
    }


}











