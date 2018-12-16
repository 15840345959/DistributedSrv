<?php
/**
 * 首页控制器
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20 0020
 * Time: 20:15
 */

namespace App\Http\Controllers\Admin;

use App\Components\AdminManager;
use App\Components\FeedBackManager;
use App\Components\QNManager;
use App\Components\UserManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Redirect;


class FeedBackController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
        //       dd($data);
        $admin = $request->session()->get('admin');
        //相关搜素条件
        $user_id = null;
        $busi_name = null;
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        if (array_key_exists('busi_name', $data) && !Utils::isObjNull($data['busi_name'])) {
            $busi_name = $data['busi_name'];
        }
        $con_arr = array(
            'user_id' => $user_id,
            'busi_name' => $busi_name,
        );
        $feedbacks = FeedBackManager::getListByCon($con_arr, true);
        foreach ($feedbacks as $feedback) {
            $feedback = FeedBackManager::getInfoByLevel($feedback, '01');
        }
        return view('admin.feedback.index', ['datas' => $feedbacks, 'con_arr' => $con_arr]);
    }

}