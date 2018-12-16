<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\IsartShop\Html5;

use App\Components\ADManager;
use App\Components\AdminManager;
use App\Components\BusiWordManager;
use App\Components\QNManager;
use App\Components\Shop\ShopUserManager;
use App\Components\UserManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteADManager;
use App\Components\Vote\VoteGuanZhuManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteRecordManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\Admin\Vote\VoteOrderController;
use App\Libs\CommonUtils;
use App\Models\Shop\ShopUser;
use App\Models\Vote\VoteActivity;
use App\Models\Vote\VoteOrder;
use App\Models\Vote\VoteTeam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use App\Models\Admin;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Support\Facades\Log;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;

class IndexController
{

    //相关配置
    const BUSI_NAME = "isart";      //业务名称

    /*
     * 投票大赛首页
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function index(Request $request)
    {
        $data = $request->all();
        //是否为本地调测，如果本地调测，则默认用户信息
        if (env('FWH_LOCAL_DEBUG')) {
            $user = UserManager::getById('11');
        } else {
            $session_val = session('wechat.oauth_user'); // 拿到授权用户资料
            $user_data = UserManager::convertSessionValToUserData($session_val, self::BUSI_NAME);
            //进行用户登录
            $user = UserManager::login(Utils::ACCOUNT_TYPE_FWH, $user_data);
        }
        $user = UserManager::getByIdWithToken($user->id);
        //是否有小艺商城的用户
        $con_arr = array(
            'user_id' => $user->id
        );
        $shop_user = ShopUserManager::getListByCon($con_arr, false);
        //如果不存在小艺商城的用户，则新建用户
        if (!$shop_user) {
            $shop_user = new ShopUser();
            $shop_user->user_id = $user->id;
            $shop_user->save();
        }

        //调试标识
        $debug = env("ISART_SHOP_DEBUG_FLAG", false);
        $debug ? $debug = "true" : $debug = "false";        //直接的boolean型不可以，需要变为字符串才可以比对
//        dd($user);
        return view('isartshop.html5.index', ['user' => $user, 'debug' => $debug]);
    }

}
