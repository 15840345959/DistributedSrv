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
use App\Components\LoginManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class UserController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $search_word = null;
        $type = null;
        $id = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('type', $data) && !Utils::isObjNull($data['type'])) {
            $type = $data['type'];
        }
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }

        $con_arr = array(
            'search_word' => $search_word,
            'id' => $id,
            'type' => $type
        );
        $users = UserManager::getListByCon($con_arr, true);
        foreach ($users as $user) {
            $user = UserManager::getInfoByLevel($user, '0');
        }
        return view('admin.user.index', ['datas' => $users, 'con_arr' => $con_arr]);
    }


    //设置用户状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
//        dd($data);
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数管理员id$id']);
        }
        $user = UserManager::getByIdWithToken($id);
        $user->status = $data['status'];
        $user->save();
        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }


    //设置用户类型
    public function setType(Request $request, $id)
    {
        $data = $request->all();
//        dd($data);
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数管理员id$id']);
        }
        $user = UserManager::getByIdWithToken($id);
        $user->type = $data['type'];
        $user->save();
        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }

    /*
    * 查看用户详情信息
    *
    * By TerryQi
    *
    * 2018-04-24
    *
    */
    public function info(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        //用户信息
        $user = UserManager::getById($data['id']);
        $user = UserManager::getInfoByLevel($user, '0');

        //配置登录信息
        $con_arr = array(
            'user_id' => $user->id
        );
        $logins = LoginManager::getListByCon($con_arr, false);
        foreach ($logins as $login) {
            $login = LoginManager::getInfoByLevel($login, '');
        }

        return view('admin.user.info', ['admin' => $admin, 'data' => $user, 'logins' => $logins]);
    }


    /*
     * 编辑用户信息
     *
     * By TerryQi
     *
     * 2018-11-13
     */
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
                $user = new User();
                if (array_key_exists('id', $data)) {
                    $user = UserManager::getById($data['id']);
                }
                return view('admin.user.edit', ['admin' => $admin, 'data' => $user, 'upload_token' => $upload_token]);
                break;
            case 'POST':
                $user = new User();
                $return = null;
                $user = new User();
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $user = UserManager::getByIdWithToken($data['id']);
                }
                $user = UserManager::setInfo($user, $data);
                $result = $user->save();
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
}