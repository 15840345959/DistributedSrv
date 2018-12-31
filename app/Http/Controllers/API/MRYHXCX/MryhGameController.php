<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API\MRYHXCX;


use App\Components\Mryh\MryhADManager;
use App\Components\Mryh\MryhGameManager;
use App\Components\Mryh\MryhJoinManager;
use App\Components\Mryh\MryhUserCouponManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhJoin;
use App\Models\XCXForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class MryhGameController
{
    /*
     * 根据条件获取列表
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();

        $type = null;   //活动类型
        $creator_type = null;   //创建者类型
        $creator_id = null;    //创建者id
        $show_status = null;       //展示状态
        $game_status = null;    //活动状态
        $join_status = null;    //可参与状态

        //用户id
        $user_id = null;    //用户id

        if (array_key_exists('type', $data) && !Utils::isObjNull($data['type'])) {
            $type = $data['type'];
        }
        if (array_key_exists('creator_type', $data) && !Utils::isObjNull($data['creator_type'])) {
            $creator_type = $data['creator_type'];
        }
        if (array_key_exists('creator_id', $data) && !Utils::isObjNull($data['creator_id'])) {
            $creator_id = $data['creator_id'];
        }
        if (array_key_exists('game_status', $data) && !Utils::isObjNull($data['game_status'])) {
            $game_status = $data['game_status'];
        }
        if (array_key_exists('join_status', $data) && !Utils::isObjNull($data['join_status'])) {
            $join_status = $data['join_status'];
        }
        if (array_key_exists('show_status', $data) && !Utils::isObjNull($data['show_status'])) {
            $show_status = $data['show_status'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }

        //配置搜索条件
        $con_arr = array(
            'status' => '1',
            'type' => $type,
            'creator_type' => $creator_type,
            'creator_id' => $creator_id,
            'game_status' => $game_status,
            'show_status' => $show_status,
            'join_status' => $join_status
        );

        $mryhGames = MryhGameManager::getListByCon($con_arr, true);
        foreach ($mryhGames as $mryhGame) {
            unset($mryhGame->intro_html);
            $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '');
            //用户是否参与活动
            $con_arr = array(
                'user_id' => $user_id,
                'game_id' => $mryhGame->id
            );
            if (MryhJoinManager::getListByCon($con_arr, false)->count() > 0) {
                $mryhGame->join_flag = true;
            } else {
                $mryhGame->join_flag = false;
            }
        }

        return ApiResponse::makeResponse(true, $mryhGames, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 根据id获取详细信息
     *
     * By TerryQi
     *
     * 2018-06-11
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
        $mryhGame = MryhGameManager::getById($data['id']);
        $mryhGame = MryhGameManager::getInfoByLevel($mryhGame, '');
        //如果存在user_id且user_id不为空
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
            //用户是否参与过活动
            $con_arr = array(
                'user_id' => $user_id,
                'game_id' => $mryhGame->id
            );
            $mryhJoin = MryhJoinManager::getListByCon($con_arr, false);
            if ($mryhJoin->count() > 0) {
                $mryhJoin->join_flag = true;
            } else {
                $mryhJoin->join_flag = false;
            }

            //优惠券信息
            $con_arr = array(
                'user_id' => $user_id,
                'used_status' => '0',
            );
            $mryhUserCoupon = MryhUserCouponManager::getListByCon($con_arr, false)->first();
            //存在有效优惠券
            if ($mryhUserCoupon) {
                $mryhUserCoupon = MryhUserCouponManager::getInfoByLevel($mryhUserCoupon, '1');
                //存在优惠券标识为true
                $mryhGame->has_coupon_flag = true;
                //返回优惠券信息
                $mryhGame->coupon = $mryhUserCoupon;
            }
        }
        return ApiResponse::makeResponse(true, $mryhGame, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 用户是否参与了一个活动
     *
     * By TerryQi
     *
     * 2018-10-09
     *
     */
    public function isUserJoin(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'game_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $con_arr = array(
            'user_id' => $data['user_id'],
            'game_id' => $data['game_id']
        );

        $mryhJoin = MryhJoinManager::getListByCon($con_arr, false);

        if ($mryhJoin->count() > 0) {
            return ApiResponse::makeResponse(true, $mryhJoin, ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, "用户还未参与活动", ApiResponse::SUCCESS_CODE);
        }
    }


    /*
     * 分享活动，记录form_id，用于下发小程序消息
     *
     * By TerryQi
     *
     * 2018-11-26
     */
    public function share(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'form_id' => 'required',
            'game_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        $xcxForm = new XCXForm();
        $xcxForm->user_id = $data['user_id'];
        $xcxForm->busi_name = Utils::BUSI_NAME_MRYH;
        $xcxForm->form_id = $data['form_id'];
        $xcxForm->total_num = 1;
        $xcxForm->f_table = Utils::F_TABLE_MRYH_GAME;
        $xcxForm->f_id = $data['game_id'];
        $xcxForm->save();

        return ApiResponse::makeResponse(true, "分享活动成功", ApiResponse::SUCCESS_CODE);
    }


    /*
     * 获取分享信息-主要是将图片设置为https的
     *
     * By TerryQi
     *
     * 2018-12-05
     */
    public function getShareInfo(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        //获取活动信息
        $mryhGame = MryhGameManager::getById($data['id']);
        if (!$mryhGame) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::INNER_ERROR);
        }
        Utils::processLog(__METHOD__, '', '活动信息 mryhGame：' . json_encode($mryhGame));
        //变为https的图片
        $https_share_img = Utils::downloadFile($mryhGame->share_img, public_path('img/mryh/haibao'), Utils::generateTradeNo() . '.png');
        $mryhGame->https_share_img = URL::asset('/img/mryh/haibao/' . $https_share_img);

        return ApiResponse::makeResponse(true, $mryhGame, ApiResponse::SUCCESS_CODE);
    }

}





