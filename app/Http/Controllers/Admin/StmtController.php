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
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class StmtController
{
    /*
     * 综合统计首页
     *
     * By TerryQi
     *
     * 2018-07-02
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');

        return view('admin.admin.index', ['datas' => $data]);
    }

}