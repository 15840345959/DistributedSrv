<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\B;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\B\BMessageManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\B\Message;
use App\Models\Rule;
use function Couchbase\defaultDecoder;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class BMessageController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //配置条件

        $search_word = null;
        $busi_name = null;
        $level = null;

        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }

        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = array_get($data, 'busi_name');
        }

        if (array_key_exists('level', $data) && !Utils::isObjNull($data['level'])) {
            $level = array_get($data, 'level');
        }

        $con_arr = array(
            'search_word' => $search_word,
            'busi_name' => $busi_name,
            'level' => $level
        );

        $messages = BMessageManager::getListByCon($con_arr, true);
        foreach ($messages as $message) {
            unset($message->content_html);
            $message = BMessageManager::getInfoByLevel($message, '0');
        }

//        dd($con_arr);

        return view('admin.b.message.index', ['datas' => $messages, 'con_arr' => $con_arr]);
    }


    //设置规则状态
    public function setStatus(Request $request)
    {
        $data = $request->all();
        $id = array_get($data, 'id');
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数规则id$id']);
        }
        $message = BMessageManager::getById($id);
        $message->status = $data['status'];
        $message->save();
        return ApiResponse::makeResponse(true, $message, ApiResponse::SUCCESS_CODE);
    }


    /*
     * 添加、编辑投票规则
     *
     * By leek
     *
     * 2018-4-9
     */
    public function edit(Request $request)
    {
        $methods = $request->method();
        $data = $request->all();
        $admin = $request->session()->get('admin');
        $message = new Message();
        switch ($methods) {
            case 'GET':
                $upload_token = QNManager::uploadToken();
                if (array_key_exists('id', $data)) {
                    $message = BMessageManager::getById($data['id']);
                }
                $message = BMessageManager::setInfo($message, $data);
                return view('admin.b.message.edit', ['admin' => $admin, 'data' => $message, 'upload_token' => $upload_token]);
                break;
            case 'POST':
                if (array_key_exists('id', $data) && !Utils::isObjNull($data['id'])) {
                    $message = BMessageManager::getById($data['id']);
                }
                $message = BMessageManager::setInfo($message, $data);
                $message->admin_id = $admin->id;      //记录规则id
                $message->save();

                return ApiResponse::makeResponse(true, $message, ApiResponse::SUCCESS_CODE);
                break;
        }


    }
}