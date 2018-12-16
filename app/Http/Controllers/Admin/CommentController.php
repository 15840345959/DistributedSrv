<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\CommentManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class CommentController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
        //       dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $user_id = null;
        $f_table = null;
        $f_id = null;
        $audit_status = null;
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('f_table', $data) && !Utils::isObjNull($data['f_table'])) {
            $f_table = $data['f_table'];
        }
        if (array_key_exists('f_id', $data) && !Utils::isObjNull($data['f_id'])) {
            $f_id = $data['f_id'];
        }
        if (array_key_exists('audit_status', $data) && !Utils::isObjNull($data['audit_status'])) {
            $f_id = $data['audit_status'];
        }
        $con_arr = array(
            'user_id' => $user_id,
            'f_table' => $f_table,
            'f_id' => $f_id,
            'audit_status' => $audit_status
        );
        $comments = CommentManager::getListByCon($con_arr, true);
        foreach ($comments as $comment) {
            $comment = CommentManager::getInfoByLevel($comment, '01');
        }
        return view('admin.comment.index', ['datas' => $comments, 'con_arr' => $con_arr]);
    }

    /*
     * 设置评论状态
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
        $comment = CommentManager::getById($data['id']);
        $comment = CommentManager::setInfo($comment, $data);
        $comment->save();
        return ApiResponse::makeResponse(true, $comment, ApiResponse::SUCCESS_CODE);
    }

}