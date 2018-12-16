<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Shop;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Shop\ShopUserManager;
use App\Components\Shop\ShopRuleManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Shop\ShopUser;
use Illuminate\Http\Request;


class ShopUserController
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
        $shopUsers = ShopUserManager::getListByCon($con_arr, true);
        foreach ($shopUsers as $shopUser) {
            $shopUser = ShopUserManager::getInfoByLevel($shopUser, '');
        }
//        dd($shopUsers);
        return view('admin.shop.shopUser.index', ['datas' => $shopUsers, 'con_arr' => $con_arr]);
    }

}