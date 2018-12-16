<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin\Vote;

use App\Components\AdminManager;
use App\Components\QNManager;
use App\Components\Vote\VoteComplainManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteGift;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class VoteComplainController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        $status = null;
        $search_word = null;
        if (array_key_exists('status', $data) && !Utils::isObjNull($data['status'])) {
            $status = $data['status'];
        }
        if (array_key_exists('search_word', $data) && !Utils::isObjNull($data['search_word'])) {
            $search_word = $data['search_word'];
        }
        $con_arr = array(
            'status' => $status,
            'search_word' => $search_word
        );
        $voteComplains = VoteComplainManager::getListByCon($con_arr, true);
        foreach ($voteComplains as $voteComplain) {
            $voteComplain = VoteComplainManager::getInfoByLevel($voteComplain, '012');
        }
//        dd($voteComplains);
        return view('admin.vote.voteComplain.index', ['datas' => $voteComplains, 'con_arr' => $con_arr]);
    }

    //设置投诉状态
    public function setStatus(Request $request, $id)
    {
        $data = $request->all();
        if (is_numeric($id) !== true) {
            return redirect()->action('\App\Http\Controllers\Admin\IndexController@error', ['msg' => '合规校验失败，请检查参数投诉id$id']);
        }
        $voteComplain = VoteComplainManager::getById($id);
        $voteComplain->status = $data['status'];
        $voteComplain->save();
        return ApiResponse::makeResponse(true, $voteComplain, ApiResponse::SUCCESS_CODE);
    }

}