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
use App\Components\UserRelManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\AD;
use Illuminate\Http\Request;

class UserRelController
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
        $a_user_id = null;
        $b_user_id = null;
        $busi_name = null;
        $level = null;

        if (array_key_exists('a_user_id', $data) && !Utils::isObjNull($data['a_user_id'])) {
            $a_user_id = $data['a_user_id'];
        }
        if (array_key_exists('b_user_id', $data) && !Utils::isObjNull($data['b_user_id'])) {
            $b_user_id = $data['b_user_id'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }
        $con_arr = array(
            'a_user_id' => $a_user_id,
            'b_user_id' => $b_user_id,
            'busi_name' => $busi_name,
            'level' => $level
        );
        $userRels = UserRelManager::getListByCon($con_arr, true);
        foreach ($userRels as $userRel) {
            $userRel = UserRelManager::getInfoByLevel($userRel, '');
        }
        return view('admin.userRel.index', ['admin' => $admin, 'datas' => $userRels, 'con_arr' => $con_arr]);
    }

}





