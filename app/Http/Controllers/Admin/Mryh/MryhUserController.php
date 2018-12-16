<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Mryh\MryhUserManager;

use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhUser;
use Illuminate\Http\Request;


class MryhUserController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //配置条件
        $user_id = null;
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'user_id' => $user_id,
        );
        $mryhUsers = MryhUserManager::getListByCon($con_arr, true);
        foreach ($mryhUsers as $mryhUser) {
            $mryhUser = MryhUserManager::getInfoByLevel($mryhUser, '');
        }
//        dd($mryhUsers);
        return view('admin.mryh.mryhUser.index', ['datas' => $mryhUsers, 'con_arr' => $con_arr]);
    }

}