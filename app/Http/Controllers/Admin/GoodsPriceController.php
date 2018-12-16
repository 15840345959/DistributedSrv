<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\GoodsPriceManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\AD;
use App\Models\GoodsPrice;
use Illuminate\Http\Request;

class GoodsPriceController
{

    /*
     * 首页
     *
     * By mtt
     *
     * 2018-4-9
     */
    public static function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
        //相关搜素条件
        $goodsPrices = GoodsPriceManager::getListByCon([], true);
        foreach ($goodsPrices as $goodsPrice) {
            $goodsPrice = GoodsPriceManager::getInfoByLevel($goodsPrice, '0');
        }
        return view('admin.goodsPrice.index', ['admin' => $admin, 'datas' => $goodsPrices, 'con_arr' => []]);
    }


    /*
     * 设置商品状态
     *
     * By mtt
     *
     * 2018-4-9
     */
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数轮播图id$id']);
        }
        $goodsPrice = GoodsPriceManager::getById($data['id']);
        $goodsPrice = GoodsPriceManager::setInfo($goodsPrice, $data);
        $goodsPrice->save();
        return ApiResponse::makeResponse(true, $goodsPrice, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 编辑商品信息
     *
     * By TerryQi
     *
     * 2018-09-23
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

        $goodsPrices = new GoodsPrice();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $goodsPrices = GoodsPriceManager::getById($data['id']);
            $goodsPrices = GoodsPriceManager::getInfoByLevel($goodsPrices, '0');
//            dd($goodsPrices);
        }
        $goodsPrices = GoodsPriceManager::setInfo($goodsPrices, $data);

        return view('admin.goodsPrice.edit', ['data' => $goodsPrices]);
    }


    /*
     * 添加、编辑商品信息
     *
     * By TerryQi
     *
     * 2018-09-23
     */
    public function editPost(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $goodsPrice = new GoodsPrice();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $goodsPrice = GoodsPriceManager::getById($data['id']);
        }
        $goodsPrice = GoodsPriceManager::setInfo($goodsPrice, $data);
        $goodsPrice->admin_id = $admin->id;
//        dd($goodsPrice);
        $goodsPrice->save();
        return ApiResponse::makeResponse(true, $goodsPrice, ApiResponse::SUCCESS_CODE);
    }
}





