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
use App\Components\QNManager;
use App\Components\Mryh\MryhCertSendManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Mryh\MryhGift;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class MryhCertSendController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $con_arr = array();
        $mryhCertSends = MryhCertSendManager::getListByCon($con_arr, true);
        foreach ($mryhCertSends as $mryhCertSend) {
            $mryhCertSend = MryhCertSendManager::getInfoByLevel($mryhCertSend, '0');
        }
//        dd($mryhCertSends);
        return view('admin.mryh.mryhCertSend.index', ['datas' => $mryhCertSends, 'con_arr' => $con_arr]);
    }
}