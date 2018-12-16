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
use App\Components\GZH\ReplyManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\GZH\Reply;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class ReplyController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $search_word = null;
        $busi_name = null;
        $type = null;
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
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
        $replys = ReplyManager::getListByCon($con_arr, true);
        foreach ($replys as $reply) {
            $reply = ReplyManager::getInfoByLevel($reply, '0');
        }
//        dd($replys);
        return view('admin.gzh.reply.index', ['datas' => $replys, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑公众号素材-get
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
        $reply = new Reply();
        if (array_key_exists('id', $data)) {
            $reply = ReplyManager::getById($data['id']);
        }
        $reply = ReplyManager::setInfo($reply, $data);
        $reply = ReplyManager::getInfoByLevel($reply, '');
        return view('admin.gzh.reply.edit', ['admin' => $admin, 'data' => $reply, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑公众号素材-post
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
        $reply = new Reply();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $reply = ReplyManager::getById($data['id']);
        }
        $reply = ReplyManager::setInfo($reply, $data);
        $reply->admin_id = $admin->id;      //记录管理员id
        $reply->save();

        return ApiResponse::makeResponse(true, $reply, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 删除公众号素材
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
        $reply = Reply::find($id);
        $reply->delete();

        return ApiResponse::makeResponse(true, "删除成功", ApiResponse::SUCCESS_CODE);
    }

}