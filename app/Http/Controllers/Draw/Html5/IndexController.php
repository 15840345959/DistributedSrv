<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\Draw\Html5;

use App\Components\ADManager;
use App\Components\AdminManager;
use App\Components\BusiWordManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Vote\VoteActivityManager;
use App\Components\Vote\VoteADManager;
use App\Components\Vote\VoteGuanZhuManager;
use App\Components\Vote\VoteOrderManager;
use App\Components\Vote\VoteRecordManager;
use App\Components\Vote\VoteUserManager;
use App\Http\Controllers\Admin\Vote\VoteOrderController;
use App\Libs\CommonUtils;
use App\Models\Vote\VoteOrder;
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
     * 画布首页
     *
     * By TerryQi
     *
     * 2018-07-18
     */
    public function canvas(Request $request)
    {
        $data = $request->all();

        return view('draw.html5.canvas', []);
    }


}
