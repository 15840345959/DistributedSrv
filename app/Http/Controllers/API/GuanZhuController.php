<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\GuanZhuManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\GuanZhu;
use Illuminate\Http\Request;

class GuanZhuController
{
    /*
     * 关注/取消关注接口
     *
     * By TerryQi
     *
     * 218-06-21
     *
     * opt: 0代表取消关注 1:代表关注
     */
    public function setGuanZhu(Request $request)
    {
        $data = $request->all();

        $requestValidationResult = RequestValidator::validator($request->all(), [
            'opt' => 'required',
            'user_id' => 'required',
            'gz_user_id' => 'required',
            'busi_name' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        //判断用户是否存在
        $gz_user = UserManager::getById($data['gz_user_id']);
        //如果用户不存在
        if (!$gz_user) {
            return ApiResponse::makeResponse(false, "被关注用户不存在", ApiResponse::INNER_ERROR);
        }

        $opt = $data['opt'];
        //取消关注
        if ($opt == '0') {
            $con_arr = array(
                'fan_user_id' => $data['user_id'],
                'gz_user_id' => $data['gz_user_id'],
                'busi_name' => $data['busi_name']
            );
            $guanzhus = GuanZhuManager::getListByCon($con_arr, false);
            foreach ($guanzhus as $guanzhu) {
                $guanzhu->delete();
            }
            return ApiResponse::makeResponse(true, "取消关注成功", ApiResponse::SUCCESS_CODE);
        }
        //进行关注
        if ($opt == "1") {
            $con_arr = array(
                'fan_user_id' => $data['user_id'],
                'gz_user_id' => $data['gz_user_id'],
                'busi_name' => $data['busi_name']
            );
            $guanzhus = GuanZhuManager::getListByCon($con_arr, false);
            if ($guanzhus->count() == 0) {
                $guanzhu = new GuanZhu();
                $guanzhu = GuanZhuManager::setInfo($guanzhu, $con_arr);
                $guanzhu->save();
            }
            return ApiResponse::makeResponse(true, "关注成功", ApiResponse::SUCCESS_CODE);
        }
    }


    /*
     * a、b用户间是否有关注关系
     *
     * By TerryQi
     *
     * 2018-11-08
     *
     * A用户是否关注了B用户
     *
     */
    public function getRel(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',
            'gz_user_id' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        //业务名称
        $busi_name = null;
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        $con_arr = array(
            'fan_user_id' => $data['user_id'],
            'gz_user_id' => $data['gz_user_id'],
            'busi_name' => $busi_name
        );
        $guanzhu = GuanZhuManager::getListByCon($con_arr, false)->first();

        if ($guanzhu) {
            return ApiResponse::makeResponse(true, "存在关注关系", ApiResponse::SUCCESS_CODE);
        } else {
            return ApiResponse::makeResponse(false, "不存在关注关系", ApiResponse::SUCCESS_CODE);
        }
    }


    /*
     * 根据条件获取关注列表
     *
     * By TerryQi
     *
     * 2018-06-21
     */

    public function getListByCon(Request $request)
    {
        $data = $request->all();
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'busi_name' => 'required'
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }

        //配置条件
        $gz_user_id = null;
        $fan_user_id = null;
        $busi_name = $data['busi_name'];

        $level = "01";

        if (array_key_exists('gz_user_id', $data) && !Utils::isObjNull($data['gz_user_id'])) {
            $gz_user_id = $data['gz_user_id'];
        }
        if (array_key_exists('fan_user_id', $data) && !Utils::isObjNull($data['fan_user_id'])) {
            $fan_user_id = $data['fan_user_id'];
        }
        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = $data['level'];
        }
        $con_arr = array(
            'fan_user_id' => $fan_user_id,
            'gz_user_id' => $gz_user_id,
            'busi_name' => $busi_name
        );
        $guanzhus = GuanZhuManager::getListByCon($con_arr, false);
        foreach ($guanzhus as $guanzhu) {
            $guanzhu = GuanZhuManager::getInfoByLevel($guanzhu, $level);
        }
        return ApiResponse::makeResponse(true, $guanzhus, ApiResponse::SUCCESS_CODE);

    }


}





