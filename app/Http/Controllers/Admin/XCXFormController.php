<?php
/**
 * Created by PhpStorm.
 * User: mtt17
 * Date: 2018/4/20
 * Time: 10:50
 */

namespace App\Http\Controllers\Admin;


use App\Components\XCXFormManager;
use App\Components\QNManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\XCXForm;
use Illuminate\Http\Request;

class XCXFormController
{

    //首页
    public function index(Request $request)
    {
        $admin = $request->session()->get('admin');
        $data = $request->all();
        //相关搜素条件
        $busi_name = null;
        $user_id = null;
        $used_flag = null;
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        if (array_key_exists('used_flag', $data) && !Utils::isObjNull($data['used_flag'])) {
            $used_flag = $data['used_flag'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'busi_name' => $busi_name,
            'used_flag' => $used_flag,
            'user_id' => $user_id
        );
        $xcxFroms = XCXFormManager::getListByCon($con_arr, true);
        foreach ($xcxFroms as $xcxFrom) {
            $xcxFrom = XCXFormManager::getInfoByLevel($xcxFrom, '0');
        }

        return view('admin.xcxForm.index', ['admin' => $admin, 'datas' => $xcxFroms, 'con_arr' => $con_arr]);
    }

}











