<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Http\Controllers\Admin;

use App\Components\ADManager;
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\AD;
use Illuminate\Http\Request;

class ADController
{

    /*
     * 首页
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
        //相关搜素条件
        $search_word = null;    //搜索条件
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        $con_arr = array(
            'search_word' => $search_word
        );
        $ads = ADManager::getListByCon($con_arr, true);
        return view('admin.ad.index', ['admin' => $admin, 'datas' => $ads, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑广告图-get
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $ads = new AD();
        if (array_key_exists('id', $data)) {
            $ads = ADManager::getById($data['id']);
        }
        return view('admin.ad.edit', ['admin' => $admin, 'data' => $ads, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑广告图-post
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $ad = new AD();
        $return = null;
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $ad = ADManager::getById($data['id']);
        }
        $ad = ADManager::setInfo($ad, $data);
        $result = $ad->save();
        if ($result) {
            return ApiResponse::makeResponse(true, "添加成功", ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, "添加失败", ApiResponse::INNER_ERROR);
        }
    }

    /*
     * 设置广告状态
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数轮播图id$id']);
        }
        $ad = ADManager::getById($data['id']);
        $ad->status = $data['status'];
        $ad->save();
        return ApiResponse::makeResponse(true, $ad, ApiResponse::SUCCESS_CODE);
    }

    /*
     * 删除广告图
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数广告id$id']);
        }
        $ad = AD::find($id);
        $ad->delete();
        return ApiResponse::makeResponse(true, $ad, ApiResponse::SUCCESS_CODE);
    }


}





