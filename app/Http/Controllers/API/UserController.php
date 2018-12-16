<?php
/**
 * File_Name:UserController.php
 * Author: leek
 * Date: 2017/8/23
 * Time: 15:24
 */

namespace App\Http\Controllers\API;

use App\Components\AdminManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Libs\wxDecode\ErrorCode;
use Illuminate\Http\Request;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Storage;
use Qiniu\Auth;


class UserController extends Controller
{

    /*
     * 根据id更新用户信息
     *
     * @request id:用户id
     *
     * By TerryQi
     *
     */
    public function updateById(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if (!$requestValidationResult) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //更新用户信息
        $user = UserManager::getByIdWithToken($data['user_id']);
        $user = UserManager::setInfo($user, $data);
        $user->save();
        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 根据id获取用户信息
     *
     * @request id：用户id
     *
     * By TerryQi
     *
     * 2017-09-28
     *
     */
    public function getById(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user = UserManager::getById($data['id']);
        if ($user) {
            return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, ApiResponse::$errorMassage[ApiResponse::NO_USER], ApiResponse::NO_USER);
        }
    }

    /*
     * 根据id获取用户信息带token
     *
     * @request user_id：本人用户信息
     *
     * By TerryQi
     *
     * 2017-09-28
     *
     */
    public function getByIdWithToken(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user = UserManager::getByIdWithToken($data['user_id']);
        if ($user) {
            return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, ApiResponse::$errorMassage[ApiResponse::NO_USER], ApiResponse::NO_USER);
        }
    }

    /*
     * 获取七牛token
     *
     * By TerryQi
     *
     * 2018-11-19
     */

    public function getQiniuToken(Request $request)
    {
        $data = $request->all();

        $disk = Storage::disk('qiniu');
        $token = $disk->getUploadToken();

        return ApiResponse::makeResponse(true, $token, ApiResponse::SUCCESS_CODE);
    }
}