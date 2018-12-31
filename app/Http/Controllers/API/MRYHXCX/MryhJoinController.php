<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\DateTool;
use App\Components\LoginManager;
use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhCouponManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhJoinOrderManager;
use App\Components\Mryh\MryhSettingManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\Mryh\MryhUserManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\Mryh\MryhJoinOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Yansongda\Pay\Pay;

class MryhJoinController
{

    /*
     * 根据条件获取用户参与活动列表
     *
     * By TerryQi
     *
     * 2018-08-17
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        //2018-12-17增加逻辑，即mryh_user_info表中，增加join_notify_num，该值代表需要提醒的参赛记录消息数，当用户成功或失败时，该字段++
        //当每次用户查看自己的参赛记录列表时，即将mryh_user_info的join_notify_num设置为0
        //前提是用户访问自己的参赛记录，即user_id==p_user_id
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])
            && array_key_exists('p_user_id', $data) && !Utils::isObjNull($data['p_user_id'])) {
            if ($data['user_id'] == $data['p_user_id']) {
                //将mryhUser的join_notify_num设置为0
                $mryhUser = MryhUserManager::getByUserId($data['user_id']);
                if ($mryhUser) {
                    $mryhUser->join_notify_num = 0;
                    $mryhUser->save();
                }
            }
        }

        //配置条件
        $user_id = null;
        if (array_key_exists('p_user_id', $data) && !Utils::isObjNull($data['p_user_id'])) {
            $user_id = $data['p_user_id'];
        }
        $game_status = null;
        if (array_key_exists('game_status', $data) && !Utils::isObjNull($data['game_status'])) {
            $game_status = $data['game_status'];
        }
        //game_id
        $game_id = null;
        if (array_key_exists('game_id', $data) && !Utils::isObjNull($data['game_id'])) {
            $game_id = $data['game_id'];
        }
        //获取信息级别
        $level = '0';
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }

        $con_arr = array(
            'user_id' => $user_id,
            'game_status' => $game_status,
            'game_id' => $game_id
        );
        $mryhJoins = MryhJoinManager::getListByCon($con_arr, false);
        foreach ($mryhJoins as $mryhJoin) {
            $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, $level);
        }
        return ApiResponse::makeResponse(true, $mryhJoins, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 通过优惠券参加活动
     *
     * By mtt
     *
     * 2018-08-15
     */
    public function joinByCoupon(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'game_id' => 'required',
            'userCoupon_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //判断用户是否存在
        $user = UserManager::getById($data['user_id']);
        Utils::processLog(__METHOD__, '', " " . "user:" . json_encode($user));
        if (!$user) {
            return ApiResponse::makeResponse(false, "未找到用户信息", ApiResponse::INNER_ERROR);
        }
        $mryhGame = MryhGameManager::getById($data['game_id']);
        Utils::processLog(__METHOD__, '', " " . "mryhGame:" . json_encode($mryhGame));
        if (!$mryhGame) {
            return ApiResponse::makeResponse(false, "未找到活动信息", ApiResponse::INNER_ERROR);
        }
        if ($mryhGame->join_status != '1') {
            return ApiResponse::makeResponse(false, "活动暂不允许参与", ApiResponse::INNER_ERROR);
        }
        //如果活动有人数控制
        if ($mryhGame->max_join_num != 0) {
            if ($mryhGame->join_num >= $mryhGame->max_join_num) {
                return ApiResponse::makeResponse(false, "活动已经达到上限", ApiResponse::INNER_ERROR);
            }
        }
        //用户是否已经参与过活动
        $con_arr = array(
            'user_id' => $user->id,
            'game_id' => $mryhGame->id
        );
        if (MryhJoinManager::getListByCon($con_arr, false)->count() > 0) {
            return ApiResponse::makeResponse(false, "已经参与过活动", ApiResponse::INNER_ERROR);
        }
        //优惠券类别的判断
        $mryhUserCoupon = MryhUserCouponManager::getById($data['userCoupon_id']);
        Utils::processLog(__METHOD__, '', " " . "mryhUserCoupon:" . json_encode($mryhUserCoupon));
        if (!$mryhUserCoupon) {
            return ApiResponse::makeResponse(false, "未找到优惠券信息", ApiResponse::INNER_ERROR);
        }
        if ($mryhUserCoupon->used_status != '0') {
            return ApiResponse::makeResponse(false, "优惠券无效", ApiResponse::INNER_ERROR);
        }
        if ($mryhUserCoupon->user_id != $user->id) {
            return ApiResponse::makeResponse(false, "优惠券归属错误", ApiResponse::INNER_ERROR);
        }
        //用到优惠券
        $mryhUserCoupon->used_status = '1';
        $mryhUserCoupon->save();
        Utils::processLog(__METHOD__, '', " " . "after used mryhUserCoupon:" . json_encode($mryhUserCoupon));
        $mryhJoin = new MryhJoin();
        $mryhJoin->user_id = $user->id;
        $mryhJoin->game_id = $mryhGame->id;
        $mryhJoin->userCoupon_id = $mryhUserCoupon->id;
        $mryhJoin->join_time = DateTool::getCurrentTime();
        $mryhJoin->save();

        //封装统计数据
        MryhGameManager::addStatistics($mryhGame->id, 'join_num', 1);

        return ApiResponse::makeResponse(true, $mryhJoin, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 免费参与活动
     *
     * By TerryQi
     *
     * 2018-12-18
     */
    public function joinByFree(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'game_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //判断用户是否存在
        $user = UserManager::getById($data['user_id']);
        Utils::processLog(__METHOD__, '', " " . "user:" . json_encode($user));
        if (!$user) {
            return ApiResponse::makeResponse(false, "未找到用户信息", ApiResponse::INNER_ERROR);
        }
        $mryhGame = MryhGameManager::getById($data['game_id']);
        Utils::processLog(__METHOD__, '', " " . "mryhGame:" . json_encode($mryhGame));
        if (!$mryhGame) {
            return ApiResponse::makeResponse(false, "未找到活动信息", ApiResponse::INNER_ERROR);
        }
        //获取0级信息
        $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '0');
        if ($mryhGame->is_free != true) {
            return ApiResponse::makeResponse(false, "该活动不允许免费参与", ApiResponse::INNER_ERROR);
        }
        if ($mryhGame->join_status != '1') {
            return ApiResponse::makeResponse(false, "活动暂不允许参与", ApiResponse::INNER_ERROR);
        }
        //如果活动有人数控制
        if ($mryhGame->max_join_num != 0) {
            if ($mryhGame->join_num >= $mryhGame->max_join_num) {
                return ApiResponse::makeResponse(false, "活动已经达到上限", ApiResponse::INNER_ERROR);
            }
        }
        //用户是否已经参与过活动
        $con_arr = array(
            'user_id' => $user->id,
            'game_id' => $mryhGame->id
        );
        if (MryhJoinManager::getListByCon($con_arr, false)->count() > 0) {
            return ApiResponse::makeResponse(false, "已经参与过活动", ApiResponse::INNER_ERROR);
        }

        $mryhJoin = new MryhJoin();
        $mryhJoin->user_id = $user->id;
        $mryhJoin->game_id = $mryhGame->id;
        $mryhJoin->join_time = DateTool::getCurrentTime();
        $mryhJoin->save();

        //封装统计数据
        MryhGameManager::addStatistics($mryhGame->id, 'join_num', 1);

        return ApiResponse::makeResponse(true, $mryhJoin, ApiResponse::SUCCESS_CODE);

    }

    /*
     * 获取每天一画的证书接口
     *
     * By TerryQi
     *
     * 2018-10-13
     */
    public function getCert(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'join_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $mryhJoin = MryhJoinManager::getById($data['join_id']);
        $mryhJoin = MryhJoinManager::getInfoByLevel($mryhJoin, '01');
        if (!$mryhJoin) {
            return ApiResponse::makeResponse(false, "没有找到参与信息", ApiResponse::INNER_ERROR);
        }
        //设置参与编码规则，规则为 mryh-（100000+join_id），例如MRYH-1000021
        $mryhJoin_code = 'MRYH-' . strval((100000 + $mryhJoin->id));
        $mryhSetting = MryhSettingManager::getListByCon(['status' => '1'], false)->first();
        if (!$mryhSetting || Utils::isObjNull($mryhSetting->gzh_ewm)) {
            return ApiResponse::makeResponse(false, "系统缺少配置信息", ApiResponse::INNER_ERROR);
        }
        //获取证书图片
        $name = $mryhJoin->user->nick_name;
        $game_name = $mryhJoin->game->name;
        $info_arr = [
            'name' => $name,
            'game_name' => $game_name,
            'cert_no' => $mryhJoin_code,
            'date' => DateTool::getYMDChi($mryhJoin->game->game_end_time)
        ];
        //生成证书
        $cert_path = MryhJoinManager::generateCert($info_arr);

        $ret_info = array(
            'join_code' => $mryhJoin_code,
            'gzh_ewm' => URL::asset('/img/isart_fwh_ewm.jpg'),
            'intro_text' => '请关注公众号，回复参赛编号获取证书',
            'cert_img_url' => URL::asset('/img/mryh/cert/' . basename($cert_path))
        );

        return ApiResponse::makeResponse(true, $ret_info, ApiResponse::SUCCESS_CODE);
    }

}





