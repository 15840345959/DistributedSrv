<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\YSB;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\YSB\YSBUserManager;
use App\Components\YSB\YSBRuleManager;
use App\Http\Controllers\ApiResponse;
use App\Models\YSB\YSBUser;
use Illuminate\Http\Request;


class YSBUserController
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
        $ysbUsers = YSBUserManager::getListByCon($con_arr, true);
        foreach ($ysbUsers as $ysbUser) {
            $ysbUser = YSBUserManager::getInfoByLevel($ysbUser, '0');
        }
//        dd($ysbUsers);
        return view('admin.ysb.ysbUser.index', ['datas' => $ysbUsers, 'con_arr' => $con_arr]);
    }

}