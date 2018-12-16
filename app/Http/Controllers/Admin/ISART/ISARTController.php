<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Http\Controllers\Admin\ISART;

use App\Components\ADManager;
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\AD;
use Illuminate\Http\Request;

class ISARTController
{

    /*
     * 配置信息
     *
     * By TerryQi
     *
     * 2018-7-9
     */
    public static function info(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
//        dd($data);
        return view('admin.base.isart.info', ['admin' => $admin]);
    }

}





