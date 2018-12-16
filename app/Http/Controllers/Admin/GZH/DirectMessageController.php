<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\GZH;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\QNManager;
use App\Components\GZH\DirectMessageManager;
use App\Components\Utils;
use App\Components\GZH\WeChatManager;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\GZH\DirectMessage;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class DirectMessageController
{

    /*
     * 添加、编辑公众号素材-get
     *
     * 其中，必须传入busi_name
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        //必须传入busi_name
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'busi_name' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $directMessage = new DirectMessage();
        if (array_key_exists('id', $data)) {
            $directMessage = DirectMessageManager::getById($data['id']);
        }
        $directMessage = DirectMessageManager::setInfo($directMessage, $data);
        $directMessage = DirectMessageManager::getInfoByLevel($directMessage, '');
        return view('admin.gzh.directMessage.edit', ['admin' => $admin, 'data' => $directMessage, 'upload_token' => $upload_token]);
    }

    /*
     * 添加、编辑公众号素材-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     * 其中busi_name参看Utils中的BUSI_NAME_VAL值，此为业务名称
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $directMessage = new DirectMessage();
        //必须传入busi_name
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'to_openid' => 'required',
            'content' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user_openid = $data['to_openid'];
        //进行消息发送
        $app = app(Utils::OFFICIAL_ACCOUNT_CONFIG_VAL[$data['busi_name']]);
        $wechat_user = $app->user->get($user_openid);        //通过用户openid获取信息
        Utils::processLog(__METHOD__, '', " " . 'wechat_user:' . json_encode($wechat_user));
        //如果未关注
        if ($wechat_user['subscribe'] == '0') {
            return ApiResponse::makeResponse(false, "该用户未关注公众号，无法推送消息", ApiResponse::INNER_ERROR);
        }
        //进行消息发送
        $directMessage = DirectMessageManager::setInfo($directMessage, $data);
        $directMessage->admin_id = $admin->id;      //记录管理员id
        $result = WeChatManager::sendDirectMessage($directMessage, $app);
        if ($result['errcode'] != '0') {
            return ApiResponse::makeResponse(false, "该用户超过48小时没有互动，无法推送消息", ApiResponse::INNER_ERROR);
        }
        //如果发送成功才进行保存
        $directMessage->save();

        return ApiResponse::makeResponse(true, $directMessage, ApiResponse::SUCCESS_CODE);
    }

}