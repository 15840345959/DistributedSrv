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
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class AdminController
{
    /*
     * 管理员首页
     *
     * By TerryQi
     *
     * 2018-07-02
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $search_word = null;
        $role = null;
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('role', $data) && !Utils::isObjNull($data['role'])) {
            $role = $data['role'];
        }
        $con_arr = array(
            'search_word' => $search_word,
            'role' => $role
        );
        $admins = AdminManager::getListByCon($con_arr, true);
        foreach ($admins as $admin) {
            $admin = AdminManager::getInfoByLevel($admin, '0');
        }
        return view('admin.admin.index', ['datas' => $admins, 'con_arr' => $con_arr]);
    }

    //删除管理员
    public function del(Request $request, $id)
    {
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数管理员id$id']);
        }
        $admin = AdminManager::getById($id);
        //如果不存在管理员，则返回成功
        if (!$admin) {
            return ApiResponse::makeResponse(true, "管理员不存在", ApiResponse::SUCCESS_CODE);
        }
        //非根管理员
        if ($admin->role != '0') {
            $admin->delete();
            return ApiResponse::makeResponse(true, "删除管理员成功", ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, "不允许删除超级管理员", ApiResponse::SUCCESS_CODE);
        }
    }

    //设置管理员状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数管理员id$id']);
        }
        $admin = AdminManager::getById($id);
        $admin->status = $data['status'];
        $admin->save();
        return ApiResponse::makeResponse(true, $admin, ApiResponse::SUCCESS_CODE);
    }


    //新建或编辑管理员-get
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin_b = new Admin();
        if (array_key_exists('id', $data)) {
            $admin_b = AdminManager::getById($data['id']);
        }
        $admin = $request->session()->get('admin');

        //只有根管理员有修改权限
        /*
         *   此处出现问题，找不到IndexController@error
         *   2018/04/20
         *
         * */
//        if (!($admin->role == '0')) {
//            return redirect()->action('/App/Http/Controllers/MBGL/Admin/IndexController@error', ['msg' => '合规校验失败，只有根级管理员有修改权限']);
//        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();
//        dd($admin_b);
        return view('admin.admin.edit', ['admin' => $admin, 'data' => $admin_b, 'upload_token' => $upload_token]);
    }


    //搜索管理员
    public function search(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
//        dd($data);
        $search_word = $data['search_word'];
        if (!array_key_exists('nick_name', $data)) {
            $data['nick_name'] = '';
        }
        $admins = AdminManager::searchByNameAndPhonenum($search_word);
        return view('admin.admin.index', ['admin' => $admin, 'datas' => $admins]);
    }


    //新建或编辑管理员->post
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = new Admin();
//        dd($data);
        //存在id是保存
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $admin = AdminManager::getById($data['id']);
            //保存查看手机号是否重复
            if (array_key_exists('phonenum', $data) && !Utils::isObjNull($data['phonenum'])) {
                $e_admin = AdminManager::getByPhonenum($data['phonenum']);

                if ($e_admin->id != $data['id']) {
                    return ApiResponse::makeResponse(false, "手机号重复", ApiResponse::PHONENUM_DUP);
                }
            }
        } else {
            //新建进行校验，手机号是否重复
            if (array_key_exists('phonenum', $data) && !Utils::isObjNull($data['phonenum'])) {
                $con_arr = array(
                    'phonenum' => $data['phonenum']
                );
                $e_admin = AdminManager::getListByCon($con_arr, false)->first();
                if ($e_admin) {
                    return ApiResponse::makeResponse(false, "手机号重复", ApiResponse::PHONENUM_DUP);
                }
            }
        }
        $admin = AdminManager::setInfo($admin, $data);
        //如果不存在id代表新建，则默认设置密码
        if (!array_key_exists('id', $data) || Utils::isObjNull($data['id'])) {
            $admin->password = 'afdd0b4ad2ec172c586e2150770fbf9e';  //该password为Aa123456的码
        }
        $admin->save();
        return ApiResponse::makeResponse(true, $data, ApiResponse::SUCCESS_CODE);
    }
    /*
      2/22谢晋 编辑
    */
    //修改个人资料get
    public function editMySelf(Request $request)
    {
        $admin = $request->session()->get('admin');
        $admins = AdminManager::getById($admin['id']);
        $upload_token = QNManager::uploadToken();
        $param = array(
            'data' => $admins,
            'upload_token' => $upload_token //定义了个人基本信息的 头像上传token
        );

        return view('admin.admin.editMySelf', $param);
    }

    //修改个人资料post
    public function editMySelfPost(Request $request)
    {
        $data = $request->all();
        $return = null;
        if (empty($data['password'])) {
            $admin = AdminManager::getById($data['id']);
            unset($data['password']);
            unset($data['new_password']);
            unset($data['confirm_password']);
            //判断电话号码是否唯一
            if ($data['phonenum'] != $admin['phonenum']) {
                $con_arr = array(
                    'phonenum' => $data['phonenum']
                );
                $result = AdminManager::getListByCon($con_arr, false)->first();
                if ($result) {
                    $return['result'] = false;
                    $return['msg'] = '个人信息修改失败,此电话号码已被注册';
                    return $return;
                }
            }
            //设置管理员信息
            $admin = AdminManager::setInfo($admin, $data);
            $result = $admin->save();
            if ($result) {
                //存入session
                $request->session()->put('admin', $admin);
                $return['result'] = true;
                $return['msg'] = '个人信息修改成功';
            } else {
                $return['result'] = false;
                $return['msg'] = '个人信息修改失败';
            }
        } else {
            $admin = AdminManager::getById($data['id']);
            unset($data['name']);
            unset($data['phonenum']);
            if ($data['password'] != $admin['password']) {
                $return['result'] = false;
                $return['msg'] = '修改密码失败,原密码输入不正确';
            } else {
                $data['password'] = $data['new_password'];
                unset($data['new_password']);
                unset($data['confirm_password']);
                $admin = AdminManager::setInfo($admin, $data);
                $result = $admin->save();
                if ($result) {
                    $return['result'] = true;
                    $return['msg'] = '修改密码成功,请重新登陆';
                } else {
                    $return['result'] = false;
                    $return['msg'] = '修改密码失败';
                }
            }
        }
        return $return;
    }


}