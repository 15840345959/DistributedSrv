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
use App\Components\GuanZhuManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\GuanZhu;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class GuanZhuController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
        //       dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $id = null;
        $fan_user_id = null;
        $gz_user_id = null;
        $busi_name = null;
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $id = $data['id'];
        }
        if (array_key_exists('fan_user_id', $data) && !Utils::isObjNull($data['fan_user_id'])) {
            $fan_user_id = $data['fan_user_id'];
        }
        if (array_key_exists('gz_user_id', $data) && !Utils::isObjNull($data['gz_user_id'])) {
            $gz_user_id = $data['gz_user_id'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }

        $con_arr = array(
            'id' => $id,
            'gz_user_id' => $gz_user_id,
            'fan_user_id' => $fan_user_id,
            'busi_name' => $busi_name
        );
        $guanzhus = GuanZhuManager::getListByCon($con_arr, true);
        foreach ($guanzhus as $guanzhu) {
            $guanzhu = GuanZhuManager::getInfoByLevel($guanzhu, '01');
        }
        return view('admin.guanzhu.index', ['datas' => $guanzhus, 'con_arr' => $con_arr]);
    }

}