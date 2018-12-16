<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 13:29
 */

namespace App\Http\Controllers\API;


use App\Components\AddressManager;
use App\Components\RequestValidator;
use App\Components\ADManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController
{
    /*
     * 获取地址列表
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function getListByCon(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',            //用户id
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user_id = $data['user_id'];
        $con_arr = array(
            'user_id' => $user_id
        );
        $addresses = AddressManager::getListByCon($con_arr, false);

        return ApiResponse::makeResponse(true, $addresses, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 获取默认的地址，逻辑为有默认的地址则获取默认地址，如果没有默认地址返回最近的一个
     *
     * By TerryQi
     *
     * 2018-06-11
     */
    public function getDefault(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',            //用户id
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $user_id = $data['user_id'];
        //默认地址
        $con_arr = array(
            'user_id' => $user_id,
            'default_flag' => '1'
        );
        $addresses = AddressManager::getListByCon($con_arr, false);
        //如果存在默认地址
        if ($addresses->count() > 0) {
            return ApiResponse::makeResponse(true, $addresses->first(), ApiResponse::SUCCESS_CODE);
        }
        //非默认地址
        $con_arr = array(
            'user_id' => $user_id,
        );
        $addresses = AddressManager::getListByCon($con_arr, false);
        if ($addresses->count() > 0) {
            return ApiResponse::makeResponse(true, $addresses->first(), ApiResponse::SUCCESS_CODE);
        }

        return ApiResponse::makeResponse(false, "还没有地址", ApiResponse::INNER_ERROR);
    }

    /*
     * 添加地址
     *
     * By TerryQi
     *
     * 如果带id为修改、如果不带id为新增
     *
     * 2018-10-16
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'user_id' => 'required',            //用户id
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $address = new Address();
        //存在id
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $address = AddressManager::getById($data['id']);
        }
        $address = AddressManager::setInfo($address, $data);
        $address->save();

        return ApiResponse::makeResponse(true, "保存成功", ApiResponse::SUCCESS_CODE);
    }

    /*
     * 设置地址为默认地址
     *
     * By TerryQi
     *
     * 2018-10-16
     */
    public function setDefault(Request $request)
    {
        $data = $request->all();
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'id' => 'required',            //地址id
        ]);
        if ($requestValidationResult !== true) {
            return ApiResponse::makeResponse(false, $requestValidationResult, ApiResponse::MISSING_PARAM);
        }
        $address = AddressManager::getById($data['id']);
        if (!$address) {
            return ApiResponse::makeResponse(false, "未找到地址", ApiResponse::INNER_ERROR);
        }
        //将该用户的全部地址都设置为非默认地址
        $user_id = $address->user_id;
        Address::where('user_id', '=', $user_id)->update(['default_flag' => '0']);
        $address->default_flag = '1';       //当前address为默认地址
        $address->save();

        return ApiResponse::makeResponse(false, "设置成功", ApiResponse::INNER_ERROR);
    }

}





