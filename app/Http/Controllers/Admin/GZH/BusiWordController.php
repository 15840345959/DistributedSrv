<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\GZH;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\GZH\BusiWordManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\GZH\BusiWord;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class BusiWordController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $busi_name = null;
        $type = null;
        if (array_key_exists('type', $data) && !Utils::isObjNull($data['type'])) {
            $type = $data['type'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        $con_arr = array(
            'type' => $type,
            'busi_name' => $busi_name
        );
//        dd($con_arr);
        $busiWords = BusiWordManager::getListByCon($con_arr, true);
        foreach ($busiWords as $busiWord) {
            $busiWord = BusiWordManager::getInfoByLevel($busiWord, '0');
        }
//        dd($busiWords);
        return view('admin.gzh.busiWord.index', ['datas' => $busiWords, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑业务话术-get
     *
     * 其中，必须传入busi_name
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //必须传入busi_name
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'busi_name' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $busiWord = new BusiWord();
        if (array_key_exists('id', $data)) {
            $busiWord = BusiWordManager::getById($data['id']);
        }
        $busiWord = BusiWordManager::setInfo($busiWord, $data);
        $busiWord = BusiWordManager::getInfoByLevel($busiWord, '');
        return view('admin.gzh.busiWord.edit', ['admin' => $admin, 'data' => $busiWord, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑业务话术-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     * 其中busi_name参看Utils中的BUSI_NAME_VAL值，此为业务名称
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $busiWord = new BusiWord();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $busiWord = BusiWordManager::getById($data['id']);
        }
        $busiWord = BusiWordManager::setInfo($busiWord, $data);
        $busiWord->admin_id = $admin->id;      //记录管理员id
        $busiWord->save();

        return ApiResponse::makeResponse(true, $busiWord, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 删除业务话术
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function del(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return ApiResponse::makeResponse(false, "删除失败", ApiResponse::INNER_ERROR);
        }
        $busiWord = BusiWord::find($id);
        $busiWord->delete();

        return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
    }

}