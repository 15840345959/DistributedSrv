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
use App\Components\Vote\VoteRecordManager;
use App\Components\Utils;
use App\Http\Controllers\ApiResponse;
use App\Models\Admin;
use App\Models\Vote\VoteGift;
use Illuminate\Http\Request;
use App\Libs\ServerUtils;
use App\Components\RequestValidator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class VoteRecordController
{
    //首页
    public function index(Request $request)
    {
        $data = $request->all();
//        dd($data);
        $admin = $request->session()->get('admin');
        //搜索条件
        $vote_user_id = null;
        $user_id = null;
        if (array_key_exists('vote_user_id', $data) && !Utils::isObjNull($data['vote_user_id'])) {
            $vote_user_id = $data['vote_user_id'];
        }
        if (array_key_exists('user_id', $data) && !Utils::isObjNull($data['user_id'])) {
            $user_id = $data['user_id'];
        }
        $con_arr = array(
            'vote_user_id' => $vote_user_id,
            'user_id' => $user_id
        );
        $vote_records = VoteRecordManager::getListByCon($con_arr, true);
        foreach ($vote_records as $vote_record) {
            $vote_record = VoteRecordManager::getInfoByLevel($vote_record, '12');
        }
//        dd($vote_records);
        return view('admin.vote.voteRecord.index', ['datas' => $vote_records, 'con_arr' => $con_arr]);
    }

}