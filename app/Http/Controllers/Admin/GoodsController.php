<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\GoodsManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\AD;
use App\Models\Goods;
use Illuminate\Http\Request;

class GoodsController
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
        $search_word = null;    //搜索条件
        $recomm_flag = null;
        $busi_name = null;      //业务归属
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        if (array_key_exists('recomm_flag', $data) && !Utils::isObjNull($data['recomm_flag'])) {
            $recomm_flag = $data['recomm_flag'];
        }

        $con_arr = array(
            'search_word' => $search_word,
            'recomm_flag' => $recomm_flag,
            'busi_name' => $busi_name
        );
        $goods = GoodsManager::getListByCon($con_arr, true);
        foreach ($goods as $good) {
            unset($good->content_html);
            $good = GoodsManager::getInfoByLevel($good, '04');
        }
        return view('admin.goods.index', ['admin' => $admin, 'datas' => $goods, 'con_arr' => $con_arr]);
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
        $good = GoodsManager::getById($data['id']);
        $good = GoodsManager::setInfo($good, $data);
        $good->save();
        return ApiResponse::makeResponse(true, $good, ApiResponse::SUCCESS_CODE);
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

        $goods = new Goods();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $goods = GoodsManager::getById($data['id']);
            $goods = GoodsManager::getInfoByLevel($goods, '0');
//            dd($goods);
        }
        $goods = GoodsManager::setInfo($goods, $data);

        //生成管理员信息（此处作者信息为）
        $con_arr = array(
            'type' => '1',
            'status' => '1',
        );
        $admin_users = UserManager::getListByCon($con_arr, false);

//        dd($admin_users);

        //生成管理员信息
        $con_arr = array(
            'status' => '1'
        );
        $admins = AdminManager::getListByCon($con_arr, false);
        return view('admin.goods.edit', ['admin' => $admin, 'data' => $goods
            , 'upload_token' => $upload_token, 'item' => $item, 'admin_users' => $admin_users, 'admins' => $admins]);
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

        $goods = new Goods();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $goods = GoodsManager::getById($data['id']);
        }
        $goods = GoodsManager::setInfo($goods, $data);
//        dd($goods);
        $goods->save();
        return ApiResponse::makeResponse(true, $goods, ApiResponse::SUCCESS_CODE);

    }
}





