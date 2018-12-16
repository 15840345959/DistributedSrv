<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Vote\Team;

use Illuminate\Http\Request;
use App\Libs\ServerUtils;

class IndexController
{
    //首页
    public function index(Request $request)
    {
        $serverInfo = ServerUtils::getServerInfo();
        $team = $request->session()->get('team');
        return view('vote.team.index.index', ['serverInfo' => $serverInfo, 'team' => $team]);
    }

    //错误
    public function error500(Request $request)
    {
        $data = $request->all();
        $msg = null;
        if (array_key_exists('msg', $data)) {
            $msg = $data['msg'];
        }
        $team = $request->session()->get('team');
        return view('vote.team.error.500', ['msg' => $msg, 'team' => $team]);
    }
}