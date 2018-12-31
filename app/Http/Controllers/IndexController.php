<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libs\ServerUtils;

class IndexController
{
    //首页-重定向到官网
    public function index(Request $request)
    {
        return redirect('http://s.isart.me/');
    }

}