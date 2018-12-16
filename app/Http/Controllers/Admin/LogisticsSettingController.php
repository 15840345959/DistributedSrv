<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/9
 * Time: 11:32
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\LogisticsSettingManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\AD;
use App\Models\LogisticsSetting;
use Illuminate\Http\Request;

class LogisticsSettingController
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
        $busi_name = null;
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        //相关搜素条件
        $con_arr = array(
            'busi_name' => $busi_name
        );
        $logisticsSettings = LogisticsSettingManager::getListByCon($con_arr, true);
        foreach ($logisticsSettings as $logisticsSetting) {
            $logisticsSetting = LogisticsSettingManager::getInfoByLevel($logisticsSetting, '0');
        }
        return view('admin.logisticsSetting.index', ['admin' => $admin, 'datas' => $logisticsSettings, 'con_arr' => $con_arr]);
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
        $logisticsSetting = LogisticsSettingManager::getById($data['id']);
        $logisticsSetting = LogisticsSettingManager::setInfo($logisticsSetting, $data);
        $logisticsSetting->save();
        return ApiResponse::makeResponse(true, $logisticsSetting, ApiResponse::SUCCESS_CODE);
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

        $logisticsSettings = new LogisticsSetting();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $logisticsSettings = LogisticsSettingManager::getById($data['id']);
            $logisticsSettings = LogisticsSettingManager::getInfoByLevel($logisticsSettings, '0');
//            dd($logisticsSettings);
        }
        $logisticsSettings = LogisticsSettingManager::setInfo($logisticsSettings, $data);

        return view('admin.logisticsSetting.edit', ['data' => $logisticsSettings]);
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
        $logisticsSetting = new LogisticsSetting();
        if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
            $logisticsSetting = LogisticsSettingManager::getById($data['id']);
        }
        $logisticsSetting = LogisticsSettingManager::setInfo($logisticsSetting, $data);
        $logisticsSetting->admin_id = $admin->id;
//        dd($logisticsSetting);
        $logisticsSetting->save();
        return ApiResponse::makeResponse(true, $logisticsSetting, ApiResponse::SUCCESS_CODE);
    }
}





