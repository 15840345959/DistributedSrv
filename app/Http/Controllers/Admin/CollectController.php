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
use App\Components\CollectManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Collect;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class CollectController
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
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('f_table', $data) && !Utils::isObjNull($data['f_table'])) {
            $f_table = $data['f_table'];
        }
        if (array_key_exists('f_id', $data) && !Utils::isObjNull($data['f_id'])) {
            $f_id = $data['f_id'];
        }
        $con_arr = array(
            'user_id' => $user_id,
            'f_table' => $f_table,
            'f_id' => $f_id
        );
        $collects = CollectManager::getListByCon($con_arr, true);
        foreach ($collects as $collect) {
            $collect = CollectManager::getInfoByLevel($collect, '01');
        }
        return view('admin.collect.index', ['datas' => $collects, 'con_arr' => $con_arr]);
    }

}