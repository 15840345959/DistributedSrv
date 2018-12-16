<?php
/**
 * File_Name:UserController.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 15:24
 */

namespace App\Http\Controllers\API;

use App\Components\LoginManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Libs\wxDecode\ErrorCode;
use App\Models\Login;
use App\Models\User;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use Qiniu\Auth;


class LoginController extends Controller
{

    /*
     * 通用登录接口
     *
     * By TerryQi
     *
     * 2018-06-13
     */
    public function login(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'account_type' => 'required',
            'busi_name' => 'required',
            've_value1' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user = null;
        //根据不同类型进行处理
        switch ($data['account_type']) {
            case "xcx":
                $user = $this->xcx_login($data);
                break;
        }

        //如果用户信息为空，则代表登录失败
        if (!$user) {
            return ApiResponse::makeResponse(false, "登录失败", ApiResponse::INNER_ERROR);
        }
        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 小程序登录处理流程
     *
     * By TerryQi
     *
     * 2018-06-13
     *
     */
    public function xcx_login($data)
    {
        $con_arr = array(
            'busi_name' => $data['busi_name'],
            've_value1' => $data['ve_value1']
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        //存在用户信息，则返回用户信息
        if ($login) {
            return UserManager::getByIdWithToken($login->user_id);
        }
        //不存在则进行注册
        $user = new User();
        $user->token = UserManager::getGUID();      //生成token
        $user->save();
        $data['user_id'] = $user->id;
        $login = new Login();
        $login = LoginManager::setInfo($login, $data);
        $login->save();
        return UserManager::getByIdWithToken($login->user_id);
    }

}