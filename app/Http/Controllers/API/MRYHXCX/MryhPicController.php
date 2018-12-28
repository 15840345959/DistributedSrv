<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhFriendManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinArticleManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhPicManager;
use App\Components\Mryh\MryhSettingManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\Mryh\MryhUserManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Yansongda\Pay\Pay;

//每天一画的海报相关操作Controller
class MryhPicController
{

    //相关配置
    const ACCOUNT_CONFIG = "wechat.mini_program.mryh";     //配置文件位置
    const BUSI_NAME = "mryh";      //业务名称

    /*
     * 生成分享海报
     *
     * By TerryQi
     *
     * 2018-10-11
     */
    public function share(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //获取用户id
        $user_id = $data['user_id'];
        //获取用户信息
        $user = UserManager::getById($user_id);
        if (!$user) {
            return ApiResponse::makeResponse(false, "用户不存在", ApiResponse::INNER_ERROR);
        }
        //如果用户重要信息为空，则返回失败
        if (Utils::isObjNull($user->nick_name) || Utils::isObjNull($user->avatar)) {
            return ApiResponse::makeResponse(false, "用户昵称或者头像为空", ApiResponse::INNER_ERROR);
        }

        //生成二维码
//        $app = app(self::ACCOUNT_CONFIG);
//        $response = $app->app_code->getUnlimit('xcxhb', ["page" => 'pages/index?a_user_id=' . $user_id]);
////        $response = $app->app_code->get('pages/message/message?a_user_id=' . $user_id);
//        $response->saveAs(public_path('img'), 'xcx_code_user_' . $user_id . '.png');

        //生产海报
        $info_arr = [
            'name' => $user->nick_name,
            'avatar' => $user->avatar,
            'ewm_code' => public_path('img/mryh/haibao/mryh_xcx_ewm.jpg')           //每天一画小程序需的二维码
        ];
        $file_name = MryhPicManager::generateHaiBao($info_arr);
        $img_url = URL::asset('/img/mryh/haibao/' . $file_name);

        return ApiResponse::makeResponse(true, $img_url, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 生成证书
     *
     * By TerryQi
     *
     * 2018-10-14
     *
     */
    public function cert(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'join_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

//        $mryhJoin = MryhJoinManager::getById($data['join_id']);
//        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '01');

        $info_arr = array(
            'cert_no' => 'MRYH-100003',
            'name' => 'TerryQi',
            'game_name' => '每天一画第22期',
            'date' => '2018年10月14日'
        );
        $cert_path = MryhJoinManager::generateCert($info_arr);

        return ApiResponse::makeResponse(true, $cert_path, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 生成活动分享好友的海报
     *
     * By TerryQi
     *
     * 2018-12-25
     */
    public function shareGame(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'id' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $mryhGame = MryhGameManager::getById($data['id']);
        Utils::processLog(__METHOD__, '', '分享活动信息 mryhGame：' . json_encode($mryhGame));
        if (!$mryhGame) {
            return ApiResponse::makeResponse(false, "未找到活动", ApiResponse::INNER_ERROR);
        }
        $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '');
        //变为https的图片
        $https_share_img = Utils::downloadFile($mryhGame->share_img, public_path('img/mryh/haibao'), Utils::generateTradeNo() . '.png');
        $mryhGame->https_share_img = URL::asset('/img/mryh/haibao/' . $https_share_img);
        Utils::processLog(__METHOD__, '', '下载分享图片并转换为https：' . json_encode($https_share_img));
        //获取分享海报
        $app = app(self::ACCOUNT_CONFIG);
        Utils::processLog(__METHOD__, '', '完成app配置');
        $file_name = MryhPicManager::generateGameHaiBao($mryhGame->id, $data['user_id'], $app);

        $img_url = URL::asset('/img/nas/' . $file_name);
        Utils::processLog(__METHOD__, '', '海报样式 img_url：' . json_encode($img_url));
        $mryhGame->https_share_haibao = $img_url;
        unset($mryhGame->intro_html);

        return ApiResponse::makeResponse(true, $mryhGame, ApiResponse::SUCCESS_CODE);
    }
}





