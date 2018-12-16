<?php

/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2017/10/3
 * Time: 0:38
 */

namespace App\Http\Controllers\Vote\Team;

use App\Components\AdminManager;
use App\Components\Vote\VoteTeamManager;
use App\Libs\CommonUtils;
use App\Models\Doctor;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use App\Models\Admin;

class LoginController
{
    public function login(Request $request)
    {
        $method = $request->method();

        switch ($method) {
            case 'GET':
                return view('vote.team.login.index', ['msg' => '']);
                break;
            case "POST":
                $data = $request->all();

                //参数校验
                $requestValidationResult = RequestValidator::validator($request->all(), [
                    'phonenum' => 'required',
                    'password' => 'required'
                ]);
                if ($requestValidationResult !== true) {
                    return view('vote.team.login.index', '请输入手机号和密码');
                }
                $con_arr = array(
                    'phonenum' => $data['phonenum'],
                    'password' => $data['password']
                );
                $team = VoteTeamManager::getListByCon($con_arr, false)->first();
                //登录失败
                if ($team == null) {
                    return view('vote.team.login.index', ['msg' => '手机号或密码错误']);
                }
                if ($team['status'] == '0') {
                    return view('vote.team.login.index', ['msg' => '该账号已被禁用']);
                }
                $request->session()->put('team', $team);//写入session
                return redirect()->route('team.index');//跳转至地推团队后台首页
                break;
        }
    }

    //注销登录
    public function loginout(Request $request)
    {
        //清空session
        $request->session()->remove('team');
        return redirect()->route('team.login');
    }

}