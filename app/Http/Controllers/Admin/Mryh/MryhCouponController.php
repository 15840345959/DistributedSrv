<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Mryh;

use App\Components\AdminManager;
use App\Components\DateTool;
use App\Components\QNManager;
use App\Components\RequestValidator;
use App\Components\Utils;
use App\Components\Mryh\MryhCouponManager;

use App\Http\Controllers\ApiResponse;
use App\Models\Mryh\MryhCoupon;
use Illuminate\Http\Request;


class MryhCouponController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $search_word = null;
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        $con_arr = array(
            'search_word' => $search_word,
        );
        $mryhCoupons = MryhCouponManager::getListByCon($con_arr, true);
        foreach ($mryhCoupons as $mryhCoupon) {
            $mryhCoupon = MryhCouponManager::getInfoByLevel($mryhCoupon, '0');
        }
//        dd($mryhCoupons);
        return view('admin.mryh.mryhCoupon.index', ['datas' => $mryhCoupons, 'con_arr' => $con_arr]);
    }

    /*
     * 添加、编辑-get
     *
     * By TerryQi
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //设置项目 setting item为设置项目，按照顺序排下来
        $item = 0;
        if (array_key_exists('item', $data)) {
            $item = $data['item'];
        }
        //生成七牛token
        $upload_token = QNManager::uploadToken();
        $mryhCoupon = new MryhCoupon();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $mryhCoupon = MryhCouponManager::getById($data['id']);
//            dd($mryhCoupon);
        }
        $mryhCoupon = MryhCouponManager::setInfo($mryhCoupon, $data);
        return view('admin.mryh.mryhCoupon.edit', ['admin' => $admin, 'data' => $mryhCoupon
            , 'upload_token' => $upload_token, 'item' => $item]);
    }

    /*
     * 添加、编辑-post
     *
     * By TerryQi
     *
     * 2018-4-9
     *
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $mryhCoupon = new MryhCoupon();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $mryhCoupon = MryhCouponManager::getById($data['id']);
        } else {
            //如果是新建，应该设置创建者信息
            $mryhCoupon->admin_id = $admin->id;
        }
        $mryhCoupon = MryhCouponManager::setInfo($mryhCoupon, $data);
        $mryhCoupon->save();

        return ApiResponse::makeResponse(true, $mryhCoupon, ApiResponse::SUCCESS_CODE);
    }


    //设置状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数礼品id$id']);
        }
        //合规校验
        $requestValidationResult = RequestValidator::validator($request->all(), [
            'status' => 'required',
        ]);
        if ($requestValidationResult !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数' . $requestValidationResult]);
        }
        $mryhCoupon = MryhCouponManager::getById($id);
        //如果是启动服务，判断配置项目的合法性
        if ($data['status'] == '1') {
            if (Utils::isObjNull($mryhCoupon->name)) {
                return ApiResponse::makeResponse(false, "名称未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhCoupon->img)) {
                return ApiResponse::makeResponse(false, "首页封皮未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhCoupon->code)) {
                return ApiResponse::makeResponse(false, "优惠券编码未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhCoupon->intro_text)) {
                return ApiResponse::makeResponse(false, "优惠券简介未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhCoupon->intro_html)) {
                return ApiResponse::makeResponse(false, "优惠券借号信息未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhCoupon->con_date)) {
                return ApiResponse::makeResponse(false, "起算日期未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhCoupon->con_yq_num)) {
                return ApiResponse::makeResponse(false, "拉新目标未设置", ApiResponse::INNER_ERROR);
            }
            if (Utils::isObjNull($mryhCoupon->con_valid_days)) {
                return ApiResponse::makeResponse(false, "有效日期数未设置", ApiResponse::INNER_ERROR);
            }

        }

        //设置状态
        $mryhCoupon->status = $data['status'];
        $mryhCoupon->save();
        return ApiResponse::makeResponse(true, $mryhCoupon, ApiResponse::SUCCESS_CODE);
    }

}