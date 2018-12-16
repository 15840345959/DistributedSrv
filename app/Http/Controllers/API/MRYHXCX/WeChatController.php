<?php
/**
 * Created by PhpStorm.
 * User: HappyQi
 * Date: 2018/1/11
 * Time: 9:43
 */

namespace App\Http\Controllers\API\MRYHXCX;

use App\Components\Mryh\MryhCouponManager;
use App\Components\Mryh\MryhUserManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhUser;
use App\Models\UserRel;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use App\Components\BusiWordManager;
use App\Components\InviteNumManager;
use App\Components\LoginManager;
use App\Components\RequestValidator;
use App\Components\UserManager;
use App\Components\UserTJManager;
use App\Components\Utils;
use App\Components\WeChatManager;
use App\Http\Controllers\Controller;
use App\Models\InviteCodeRecord;
use App\Models\Menu;
use App\Models\UserTJ;
use EasyWeChat\Kernel\Messages\Image;
use Illuminate\Support\Facades\Log;
use Leto\MiniProgramAES\WXBizDataCrypt;


class WechatController extends Controller
{

    //相关配置
    const ACCOUNT_CONFIG = "wechat.mini_program.mryh";     //配置文件位置
    const BUSI_NAME = "mryh";      //业务名称

    /*
     * 登录接口，根据code换取openid和session等信息
     *
     * By TerryQi
     *
     * 2018-07-04
     */
    public function login(Request $request)
    {
        $data = $request->all();
        //如果存在a_user_id，则获取用户a_user_id
        $a_user_id = null;
        if (array_key_exists('a_user_id', $data) && !Utils::isObjNull($data['a_user_id'])) {
            $a_user_id = $data['a_user_id'];
        }
        //合规校验account_type
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'code' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $app = app(self::ACCOUNT_CONFIG);
        $code = $data['code'];  //获取小程序code
        $ret = $app->auth->session($code);
        Utils::processLog(__METHOD__, '', " " . "code ret:" . json_encode($ret));
        //判断微信端返回信息，如果失败，则告知前端失败
        if (array_key_exists('errcode', $ret)) {
            return ApiResponse::makeResponse(false, $ret, ApiResponse::INNER_ERROR);
        }
        //如果成功获取openid和uniondid，则进行登录处理
        $data = array(
            'openid' => $ret['openid'],
            'session_key' => $ret['session_key'],
            'busi_name' => self::BUSI_NAME
        );
        //如果传入unionid，则赋值unionid
        if (array_key_exists('unionid', $ret)) {
            $data['unionid'] = $ret['unionid'];
        }
        Utils::processLog(__METHOD__, '', " " . "data:" . json_encode($data));
        //进行用户登录/注册
        $user = UserManager::login(Utils::ACCOUNT_TYPE_XCX, $data);
        Utils::processLog(__METHOD__, '', " " . "user:" . json_encode($user));
        $user->attach = $data;

        //获取用户的绑定状态//////////////////////////////////////////////////
        /*
         * 2018年11月22日增加逻辑，因为如果一个用户没有关注服务号或者没有通过APP进行登录，则小程序无法获取unionid，因此无绑定状态
         *
         * 但考虑后续业务需要通过unionid进行账号整合，所以要强烈推荐用户进行账户绑定动作
         *
         */
        $user->bind_flag = LoginManager::isBindUnionId($user->id, self::BUSI_NAME, Utils::ACCOUNT_TYPE_XCX);
        //////////////////////////////////////////////////////////////////////

        //获取用户的活动信息//////////////////////////////////////////////////////////重要 t_user_info表中存储用户的基本信息，mryh_user_info表中存储用户在每天一画中的业务信息
        $mryhUser = MryhUserManager::getByUserId($user->id);
        if (!$mryhUser) {
            $mryhUser = new MryhUser();
            $mryhUser->user_id = $user->id;
            $mryhUser->save();
            $user->new_flag = true;         //是否为新用户
        } else {
            $user->new_flag = false;        //是否为新用户
        }
        //////////////////////////////////////////////////////////////////////////////重要
        ///
        /// 2018-11-28日，进行逻辑调整，将邀请链接的能力放置于该段代码下
        ///
        /// By TerryQi
        /// 如果有a_user_id，代表有分享用户，所以需要记录分享级别
        if (!Utils::isObjNull($a_user_id)) {
            Utils::processLog(__METHOD__, '', " " . "记录分享用户级别:" . $a_user_id);
            $userRel = new UserRel();
            $userRel->a_user_id = $a_user_id;
            $userRel->b_user_id = $user->id;
            $userRel->busi_name = self::BUSI_NAME;
            $userRel->level = $user->new_flag ? '0' : '1';
            $userRel->save();
            if ($user->new_flag) {
                UserManager::addStatistics($userRel->a_user_id, 'yq_num', 1);
            } else {
                UserManager::addStatistics($userRel->a_user_id, 'rel_num', 1);
            }
            Utils::processLog(__METHOD__, '', " " . "用户关联信息:" . json_encode($userRel));
            //增加发送优惠券逻辑
            $sendResult = MryhCouponManager::send($userRel->a_user_id);
        }

        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 解密接口
     *
     * By TerryQi
     *
     * 2018-07-30
     */
    public function decryptData(Request $request)
    {
        $data = $request->all();
//        dd($data);
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'code' => 'required',
            'iv' => 'required',
            'encryptedData' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        //获取信息
        $code = $data['code'];
        $iv = base64_decode($data['iv']);
        $encryptedData = base64_decode($data['encryptedData']);
        $app = app(self::ACCOUNT_CONFIG);
        $result = UserManager::decryptData($app, $code, $iv, $encryptedData, 'MRYH_XCX_APPID');

        //返回解密信息
        if ($result != null) {
            return ApiResponse::makeResponse(true, $result, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, null, ApiResponse::INNER_ERROR);
        }
    }

    /*
     * 绑定unionid接口
     *
     * By TerryQi
     *
     * 2018-11-22
     *
     */
    public function bindUnionId(Request $request)
    {
        $data = $request->all();
//        dd($data);
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'code' => 'required',
            'iv' => 'required',
            'encryptedData' => 'required',
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //判断是否存在用户信息
        $user = UserManager::getById($data['user_id']);
        Utils::processLog(__METHOD__, '', " " . "user:" . json_encode($user));
        if (!$user) {
            return ApiResponse::makeResponse(false, "不存在用户信息", ApiResponse::INNER_ERROR);
        }
        //获取信息
        $code = $data['code'];
        $iv = base64_decode($data['iv']);
        $encryptedData = base64_decode($data['encryptedData']);
        $app = app(self::ACCOUNT_CONFIG);
        $result = UserManager::decryptData($app, $code, $iv, $encryptedData, 'MRYH_XCX_APPID');
        if ($result == null) {
            return ApiResponse::makeResponse(false, "解析消息失败", ApiResponse::INNER_ERROR);
        }
        Utils::processLog(__METHOD__, '', " " . "result json_decode:" . json_encode($result));
        $user_data = UserManager::convertDecryptDatatoUserData($result);    //转为数据库字段名字
        Utils::processLog(__METHOD__, '', " " . "user_date:" . json_encode($user_data));

        //此处一定要注意，避免token丢失
        $user = UserManager::getByIdWithToken($user->id);
        $user = UserManager::setInfo($user, $user_data);
        $user->save();
        Utils::processLog(__METHOD__, '', " " . "after set data user:" . json_encode($user));

        //进行unionid的绑定//////////////////////////////////////
        if (array_key_exists('unionid', $user_data) && !Utils::isObjNull($user_data['unionid'])) {
            $con_arr = array(
                've_value1' => $user_data['openid'],
                'user_id' => $user->id
            );
            $login = LoginManager::getListByCon($con_arr, false)->first();
            if ($login) {
                $login->ve_value2 = $user_data['unionid'];
                $login->save();
            } else {
                //2018-12-12日优化逻辑，可能存在没有登录信息的情况


            }

        }
        /////////////////////////////////////////////////////////
        //此处要注意，重新获取用户，去除token
        $user = UserManager::getById($user->id);
        $user->bind_flag = LoginManager::isBindUnionId($user->id, self::BUSI_NAME, Utils::ACCOUNT_TYPE_XCX);

        return ApiResponse::makeResponse(true, $user, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 判断用户是否绑定unionid
     *
     * By TerryQi
     *
     * 2018-11-22
     */
    public function isBindUnionId(Request $request)
    {
        $data = $request->all();
//        dd($data);
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $con_arr = array(
            'user_id' => $data['user_id'],
            'account_type' => Utils::ACCOUNT_TYPE_XCX,
            'busi_name' => self::BUSI_NAME
        );
        $login = LoginManager::getListByCon($con_arr, false)->first();
        //如果登录信息或不存在unionid
        if (!$login || Utils::isObjNull($login->ve_value2)) {
            return ApiResponse::makeResponse(true, false, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(true, true, ApiResponse::SUCCESS_CODE);
        }
    }

}